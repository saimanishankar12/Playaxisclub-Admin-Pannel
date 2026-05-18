<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private function smsConfig(): array
    {
        return [
            'username'  => config('services.smsstriker.username'),
            'password'  => config('services.smsstriker.password'),
            'sender_id' => config('services.smsstriker.sender_id'),
        ];
    }

    private function sendSms(string $phone, string $message, string $templateId, string $logTag): bool
    {
        ['username' => $username, 'password' => $password, 'sender_id' => $senderId] = $this->smsConfig();

        // ── Log all config values to help debug missing keys ─────────────────
        Log::info("SMSStriker [{$logTag}] Config check", [
            'username'    => $username    ? '✅ set' : '❌ MISSING',
            'password'    => $password    ? '✅ set' : '❌ MISSING',
            'sender_id'   => $senderId    ? $senderId : '❌ MISSING',
            'template_id' => $templateId  ? $templateId : '❌ MISSING',
            'phone'       => "91{$phone}",
        ]);

        if (!$username || !$password || !$templateId) {
            Log::error("SMSStriker [{$logTag}] Aborting — missing config", [
                'username'    => $username    ? '***' : null,
                'sender_id'   => $senderId,
                'template_id' => $templateId,
            ]);
            return false;
        }

        try {
            $response = Http::get('https://www.smsstriker.com/API/sms.php', [
                'username'    => $username,
                'password'    => $password,
                'from'        => $senderId,
                'to'          => "91{$phone}",
                'msg'         => $message,
                'type'        => 1,
                'template_id' => $templateId,
            ]);

            $body = $response->body();

            Log::info("SMSStriker [{$logTag}] Response", [
                'http_status' => $response->status(),
                'body'        => $body,
            ]);

            // ── Accept both success formats from SMSStriker ──────────────────
            $success = str_contains($body, 'Job Id:') || str_contains($body, 'jobid');

            if (!$success) {
                Log::warning("SMSStriker [{$logTag}] Unexpected response body", ['body' => $body]);
            }

            return $success;

        } catch (\Exception $e) {
            Log::error("SMSStriker [{$logTag}] Exception", ['error' => $e->getMessage()]);
            return false;
        }
    }

    // ── OTP ──────────────────────────────────────────────────────────────────

    public function send(string $phone): bool
    {
        $otp = app()->environment('local') ? 123456 : rand(100000, 999999);

        Cache::put("otp_{$phone}", $otp, now()->addMinutes(5));

        // ── Fixed: message now says PlayAxisClub ─────────────────────────────
        $message    = "Hi {$phone}, your OTP for registration at Play Axis Club is {$otp}. It is valid for 5 minutes. Do not share it with anyone.-LEADTK";
        $templateId = config('services.smsstriker.template_id_otp');

        $sent = $this->sendSms($phone, $message, $templateId, 'OTP');

        if (!$sent) {
            // ── Clean up cached OTP if SMS failed ────────────────────────────
            Cache::forget("otp_{$phone}");
        }

        return $sent;
    }

    // ── Thank-you SMS after payment ──────────────────────────────────────────

    public function sendSuccessMsg(string $phone, string $name): bool
    {
        $message    = "Hi {$name}, thanks for registering with Play Axis Club.-LEADTK";
        $templateId = config('services.smsstriker.template_id_successmsg');

        return $this->sendSms($phone, $message, $templateId, 'SuccessMsg');
    }

    // ── Verify ───────────────────────────────────────────────────────────────

    public function verify(string $phone, string $otp): bool
    {
        $stored = Cache::get("otp_{$phone}");

        if ($stored && (string) $stored === (string) $otp) {
            Cache::forget("otp_{$phone}");
            return true;
        }

        return false;
    }
}