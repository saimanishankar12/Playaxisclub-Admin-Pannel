<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerOtp;
use App\Mail\PlayerOtpMail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserAuthController extends Controller
{
    public function showLogin()
    {
        return view('user.login');
    }

    public function profile()
    {
        $player = Player::where('player_id', session('player_id'))->first();
        return view('user.profile', compact('player'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'player_id' => ['required', 'string'],
            'season_id' => ['required', 'string'],
            'email'     => ['required', 'email'],
        ], [
            'player_id.required' => 'Player ID is required.',
            'season_id.required' => 'Season ID is required.',
            'email.required'     => 'Email is required.',
            'email.email'        => 'Enter a valid email address.',
        ]);

        $player = Player::where('player_id', trim($request->player_id))
                        ->where('season_id',  trim($request->season_id))
                        ->where('email',      strtolower(trim($request->email)))
                        ->first();

        if (!$player) {
            return back()->withInput()
                ->with('error', 'No player found with those details. Please check your Player ID, Season ID and Email.');
        }

        $payment = Payment::where('season_id', $player->season_id)
                          ->where('status', 'paid')
                          ->first();

        if (!$payment) {
            return back()->withInput()
                ->with('error', 'Your payment is not completed. Please complete your registration payment to login.');
        }

        // Generate OTP and store in DB
        $otp = rand(100000, 999999);

        PlayerOtp::where('player_id', $player->player_id)->delete();

        $record = PlayerOtp::create([  // ← was missing $record =
            'player_id'  => $player->player_id,
            'otp'        => (string) $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        Log::info('OTP saved', ['record_id' => $record->id, 'otp' => $otp]);  // ← moved here, after $record is defined

        try {
            Mail::to($player->email)->send(new PlayerOtpMail($player->name, (string) $otp));
            Log::info('OTP mail sent to ' . $player->email);
        } catch (\Exception $e) {
            Log::error('OTP mail failed: ' . $e->getMessage());
        }

        session(['otp_player_id' => $player->player_id]);

        return redirect()->route('user-login.otp')
            ->with('success', 'OTP sent to your registered email address.');
    }

    public function showOtp()
    {
        if (!session('otp_player_id')) {
            return redirect()->route('user-login')
                ->with('error', 'Please login first.');
        }
        return view('user.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Please enter the OTP.',
            'otp.digits'   => 'OTP must be 6 digits.',
        ]);

        $playerId = session('otp_player_id');

        if (!$playerId) {
            return redirect()->route('user-login')
                ->with('error', 'Session expired. Please login again.');
        }

        $otpRecord = PlayerOtp::where('player_id', $playerId)->latest()->first();

        // OTP not found or expired
        if (!$otpRecord || now()->isAfter($otpRecord->expires_at)) {
            optional($otpRecord)->delete();
            return back()->with('error', 'OTP has expired. Please go back and login again.');
        }

        // OTP mismatch
        if ($otpRecord->otp !== (string) $request->otp) {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        // OTP matched — clean up and create session
        $otpRecord->delete();
        session()->forget('otp_player_id');

        $player = Player::where('player_id', $playerId)->first();

        session([
            'player_id'    => $player->player_id,
            'player_name'  => $player->name,
            'player_db_id' => $player->id,
        ]);

        return redirect()->route('user-dashboard')
            ->with('success', 'Welcome, ' . $player->name . '!');
    }

    public function logout()
    {
        session()->forget(['player_id', 'player_name', 'player_db_id']);
        return redirect()->route('user-login')
            ->with('success', 'You have been logged out.');
    }
}