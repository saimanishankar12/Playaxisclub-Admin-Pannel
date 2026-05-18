<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\State;
use App\Models\City;
use App\Http\Controllers\TournamentController;
 use App\Http\Controllers\WebhookController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//STATES DROP DOWN
Route::get('/states', function () {
    return State::orderBy('name')->get(['id', 'name']);
});
//BASED ON STATE CITIES DROP DOWN
Route::get('/cities', function () {
    $stateId = request('state_id');
    if (!$stateId) return response()->json([]);
    return City::where('state_id', $stateId)->orderBy('name')->get(['id', 'name']);
});
//REGISTERATION  FOR SINGLES/DOUBLES
Route::post('/register/singles', [TournamentController::class, 'registerSingles']);
Route::post('/register/doubles', [TournamentController::class, 'registerDoubles']);
//OTP FOR SENDING/VERIFY THE MOBILE NUMBER
Route::post('/send-otp',   [OtpController::class, 'send']);
Route::post('/verify-otp', [OtpController::class, 'verify']);
//RAZORPAY FOR PAYMENT
Route::post('/payment/create-order', [PaymentController::class, 'createOrder']);
Route::post('/payment/verify',       [PaymentController::class, 'verifyPayment']);
//WEBHOOK 
// ── Razorpay Webhook (no auth, no CSRF) ──────────────────────────────────────
Route::post('/payment/webhook', [WebhookController::class, 'handleRazorpay']);
//CONTACTUS FORM
Route::post('/contact', [ContactController::class, 'store']);








//*************************************************************************************************************************** */
Route::get('/admin-register', [AdminController::class, 'getAdminRegister']);