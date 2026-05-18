<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\OtpService;

class OtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    // POST /api/send-otp
    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
       
        ]);

        $success = $this->otpService->send($request->phone);

        if ($success) {
            return response()->json(['message' => 'OTP sent successfully'], 200);
        }

        return response()->json(['message' => 'Failed to send OTP. Please try again.'], 500);
    }

    // POST /api/verify-otp
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp'   => 'required|digits:6',
        ]);

        $verified = $this->otpService->verify($request->phone, $request->otp);

        if ($verified) {
            return response()->json(['verified' => true], 200);
        }

        return response()->json([
            'verified' => false,
            'message'  => 'Invalid or expired OTP.',
        ], 422);
    }
}