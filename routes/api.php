<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\HospitalController;
use Illuminate\Support\Facades\Route;

Route::get('/services', [HospitalController::class, 'listServices']);
Route::get('/staff', [HospitalController::class, 'listStaff']);
Route::post('/appointments', [HospitalController::class, 'storeAppointment']);
Route::post('/appointments/initiate', [App\Http\Controllers\MonnifyController::class, 'initiate']);
Route::post('/contact', [HospitalController::class, 'contact']);
Route::post('/password/forgot', [App\Http\Controllers\AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [App\Http\Controllers\AuthController::class, 'resetPassword']);
Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);
Route::get('/dashboard/data', [AdminDashboardController::class, 'data']);
Route::get('/dashboard/staff', [AdminDashboardController::class, 'staff']);
Route::get('/dashboard/appointments', [AdminDashboardController::class, 'appointments']);
Route::get('/dashboard/transactions', [AdminDashboardController::class, 'transactions']);
Route::get('/dashboard/specializations', [AdminDashboardController::class, 'indexSpecializations']);
Route::post('/dashboard/specializations', [AdminDashboardController::class, 'storeSpecialization']);
Route::patch('/dashboard/specializations/{specialization}', [AdminDashboardController::class, 'updateSpecialization']);
Route::delete('/dashboard/specializations/{specialization}', [AdminDashboardController::class, 'destroySpecialization']);
Route::post('/dashboard/staff', [AdminDashboardController::class, 'storeStaff']);
Route::patch('/dashboard/staff/{staffMember}', [AdminDashboardController::class, 'updateStaff']);
Route::post('/dashboard/users', [AdminDashboardController::class, 'createUser']);
Route::get('/dashboard/pending-users', [AdminDashboardController::class, 'pendingUsers']);
Route::post('/dashboard/users/{user}/approve', [AdminDashboardController::class, 'approveUser']);
Route::delete('/dashboard/users/{user}', [AdminDashboardController::class, 'declineUser']);
Route::patch('/staff/{staffMember}/bio', [AdminDashboardController::class, 'updateStaffBio']);
Route::patch('/staff/{staffMember}/availability', [AdminDashboardController::class, 'updateStaffAvailability']);
Route::patch('/dashboard/appointments/{appointment}/status', [AdminDashboardController::class, 'updateAppointmentStatus']);
Route::post('/webhooks/monnify', [App\Http\Controllers\MonnifyWebhookController::class, 'handle']);
Route::get('/transactions/{transaction}/receipt', [App\Http\Controllers\MonnifyController::class, 'downloadReceipt'])->name('transactions.receipt');
Route::get('/transactions/reference/{reference}', [App\Http\Controllers\MonnifyController::class, 'getByReference']);
Route::get('/transactions/reference/{reference}/receipt', [App\Http\Controllers\MonnifyController::class, 'downloadReceiptByReference']);
Route::post('/verification/appointment', [App\Http\Controllers\MonnifyController::class, 'verifyAppointment']);
Route::post('/verification/mark-used', [App\Http\Controllers\MonnifyController::class, 'markAppointmentUsed']);
