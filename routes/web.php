<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HospitalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HospitalController::class, 'home'])->name('home');
Route::get('/about', function () { return view('about'); })->name('about');
Route::get('/team', [HospitalController::class, 'team'])->name('team');
Route::middleware(['auth'])->group(function () {
    Route::get('/admin-dashboard', [HospitalController::class, 'dashboard'])->name('admin-dashboard');
    Route::get('/staff-dashboard', [HospitalController::class, 'staffDashboard'])->name('staff-dashboard');
});
Route::get('/appointment', [HospitalController::class, 'appointment'])->name('appointment');
Route::get('/monnify/return', [App\Http\Controllers\MonnifyController::class, 'handleReturn'])->name('monnify.return');
Route::get('/payment-status', [App\Http\Controllers\MonnifyController::class, 'paymentStatus'])->name('payment.status');
Route::get('/pages-directory', function () { return view('pages-directory'); })->name('pages-directory');
Route::get('/contact', function () { return view('contact'); })->name('contact');
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/signup', function () {
    return view('signup', ['roles' => ['patient', 'doctor', 'technician', 'admin']]);
})->name('signup');
Route::post('/signup', [AuthController::class, 'register'])->name('signup.post');
Route::get('/service', [HospitalController::class, 'services'])->name('service');
Route::get('/services/{slug}', [HospitalController::class, 'serviceDetail'])->name('service-detail');
Route::get('/verification-portal', function () { return view('verification-portal'); })->name('verification-portal');
Route::get('/password-reset-email', function () { return view('password-reset/email'); })->name('password.reset.email');
Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->name('password.forgot');
Route::get('/password-reset-link', function (Illuminate\Http\Request $request) {
    return view('password-reset/link', ['email' => $request->query('email'), 'token' => $request->query('token')]);
})->name('password.reset.link');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::get('/password-reset-confirmation', function () { return view('password-reset/confirmation'); })->name('password.reset.confirmation');

// Debug route - Remove in production
Route::get('/debug-assets', function () {
    return view('debug-assets');
});

Route::get('/test-urls', function () {
    return view('test-urls');
});
