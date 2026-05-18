<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleRazorpay(Request $request)
    {
        // ── Step 1: Verify webhook signature ─────────────────────────────────
        $webhookSecret = config('services.razorpay.webhook_secret');
        $signature     = $request->header('X-Razorpay-Signature');
        $payload       = $request->getContent();

        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        if (!hash_equals($expectedSignature, $signature ?? '')) {
            Log::warning('Razorpay Webhook: Invalid signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // ── Step 2: Parse event ───────────────────────────────────────────────
        $event     = $request->input('event');
        $entity    = $request->input('payload.payment.entity');
        $orderId   = $entity['order_id'] ?? null;
        $paymentId = $entity['id']       ?? null;

        Log::info("Razorpay Webhook received", ['event' => $event, 'order_id' => $orderId]);

        // ── Step 3: Handle events ─────────────────────────────────────────────
        if ($event === 'payment.captured') {
            $this->handlePaymentCaptured($orderId, $paymentId);
        }

        if ($event === 'payment.failed') {
            $this->handlePaymentFailed($orderId);
        }

        // Always return 200 so Razorpay doesn't retry
        return response()->json(['status' => 'ok'], 200);
    }

    // ── payment.captured ──────────────────────────────────────────────────────
    private function handlePaymentCaptured(string $orderId, string $paymentId): void
    {
        DB::transaction(function () use ($orderId, $paymentId) {
            $payment = Payment::where('razorpay_order_id', $orderId)
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                Log::warning("Webhook: Payment not found", ['order_id' => $orderId]);
                return;
            }

            // Already paid via frontend verify — skip
            if ($payment->status === 'paid') {
                Log::info("Webhook: Already paid, skipping", ['order_id' => $orderId]);
                return;
            }

            // ── Mark payment as paid ──────────────────────────────────────────
            $payment->update([
                'razorpay_payment_id' => $paymentId,
                'status'              => 'paid',
            ]);

            // ── Sync payment_status on players table ──────────────────────────
            app(PaymentController::class)->syncPlayerPaymentStatus($payment, 'paid');

            Log::info("Webhook: Payment marked as paid", ['order_id' => $orderId]);

            // ── Send thank-you SMS (fallback if frontend missed it) ───────────
            app(PaymentController::class)->sendSuccessSms($payment);
        });
    }

    // ── payment.failed ────────────────────────────────────────────────────────
    private function handlePaymentFailed(string $orderId): void
    {
        $payment = Payment::where('razorpay_order_id', $orderId)->first();

        if (!$payment) {
            Log::warning("Webhook: Payment not found for failed order", ['order_id' => $orderId]);
            return;
        }

        if ($payment->status !== 'paid') {
            $payment->update(['status' => 'failed']);

            // ── Sync failed status to players ─────────────────────────────────
            app(PaymentController::class)->syncPlayerPaymentStatus($payment, 'failed');

            Log::info("Webhook: Payment marked as failed", ['order_id' => $orderId]);
        }
    }
}