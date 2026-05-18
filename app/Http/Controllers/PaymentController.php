<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TournamentController;
use App\Mail\RegistrationConfirmationMail;
use App\Models\Payment;
use App\Models\Player;
use App\Services\GoogleSheetService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );
    }

    // POST /api/payment/create-order
    public function createOrder(Request $request)
    {
        $request->validate([
            'amount'            => 'required|numeric|min:1',
            'season_id'         => 'required|string',
            'player1_id'        => 'required|string',
            'player2_id'        => 'nullable|string',
            'registration_type' => 'required|in:single,double',
        ]);

        $alreadyPaid = Payment::where('player1_id', $request->player1_id)
            ->where('status', 'paid')
            ->first();

        if ($alreadyPaid) {
            return response()->json(['message' => 'Payment already completed for this player.'], 409);
        }

        Payment::where('player1_id', $request->player1_id)
            ->whereIn('status', ['created', 'failed'])
            ->delete();

        $order = $this->api->order->create([
            'amount'   => $request->amount * 100,
            'currency' => 'INR',
            'receipt'  => 'rcpt_' . uniqid(),
        ]);

        Payment::create([
            'razorpay_order_id' => $order['id'],
            'amount'            => $request->amount,
            'currency'          => 'INR',
            'status'            => 'created',
            'season_id'         => $request->season_id,
            'player1_id'        => $request->player1_id,
            'player2_id'        => $request->player2_id,
            'registration_type' => $request->registration_type,
        ]);

        return response()->json([
            'order_id' => $order['id'],
            'amount'   => $request->amount * 100,
            'currency' => 'INR',
            'key_id'   => config('services.razorpay.key_id'),
        ]);
    }

    // POST /api/payment/verify
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $payment       = null;
        $verifySuccess = false;

        try {
            DB::transaction(function () use ($request, &$payment, &$verifySuccess) {

                $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($payment->status === 'paid') {
                    $verifySuccess = true;
                    return;
                }

                $this->api->utility->verifyPaymentSignature([
                    'razorpay_order_id'   => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature'  => $request->razorpay_signature,
                ]);

                $payment->update([
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature'  => $request->razorpay_signature,
                    'status'              => 'paid',
                ]);

                $this->syncPlayerPaymentStatus($payment, 'paid');
                $verifySuccess = true;
            });

        } catch (\Exception $e) {
            Log::error('Razorpay verification failed', [
                'message'    => $e->getMessage(),
                'order_id'   => $request->razorpay_order_id,
                'payment_id' => $request->razorpay_payment_id,
            ]);

            if ($payment) {
                try {
                    Payment::where('razorpay_order_id', $request->razorpay_order_id)
                        ->update(['status' => 'failed']);
                    $this->syncPlayerPaymentStatus($payment, 'failed');
                } catch (\Exception $inner) {
                    Log::error('Failed to update payment status to failed', [
                        'message' => $inner->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Contact support.',
            ], 400);
        }

        if ($verifySuccess && $payment) {
            $payment->refresh();

            // ── Append to Paid Google Sheet ────────────────────────────────────
            try {
                $this->appendPaidSheet($payment);
            } catch (\Exception $e) {
                Log::error('Google Sheet Error (Paid)', [
                    'message'  => $e->getMessage(),
                    'order_id' => $payment->razorpay_order_id,
                ]);
            }

            // ── SMS ────────────────────────────────────────────────────────────
            try {
                $this->sendSuccessSms($payment);
            } catch (\Exception $e) {
                Log::error('SMS sending failed after payment', [
                    'message'  => $e->getMessage(),
                    'order_id' => $payment->razorpay_order_id,
                ]);
            }

            // ── Email ──────────────────────────────────────────────────────────
            try {
                $this->sendConfirmationEmail($payment);
            } catch (\Exception $e) {
                Log::error('Email sending failed after payment', [
                    'message'  => $e->getMessage(),
                    'order_id' => $payment->razorpay_order_id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'payment' => [
                'razorpay_payment_id' => $payment->razorpay_payment_id,
                'razorpay_order_id'   => $payment->razorpay_order_id,
                'player1_id'          => $payment->player1_id,
                'player2_id'          => $payment->player2_id,
                'season_id'           => $payment->season_id,
                'amount'              => (int) $payment->amount,
                'status'              => $payment->status,
            ],
        ]);
    }

    // ── Sync payment_status on players table ──────────────────────────────────
    public function syncPlayerPaymentStatus(Payment $payment, string $status): void
    {
        DB::table('players')
            ->where('player_id', $payment->player1_id)
            ->update(['payment_status' => $status]);

        if ($payment->player2_id) {
            DB::table('players')
                ->where('player_id', $payment->player2_id)
                ->update(['payment_status' => $status]);
        }
    }

    // ── Append to Paid-Singles or Paid-Doubles Google Sheet ───────────────────
    private function appendPaidSheet(Payment $payment): void
    {
        $sheetService   = app(GoogleSheetService::class);
        $tournamentCtrl = app(TournamentController::class);

        $p1 = Player::where('player_id', $payment->player1_id)->first();

        if (!$p1) {
            Log::warning('appendPaidSheet: player1 not found', ['player1_id' => $payment->player1_id]);
            return;
        }

        $p1State = DB::table('states')->where('id', $p1->state_id)->value('name');

        if ($payment->registration_type === 'single') {

            $row = $tournamentCtrl->buildPaidSinglesRow(
                $p1,
                $p1State,
                $payment->razorpay_payment_id,
                (float) $payment->amount
            );

            $sheetService->append($row, TournamentController::SHEET_PAID_SINGLES);

        } elseif ($payment->registration_type === 'double') {

            $p2 = Player::where('player_id', $payment->player2_id)->first();

            if (!$p2) {
                Log::warning('appendPaidSheet: player2 not found', ['player2_id' => $payment->player2_id]);
                return;
            }

            $p2State = DB::table('states')->where('id', $p2->state_id)->value('name');

            $row = $tournamentCtrl->buildPaidDoublesRow(
                $p1,
                $p1State,
                $p2,
                $p2State,
                $payment->razorpay_payment_id,
                (float) $payment->amount
            );

            $sheetService->append($row, TournamentController::SHEET_PAID_DOUBLES);
        }
    }

    // ── SMS ───────────────────────────────────────────────────────────────────
    public function sendSuccessSms(Payment $payment): void
    {
        $otpService = app(OtpService::class);

        if ($payment->registration_type === 'single') {
            $player = DB::table('players')->where('player_id', $payment->player1_id)->first();
            if ($player) {
                $otpService->sendSuccessMsg($player->phone, $player->name);
            }
        } elseif ($payment->registration_type === 'double') {
            $player1 = DB::table('players')->where('player_id', $payment->player1_id)->first();
            $player2 = DB::table('players')->where('player_id', $payment->player2_id)->first();
            if ($player1) $otpService->sendSuccessMsg($player1->phone, $player1->name);
            if ($player2) $otpService->sendSuccessMsg($player2->phone, $player2->name);
        }
    }

    // ── Confirmation Email ────────────────────────────────────────────────────
    public function sendConfirmationEmail(Payment $payment): void
    {
        $paidAt = $payment->updated_at
            ? \Carbon\Carbon::parse($payment->updated_at)->format('d M Y, h:i A')
            : now()->format('d M Y, h:i A');

        if ($payment->registration_type === 'single') {

            $player = DB::table('players')
                ->where('player_id', $payment->player1_id)
                ->first();

            if (!$player || empty($player->email)) return;

            Mail::to($player->email)->send(new RegistrationConfirmationMail([
                'type'                => 'singles',
                'player_name'         => $player->name,
                'player_id'           => $player->player_id,
                'season_id'           => $payment->season_id,
                'amount'              => (int) $payment->amount,
                'razorpay_payment_id' => $payment->razorpay_payment_id,
                'razorpay_order_id'   => $payment->razorpay_order_id,
                'payment_date'        => $paidAt,
            ]));

        } elseif ($payment->registration_type === 'double') {

            $player1 = DB::table('players')->where('player_id', $payment->player1_id)->first();
            $player2 = DB::table('players')->where('player_id', $payment->player2_id)->first();

            if (!$player1 || empty($player1->email)) return;

            $baseDetails = [
                'type'                => 'doubles',
                'player1_id'          => $player1->player_id,
                'player2_id'          => $player2->player_id ?? 'N/A',
                'season_id'           => $payment->season_id,
                'amount'              => (int) $payment->amount,
                'razorpay_payment_id' => $payment->razorpay_payment_id,
                'razorpay_order_id'   => $payment->razorpay_order_id,
                'payment_date'        => $paidAt,
            ];

            Mail::to($player1->email)->send(new RegistrationConfirmationMail(
                array_merge($baseDetails, ['player_name' => $player1->name])
            ));

            if ($player2 && !empty($player2->email) && $player2->email !== $player1->email) {
                Mail::to($player2->email)->send(new RegistrationConfirmationMail(
                    array_merge($baseDetails, [
                        'player_name' => $player2->name,
                        'player1_id'  => $player2->player_id,
                    ])
                ));
            }
        }
    }
}