<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminOtpMail;
use App\Models\Admin;        
use App\Models\AdminOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminForgotPasswordController extends Controller
{
    // ─────────────────────────────────────────────
    // STEP 1 – Forgot Password (show form)
    // ─────────────────────────────────────────────

    public function showForgotPasswordForm(): View
    {
        return view('Admin.auth.forgot-password');
    }

    // STEP 1 – Forgot Password (handle submit)
    public function sendOtp(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = strtolower(trim($request->email));

        // Check admin existence
        $admin = Admin::where('email', $email)->first();

        if (! $admin) {
            return redirect()->back()
                ->withErrors(['email' => 'Email not found. No admin account is registered with this email.'])
                ->withInput();
        }

        // Invalidate any previous unused OTPs for this email
        AdminOtp::where('email', $email)->where('used', false)->delete();

        // Generate 6-digit OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Save OTP with 5-minute expiry
        AdminOtp::create([
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(5),
            'used'       => false,
        ]);

        // Send OTP mail
        // Mail::to($email)->send(new AdminOtpMail($otp));
        Mail::to($email)->send(new AdminOtpMail($otp, $admin->first_name));

        // Store email in session for the next steps
        session(['otp_email' => $email]);

        return redirect()->route('admin-verify-otp.form')
            ->with('success', 'An OTP has been sent to your email address. It expires in 5 minutes.');
    }

    // ─────────────────────────────────────────────
    // STEP 2 – Verify OTP (show form)
    // ─────────────────────────────────────────────

    public function showVerifyOtpForm(): View|RedirectResponse
    {
        if (! session('otp_email')) {
            return redirect()->route('admin-forgot-password.form')
                ->withErrors(['email' => 'Session expired. Please start over.']);
        }

        return view('admin.auth.verify-otp', [
            'email' => session('otp_email'),
        ]);
    }

    // STEP 2 – Verify OTP (handle submit)
    public function verifyOtp(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'digits:6'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('Admin.forgot-password.form')
                ->withErrors(['email' => 'Session expired. Please start over.']);
        }

        // Fetch the latest unused OTP record for this email
        $otpRecord = AdminOtp::where('email', $email)
            ->where('used', false)
            ->latest()
            ->first();

        if (! $otpRecord) {
            return redirect()->back()
                ->withErrors(['otp' => 'No OTP found. Please request a new one.']);
        }

        if ($otpRecord->isExpired()) {
            return redirect()->back()
                ->withErrors(['otp' => 'Your OTP has expired. Please request a new one.']);
        }

        if ($otpRecord->otp !== $request->otp) {
            return redirect()->back()
                ->withErrors(['otp' => 'Invalid OTP. Please check and try again.'])
                ->withInput();
        }

        // Mark OTP as used and store verified state
        $otpRecord->update(['used' => true]);
        session(['otp_verified' => true]);

        return redirect()->route('admin-change-password.form')
            ->with('success', 'OTP verified successfully. Please set a new password.');
    }

    // ─────────────────────────────────────────────
    // STEP 3 – Change Password (show form)
    // ─────────────────────────────────────────────

    public function showChangePasswordForm(): View|RedirectResponse
    {
        if (! session('otp_email') || ! session('otp_verified')) {
            return redirect()->route('admin.forgot-password.form')
                ->withErrors(['email' => 'Unauthorized access. Please start over.']);
        }

        return view('Admin.auth.change-password');
    }

    // STEP 3 – Change Password (handle submit)
    public function changePassword(Request $request): RedirectResponse
    {
        if (! session('otp_email') || ! session('otp_verified')) {
            return redirect()->route('admin.forgot-password.form')
                ->withErrors(['email' => 'Session expired. Please start over.']);
        }

        $validator = Validator::make($request->all(), [
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = session('otp_email');

        $admin = Admin::where('email', $email)->first();

        if (! $admin) {
            return redirect()->route('admin.forgot-password.form')
                ->withErrors(['email' => 'Admin account not found.']);
        }

        // Update password
        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        // Clean up session
        session()->forget(['otp_email', 'otp_verified']);

        return redirect()->route('admin-login')
            ->with('success', 'Password changed successfully. Please log in with your new password.');
    }

    // ─────────────────────────────────────────────
    // UTILITY – Resend OTP
    // ─────────────────────────────────────────────

    public function resendOtp(): RedirectResponse
    {
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('admin.forgot-password.form')
                ->withErrors(['email' => 'Session expired. Please start over.']);
        }

        $admin = Admin::where('email', $email)->first();

        if (! $admin) {
            return redirect()->route('admin.forgot-password.form')
                ->withErrors(['email' => 'Admin not found.']);
        }

        // Invalidate old OTPs
        AdminOtp::where('email', $email)->where('used', false)->delete();

        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        AdminOtp::create([
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(5),
            'used'       => false,
        ]);

        // Mail::to($email)->send(new AdminOtpMail($otp));
        Mail::to($email)->send(new AdminOtpMail($otp, $admin->first_name));

        return redirect()->route('admin-verify-otp.form')
            ->with('success', 'A new OTP has been sent to your email address.');
    }
}