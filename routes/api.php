<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BiometricController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Biometric Device Integration (No tenant scope - device authenticates itself)
Route::post('/biometric-push', [BiometricController::class, 'push']);
Route::post('/biometric-status', [BiometricController::class, 'status']);

// Mobile App APIs (Requires authentication and tenant scope)
Route::middleware(['auth:sanctum', 'tenant.scope'])->group(function () {

    // Employee Profile
    Route::get('/employee/profile', [App\Http\Controllers\Api\EmployeeController::class, 'profile']);
    Route::put('/employee/profile', [App\Http\Controllers\Api\EmployeeController::class, 'updateProfile']);

    // Attendance
    Route::prefix('attendance')->group(function () {
        Route::post('/punch-in', [App\Http\Controllers\Api\AttendanceController::class, 'punchIn']);
        Route::post('/punch-out', [App\Http\Controllers\Api\AttendanceController::class, 'punchOut']);
        Route::get('/my-attendance', [App\Http\Controllers\Api\AttendanceController::class, 'myAttendance']);
        Route::get('/today', [App\Http\Controllers\Api\AttendanceController::class, 'today']);
        Route::post('/corrections', [App\Http\Controllers\Api\AttendanceController::class, 'requestCorrection']);
    });

    // Leaves
    Route::prefix('leaves')->group(function () {
        Route::get('/balance', [App\Http\Controllers\Api\LeaveController::class, 'balance']);
        Route::get('/types', [App\Http\Controllers\Api\LeaveController::class, 'types']);
        Route::post('/apply', [App\Http\Controllers\Api\LeaveController::class, 'apply']);
        Route::get('/my-requests', [App\Http\Controllers\Api\LeaveController::class, 'myRequests']);
        Route::post('/{leave}/cancel', [App\Http\Controllers\Api\LeaveController::class, 'cancel']);
    });

    // Payroll
    Route::prefix('payroll')->group(function () {
        Route::get('/my-payslips', [App\Http\Controllers\Api\PayrollController::class, 'myPayslips']);
        Route::get('/payslip/{payroll}', [App\Http\Controllers\Api\PayrollController::class, 'viewPayslip']);
        Route::get('/payslip/{payroll}/download', [App\Http\Controllers\Api\PayrollController::class, 'downloadPayslip']);
    });

    // Documents
    Route::prefix('documents')->group(function () {
        Route::get('/my-documents', [App\Http\Controllers\Api\DocumentController::class, 'myDocuments']);
        Route::get('/company-documents', [App\Http\Controllers\Api\DocumentController::class, 'companyDocuments']);
    });

    // Team (For Managers)
    Route::prefix('team')->middleware('role:manager,company_admin')->group(function () {
        Route::get('/members', [App\Http\Controllers\Api\TeamController::class, 'members']);
        Route::get('/attendance', [App\Http\Controllers\Api\TeamController::class, 'attendance']);
        Route::get('/leave-requests', [App\Http\Controllers\Api\TeamController::class, 'leaveRequests']);
        Route::post('/leave-requests/{leave}/approve', [App\Http\Controllers\Api\TeamController::class, 'approveLeave']);
        Route::post('/leave-requests/{leave}/reject', [App\Http\Controllers\Api\TeamController::class, 'rejectLeave']);
    });
});
