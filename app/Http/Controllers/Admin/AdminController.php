<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function getAdminRegister()
    {
        return view('Admin.register');
    }

    public function adminRegisterSubmit(Request $request)
    {
        
    $request->validate([
    'first_name' => 'required|string|max:255',
    'last_name'  => 'required|string|max:255',
    'email'      => 'required|email|unique:admin,email',
    'password'   => 'required|min:6|confirmed',
], [
    'first_name.required' => 'First name is required.',
    'first_name.string'   => 'First name must be a valid string.',
    'first_name.max'      => 'First name cannot exceed 255 characters.',

    'last_name.required' => 'Last name is required.',
    'last_name.string'   => 'Last name must be a valid string.',
    'last_name.max'      => 'Last name cannot exceed 255 characters.',

    'email.required' => 'Email is required.',
    'email.email'    => 'Please enter a valid email address.',
    'email.unique'   => 'This email is already registered.',

    'password.required'  => 'Password is required.',
    'password.min'       => 'Password must be at least 6 characters.',
    'password.confirmed' => 'Password confirmation does not match.',
]);

        Admin::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        return redirect()->route('admin-login')
            ->with('success', 'Registration successful! Please login.');
    }

    public function getAdminLogin()
    {
        return view('Admin.login');
    }

    public function chekcAdminLogin(Request $request)
    {
        // 1. Validate
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Check admin
       $admin = Admin::where('email', $request->email)->first();

if (!$admin) {
    return back()->with('error', 'No account found with this email address.');
}

if (!Hash::check($request->password, $admin->password)) {
    return back()->with('error', 'Incorrect password. Please try again.');
}

session(['admin_id' => $admin->id]);
return redirect()->route('admin-dashboard');

        return back()->with('error', 'Invalid email or password');
    }

    public function getAdminDashboard()
    {
        // Protect dashboard
        if (!session()->has('admin_id')) {
            return redirect()->route('admin-login');
        }

        return view('Admin.dashboard');
    }

    public function getAdminForgotPassword()
    {
        return view('Admin.forgot-password');
    }

    public function logout(Request $request)
{
    $request->session()->flush();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('admin-login')
        ->with('success', 'Logged out successfully.');
}
}