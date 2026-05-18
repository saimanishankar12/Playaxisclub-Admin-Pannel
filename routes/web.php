<?php
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\PlayaxisclubUserController;
use App\Http\Controllers\Admin\EkalavyaBadmintonTournamentS1Controller;
use App\Http\Controllers\Admin\PlayaxisclubTotalRevenueController;
use App\Http\Controllers\Admin\AdminForgotPasswordController;
use App\Http\Controllers\Admin\AdminUserReportController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\AudienceController;
use App\Http\Controllers\Admin\AudienceRegistrationController;
use App\Http\Controllers\Admin\MatchManagerController;
use App\Http\Controllers\User\UserAuthController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Admin\MatchResultsController;
use Illuminate\Support\Facades\Route;   
use Illuminate\Support\Facades\Mail;



     // Ekalavya Tournaments
Route::get('/audience-register', [AudienceRegistrationController::class, 'show'])->name('audience.register');
Route::post('/audience-register', [AudienceRegistrationController::class, 'store'])->name('audience.register.store');

Route::prefix('admin')->name('admin-')->group(function () {

    Route::get('/register',        [AdminController::class, 'getAdminRegister'])->name('register');
    Route::post('/register',       [AdminController::class, 'adminRegisterSubmit'])->name('register.submit');
    Route::get('/login',           [AdminController::class, 'getAdminLogin'])->name('login');
    Route::post('/login',          [AdminController::class, 'chekcAdminLogin'])->name('login.submit');
    Route::post('/logout',         [AdminController::class, 'logout'])->name('logout');
    // Route::get('/forgot-password', [AdminController::class, 'getAdminForgotPassword'])->name('forgot-password');
    Route::get('forgot-password', [AdminForgotPasswordController::class, 'showForgotPasswordForm'])
        ->name('forgot-password.form');

    Route::post('forgot-password', [AdminForgotPasswordController::class, 'sendOtp'])
        ->name('forgot-password.send');

    // ── Step 2: Verify OTP ───────────────────────────────────────────────
    Route::get('verify-otp', [AdminForgotPasswordController::class, 'showVerifyOtpForm'])
        ->name('verify-otp.form');

    Route::post('verify-otp', [AdminForgotPasswordController::class, 'verifyOtp'])
        ->name('verify-otp.submit');

    // Resend OTP helper
    Route::get('resend-otp', [AdminForgotPasswordController::class, 'resendOtp'])
        ->name('resend-otp');

    // ── Step 3: Change Password ──────────────────────────────────────────
    Route::get('change-password', [AdminForgotPasswordController::class, 'showChangePasswordForm'])
        ->name('change-password.form');

    Route::post('change-password', [AdminForgotPasswordController::class, 'changePassword'])
        ->name('change-password.submit');

    


    Route::middleware(['admin.auth', 'no.cache'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/seasons', [DashboardController::class, 'storeSeason'])->name('seasons-store');
        Route::patch('/seasons/{season}/status', [DashboardController::class, 'updateSeasonStatus'])->name('seasons-updateStatus');

        Route::get('/live-scores', [DashboardController::class, 'liveScores'])->name('dashboard.live-scores');
        

        // Ekalavya Tournaments
        Route::get('/ekalavya/singles', [PlayerController::class, 'singles'])->name('pac-singles');
        Route::get('/ekalavya/doubles', [PlayerController::class, 'doubles'])->name('pac-doubles');

        // Match
        // Route::get('/play', [PlayerController::class, 'play'])->name('get-play');

        // Revenue
        Route::get('/revenue', [PlayaxisclubTotalRevenueController::class, 'index'])->name('revenue');
        Route::get('/revenue/tournament/{tournament}', [PlayaxisclubTotalRevenueController::class, 'tournament'])->name('revenue-tournament');
        Route::get('/revenue/tournament/{tournament}/season/{season}', [PlayaxisclubTotalRevenueController::class, 'season'])->name('revenue-season');

        // Users Report
        Route::get('/users-report/paid',    fn() => view('Admin.reports.paid'))->name('users-report-paid');
        Route::get('/users-report/unpaid',  fn() => view('Admin.reports.unpaid'))->name('users-report-unpaid');
        Route::get('/users-report/results', fn() => view('Admin.reports.results'))->name('users-report-results');

        Route::get('/attendance',                [AttendanceController::class, 'index'])  ->name('attendance.index');
        Route::get('/attendance/{mode}',         [AttendanceController::class, 'ages'])   ->name('attendance.ages');
        Route::get('/attendance/{mode}/{age}',   [AttendanceController::class, 'players'])->name('attendance.players');
        Route::post('/attendance/mark',          [AttendanceController::class, 'mark'])   ->name('attendance.mark');




        // Match Results — 4-level hierarchy
   Route::get('/match-results', [MatchResultsController::class, 'index'])
    ->name('match-results');

Route::get('/match-results/{tournament}/types', [MatchResultsController::class, 'matchTypes'])
    ->name('match-results.match-types');

Route::get('/match-results/{tournament}/types/{matchType}/ages', [MatchResultsController::class, 'ageCategories'])
    ->name('match-results.age-categories');

Route::get('/match-results/{tournament}/types/{matchType}/ages/{ageCategory}', [MatchResultsController::class, 'results'])
    ->name('match-results.results');

        // Sponsors
        Route::get('/sponsors',              [SponsorController::class, 'index'])->name('sponsors');
        Route::post('/sponsors',             [SponsorController::class, 'store'])->name('sponsors-store');
        Route::put('/sponsors/{sponsor}', [SponsorController::class, 'update'])->name('admin-sponsors-update');
        Route::delete('/sponsors/{sponsor}', [SponsorController::class, 'destroy'])->name('sponsors-destroy');  

        // Audience 
        Route::get('/audience',               [AudienceController::class, 'index'])->name('audience');
        Route::post('/audience',              [AudienceController::class, 'store'])->name('audience-store');

        Route::post('/audience/declare-winner', [AudienceController::class, 'declareWinner'])->name('audience.declare-winner');
      

        
        // Route::delete('/audience/{audience}', [AudienceController::class, 'destroy'])->name('audience-destroy');

    });
});
// ── User Reports Module ─────────────────────────────────────────────────────
Route::prefix('admin/users')->name('admin-users.')->middleware(['admin.auth', 'no.cache'])->group(function () {

    // Hub page
    Route::get('/',                                         [AdminUserReportController::class, 'index'])         ->name('index');

       // Paid flow
    Route::get('/paid',                                          [AdminUserReportController::class, 'paidTournaments'])  ->name('paid.tournaments');
    Route::get('/paid/{seasonId}/categories',                    [AdminUserReportController::class, 'paidCategories'])   ->name('paid.categories');
    Route::get('/paid/{seasonId}/singles',                       [AdminUserReportController::class, 'paidSinglesAge'])   ->name('paid.singles.age');
    Route::get('/paid/{seasonId}/singles/{age}',                 [AdminUserReportController::class, 'paidSingles'])      ->name('paid.singles');
    Route::get('/paid/{seasonId}/doubles',                       [AdminUserReportController::class, 'paidDoublesAge'])   ->name('paid.doubles.age');
    Route::get('/paid/{seasonId}/doubles/{age}',                 [AdminUserReportController::class, 'paidDoubles'])      ->name('paid.doubles');

    // Not-paid flow
    Route::get('/not-paid',                                      [AdminUserReportController::class, 'notPaidTournaments'])  ->name('notpaid.tournaments');
    Route::get('/not-paid/{seasonId}/categories',                [AdminUserReportController::class, 'notPaidCategories'])   ->name('notpaid.categories');
    Route::get('/not-paid/{seasonId}/singles',                   [AdminUserReportController::class, 'notPaidSinglesAge'])   ->name('notpaid.singles.age');
    Route::get('/not-paid/{seasonId}/singles/{age}',             [AdminUserReportController::class, 'notPaidSingles'])      ->name('notpaid.singles');
    Route::get('/not-paid/{seasonId}/doubles',                   [AdminUserReportController::class, 'notPaidDoublesAge'])   ->name('notpaid.doubles.age');
    Route::get('/not-paid/{seasonId}/doubles/{age}',             [AdminUserReportController::class, 'notPaidDoubles'])      ->name('notpaid.doubles');
    Route::get('/{id}/edit',                                     [AdminUserReportController::class, 'edit'])   ->name('edit');
    Route::put('/{id}',                                          [AdminUserReportController::class, 'update']) ->name('update');

});







Route::prefix('matches')->name('admin-matches.')->middleware(['admin.auth', 'no.cache'])->group(function () {

    // Index
    Route::get('/',                    [MatchManagerController::class, 'index'])        ->name('index');

    // Setup — admin picks court/umpire/scorer/division/type, system auto-pairs
    Route::get('/setup',               [MatchManagerController::class, 'setup'])        ->name('setup');
    Route::post('/setup',              [MatchManagerController::class, 'storeSetup'])   ->name('store-setup');

    Route::get('/preview',  [MatchManagerController::class, 'preview']) ->name('preview');
    Route::post('/confirm', [MatchManagerController::class, 'confirm']) ->name('confirm');

    // Live scoring
    Route::get('/{match}/live',        [MatchManagerController::class, 'live'])         ->name('live');
    Route::post('/{match}/score',      [MatchManagerController::class, 'updateScore'])  ->name('update-score');

    // Declare winner (admin manual)
    Route::post('/{match}/declare',    [MatchManagerController::class, 'declareWinner'])->name('declare-winner');

    // Force end (no winner)
    Route::post('/{match}/force-end',  [MatchManagerController::class, 'forceEnd'])     ->name('force-end');

    // Complete summary
    Route::get('/{match}/complete',    [MatchManagerController::class, 'complete'])     ->name('complete');

    Route::get('eligible-count', [MatchManagerController::class, 'eligibleCount'])
    ->name('eligible-count');

    // Edit match scores/winner
    Route::get('/{match}/edit',        [MatchManagerController::class, 'editMatch'])    ->name('edit');
    Route::put('/{match}/edit',        [MatchManagerController::class, 'updateMatch'])  ->name('update-match');

    // Results list
    Route::get('/results',             [MatchManagerController::class, 'results'])      ->name('results');

    // Bracket
    Route::get('/bracket',             [MatchManagerController::class, 'bracket'])      ->name('bracket');

});


Route::prefix('player')->name('user-')->group(function () {

    // Public auth routes
    Route::get('/login',  [UserAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
      Route::get('/otp',         [UserAuthController::class, 'showOtp'])->name('login.otp');
    Route::post('/otp/verify', [UserAuthController::class, 'verifyOtp'])->name('login.otp.verify');
   Route::get('/profile', [UserAuthController::class, 'profile'])->name('profile');


    // Protected player routes
    Route::middleware('player.auth')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
           Route::get('/profile',   [UserDashboardController::class, 'showProfile'])->name('profile');
        Route::put('/profile',   [UserDashboardController::class, 'updateProfile'])->name('profile.update');
           Route::get('/live-scores', [UserDashboardController::class, 'liveScores'])->name('live-scores');
        // add more player routes here
    });

});


