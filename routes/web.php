<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CandidateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication routes (must be BEFORE auth middleware to prevent redirect loop!)
require __DIR__ . '/auth.php';

// Token-based login route (public, no auth required)
Route::get('/login/token/{token}', function ($token) {
    // Find the token in database
    $loginToken = \App\Models\LoginToken::where('token', $token)->first();

    // If token doesn't exist, reject
    if (!$loginToken) {
        return redirect()->route('login')->with('error', 'Invalid login link - token not found');
    }

    // CRITICAL: Check if valid BEFORE doing anything else
    // This checks: !used AND expires_at is in future
    if (!$loginToken->isValid()) {
        if ($loginToken->used) {
            return redirect()->route('login')->with('error', 'This login link has already been used and is no longer valid');
        }
        return redirect()->route('login')->with('error', 'This login link has expired');
    }

    // Mark as used IMMEDIATELY (before login to prevent race conditions)
    $loginToken->markAsUsed();

    // Refresh from database to ensure the 'used' flag is persisted
    $loginToken->refresh();

    // Double-check it was actually marked as used in database
    if (!$loginToken->used) {
        return redirect()->route('login')->with('error', 'Unable to validate token. Please try again.');
    }

    // Now log in the user
    auth()->login($loginToken->user);

    // Redirect based on role
    if (auth()->user()->role === 'super_admin') {
        return redirect('/super-admin/tenants')->with('success', 'Successfully logged in via secure link');
    }
    return redirect('/dashboard')->with('success', 'Successfully logged in via secure link');
})->name('login.token');

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'super_admin') {
            return redirect('/super-admin/tenants');
        }
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Authenticated routes (tenant)
Route::middleware(['auth', 'tenant.scope'])->group(function () {
    // Dashboard fallback
    Route::get('/dashboard', function () {
        return redirect(roleRoute('dashboard', [], false));
    })->name('dashboard');

    // Organization Management
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', \App\Http\Controllers\DesignationController::class);
    Route::resource('shifts', \App\Http\Controllers\ShiftController::class);

    // Employee Management
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/documents', [\App\Http\Controllers\EmployeeDocumentController::class, 'store'])
        ->name('employees.documents.store');
    Route::post('employees/{employee}/banking', [\App\Http\Controllers\EmployeeBankingController::class, 'store'])
        ->name('employees.banking.store');

    // Attendance Management
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/punch-in', [AttendanceController::class, 'punchIn'])->name('punch-in');
        Route::post('/punch-out', [AttendanceController::class, 'punchOut'])->name('punch-out');
        Route::get('/corrections/request', function () {
            $corrections = \App\Models\AttendanceCorrection::with('employee')->orderBy('created_at', 'desc')->get();
            return view('attendance.corrections', compact('corrections'));
        })->name('corrections.request');
        Route::post('/corrections', [AttendanceController::class, 'requestCorrection'])->name('corrections.store');
        Route::post('/corrections/{id}/approve', [AttendanceController::class, 'approveCorrection'])->name('corrections.approve');
        Route::post('/corrections/{id}/reject', [AttendanceController::class, 'rejectCorrection'])->name('corrections.reject');
        Route::get('/get-punch-times', [AttendanceController::class, 'getPunchTimes'])->name('get-punch-times');
    });
    Route::resource('devices', \App\Http\Controllers\BiometricDeviceController::class);

    // Settings
    Route::resource('leave-types', \App\Http\Controllers\LeaveTypeController::class);
    Route::resource('holidays', \App\Http\Controllers\HolidayController::class);
    Route::resource('tax-slabs', \App\Http\Controllers\TaxSlabController::class);
    Route::resource('salary-structures', \App\Http\Controllers\SalaryStructureController::class);

    // Leave Management
    Route::resource('leave-requests', LeaveRequestController::class);
    Route::post('leave-requests/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::post('leave-requests/{id}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');

    // Payroll Management
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/create', [PayrollController::class, 'create'])->name('create');
        Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
        Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::get('/{payroll}/download', [PayrollController::class, 'downloadPDF'])->name('download');
        Route::post('/{payroll}/mark-paid', [PayrollController::class, 'markAsPaid'])->name('mark-paid');
    });

    // Recruitment (ATS)
    Route::resource('jobs', \App\Http\Controllers\JobPostingController::class);
    Route::resource('candidates', CandidateController::class);
    Route::post('candidates/{candidate}/move', [CandidateController::class, 'moveStage'])->name('candidates.move');
    Route::post('candidates/{candidate}/notes', [CandidateController::class, 'updateNotes'])->name('candidates.notes');
    Route::resource('interviews', \App\Http\Controllers\InterviewController::class);

    // Performance Management
    Route::resource('appraisal-cycles', \App\Http\Controllers\AppraisalCycleController::class);
    Route::resource('appraisals', \App\Http\Controllers\AppraisalController::class);
    Route::resource('goals', \App\Http\Controllers\EmployeeGoalController::class);

    // Asset Management
    Route::resource('assets', AssetController::class);
    Route::post('assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('assets/{asset}/return', [AssetController::class, 'return'])->name('assets.return');

    // Documents
    Route::get('/documents/company', [\App\Http\Controllers\CompanyDocumentController::class, 'index'])
        ->name('documents.company');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/attendance', [\App\Http\Controllers\ReportController::class, 'attendance'])->name('attendance');
        Route::get('/payroll', [\App\Http\Controllers\ReportController::class, 'payroll'])->name('payroll');
        Route::get('/headcount', [\App\Http\Controllers\ReportController::class, 'headcount'])->name('headcount');
        Route::get('/attrition', [\App\Http\Controllers\ReportController::class, 'attrition'])->name('attrition');
    });

    // Employee Self-Service (ESS) - Role-based routes
    Route::prefix('employee')->name('employee.')->middleware(['role:employee,manager,company_admin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ESSController::class, 'dashboard'])->name('dashboard');
        Route::get('/attendance', [\App\Http\Controllers\ESSController::class, 'attendance'])->name('attendance');
        Route::get('/leaves', [\App\Http\Controllers\ESSController::class, 'leaves'])->name('leaves');
        Route::get('/payslips', [\App\Http\Controllers\ESSController::class, 'payslips'])->name('payslips');
        Route::get('/payslips/{id}/download', [\App\Http\Controllers\ESSController::class, 'downloadPayslip'])->name('payslips.download');
        Route::get('/documents', [\App\Http\Controllers\ESSController::class, 'documents'])->name('documents');
        Route::post('/leaves', [\App\Http\Controllers\LeaveRequestController::class, 'store'])->name('leaves.store');
        Route::get('/profile', [\App\Http\Controllers\ESSController::class, 'profile'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\ESSController::class, 'updateProfile'])->name('profile.update');

        // Attendance Actions
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::post('/punch-in', [\App\Http\Controllers\AttendanceController::class, 'punchIn'])->name('punch-in');
            Route::post('/punch-out', [\App\Http\Controllers\AttendanceController::class, 'punchOut'])->name('punch-out');
            Route::post('/corrections', [\App\Http\Controllers\AttendanceController::class, 'requestCorrection'])->name('corrections.store');
            Route::get('/get-punch-times', [\App\Http\Controllers\AttendanceController::class, 'getPunchTimes'])->name('get-punch-times');
        });

        // Resignations under employee
        Route::prefix('resignations')->name('resignations.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ResignationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\ResignationController::class, 'create'])->name('create');
            Route::post('/create', [\App\Http\Controllers\ResignationController::class, 'store'])->name('store');
            Route::get('/{resignation}', [\App\Http\Controllers\ResignationController::class, 'show'])->name('show');
        });
    });

    // Manager Routes (ESS + Team Management)
    Route::prefix('manager')->name('manager.')->middleware(['role:manager,company_admin'])->group(function () {
        // ESS Routes (Managers are also employees)
        Route::get('/dashboard', [\App\Http\Controllers\ESSController::class, 'dashboard'])->name('dashboard');
        Route::get('/attendance', [\App\Http\Controllers\ESSController::class, 'attendance'])->name('attendance');
        Route::get('/leaves', [\App\Http\Controllers\ESSController::class, 'leaves'])->name('leaves');
        Route::get('/payslips', [\App\Http\Controllers\ESSController::class, 'payslips'])->name('payslips');
        Route::get('/payslips/{id}/download', [\App\Http\Controllers\ESSController::class, 'downloadPayslip'])->name('payslips.download');
        Route::get('/documents', [\App\Http\Controllers\ESSController::class, 'documents'])->name('documents');
        Route::post('/leaves', [\App\Http\Controllers\LeaveRequestController::class, 'store'])->name('leaves.store');
        Route::get('/profile', [\App\Http\Controllers\ESSController::class, 'profile'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\ESSController::class, 'updateProfile'])->name('profile.update');

        // Attendance Actions
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::post('/punch-in', [\App\Http\Controllers\AttendanceController::class, 'punchIn'])->name('punch-in');
            Route::post('/punch-out', [\App\Http\Controllers\AttendanceController::class, 'punchOut'])->name('punch-out');
            Route::post('/corrections', [\App\Http\Controllers\AttendanceController::class, 'requestCorrection'])->name('corrections.store');
            Route::get('/get-punch-times', [\App\Http\Controllers\AttendanceController::class, 'getPunchTimes'])->name('get-punch-times');
        });

        // Manager Specific Routes
        Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');

        Route::prefix('resignations')->name('resignations.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ResignationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\ResignationController::class, 'create'])->name('create');
            Route::post('/create', [\App\Http\Controllers\ResignationController::class, 'store'])->name('store');
            Route::get('/{resignation}', [\App\Http\Controllers\ResignationController::class, 'show'])->name('show');
            Route::post('/{resignation}/manager-approve', [\App\Http\Controllers\ResignationController::class, 'approveByManager'])->name('manager.approve');
        });

        // Leave Management
        Route::resource('leave-requests', \App\Http\Controllers\LeaveRequestController::class);
        Route::post('leave-requests/{id}/approve', [\App\Http\Controllers\LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::post('leave-requests/{id}/reject', [\App\Http\Controllers\LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
        Route::post('leave-requests/{id}/cancel', [\App\Http\Controllers\LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
    });

    // Admin Routes (with admin prefix)
    Route::prefix('admin')->name('admin.')->middleware(['role:company_admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // Organization
        Route::resource('departments', \App\Http\Controllers\DepartmentController::class);
        Route::resource('designations', \App\Http\Controllers\DesignationController::class);
        Route::resource('shifts', \App\Http\Controllers\ShiftController::class);

        // Employees - using the fixed EmployeeController with manual model fetching
        Route::resource('employees', \App\Http\Controllers\EmployeeController::class);

        // Attendance
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('store');
            Route::post('/punch-in', [\App\Http\Controllers\AttendanceController::class, 'punchIn'])->name('punch-in');
            Route::post('/punch-out', [\App\Http\Controllers\AttendanceController::class, 'punchOut'])->name('punch-out');
            Route::get('/corrections/request', function () {
                $corrections = \App\Models\AttendanceCorrection::with('employee')->orderBy('created_at', 'desc')->get();
                return view('attendance.corrections', compact('corrections'));
            })->name('corrections.request');
            Route::post('/corrections', [\App\Http\Controllers\AttendanceController::class, 'requestCorrection'])->name('corrections.store');
            Route::post('/corrections/{id}/approve', [\App\Http\Controllers\AttendanceController::class, 'approveCorrection'])->name('corrections.approve');
            Route::post('/corrections/{id}/reject', [\App\Http\Controllers\AttendanceController::class, 'rejectCorrection'])->name('corrections.reject');
            Route::get('/get-punch-times', [\App\Http\Controllers\AttendanceController::class, 'getPunchTimes'])->name('get-punch-times');
        });

        // Leave Requests
        Route::resource('leave-requests', \App\Http\Controllers\LeaveRequestController::class);
        Route::post('leave-requests/{id}/approve', [\App\Http\Controllers\LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::post('leave-requests/{id}/reject', [\App\Http\Controllers\LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
        Route::post('leave-requests/{id}/cancel', [\App\Http\Controllers\LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');

        // Payroll
        Route::resource('payroll', \App\Http\Controllers\PayrollController::class);
        Route::post('payroll/generate', [\App\Http\Controllers\PayrollController::class, 'generate'])->name('payroll.generate');
        Route::get('payroll/{payroll}/download', [\App\Http\Controllers\PayrollController::class, 'downloadPDF'])->name('payroll.download');
        Route::post('payroll/{payroll}/mark-paid', [\App\Http\Controllers\PayrollController::class, 'markAsPaid'])->name('payroll.mark-paid');

        // Settings
        Route::resource('leave-types', \App\Http\Controllers\LeaveTypeController::class);
        Route::resource('holidays', \App\Http\Controllers\HolidayController::class);
        Route::resource('tax-slabs', \App\Http\Controllers\TaxSlabController::class);
        Route::resource('salary-structures', \App\Http\Controllers\SalaryStructureController::class);
        Route::resource('devices', \App\Http\Controllers\BiometricDeviceController::class);

        // Recruitment
        Route::resource('jobs', \App\Http\Controllers\JobPostingController::class);
        Route::resource('candidates', \App\Http\Controllers\CandidateController::class);
        Route::resource('interviews', \App\Http\Controllers\InterviewController::class);
        Route::post('candidates/{candidate}/notes', [\App\Http\Controllers\CandidateController::class, 'updateNotes'])->name('candidates.notes');

        // Performance
        Route::resource('appraisal-cycles', \App\Http\Controllers\AppraisalCycleController::class);
        Route::resource('appraisals', \App\Http\Controllers\AppraisalController::class);
        Route::resource('goals', \App\Http\Controllers\EmployeeGoalController::class);

        // Assets
        Route::resource('assets', \App\Http\Controllers\AssetController::class);
        Route::post('assets/{asset}/assign', [\App\Http\Controllers\AssetController::class, 'assign'])->name('assets.assign');
        Route::post('assets/{asset}/return', [\App\Http\Controllers\AssetController::class, 'return'])->name('assets.return');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/attendance', [\App\Http\Controllers\ReportController::class, 'attendance'])->name('attendance');
            Route::get('/payroll', [\App\Http\Controllers\ReportController::class, 'payroll'])->name('payroll');
            Route::get('/headcount', [\App\Http\Controllers\ReportController::class, 'headcount'])->name('headcount');
            Route::get('/attrition', [\App\Http\Controllers\ReportController::class, 'attrition'])->name('attrition');
        });

        // Resignations
        Route::prefix('resignations')->name('resignations.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ResignationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\ResignationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\ResignationController::class, 'store'])->name('store');
            Route::get('/{resignation}', [\App\Http\Controllers\ResignationController::class, 'show'])->name('show');
            Route::post('/{resignation}/admin-approve', [\App\Http\Controllers\ResignationController::class, 'approveByAdmin'])->name('admin.approve');
        });

        // Users & Roles
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('roles', \App\Http\Controllers\RoleController::class);


        // Password Management
        Route::prefix('password-management')->name('password.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PasswordManagementController::class, 'index'])->name('index');
            Route::post('reset/{user}', [\App\Http\Controllers\Admin\PasswordManagementController::class, 'resetPassword'])->name('reset');
            Route::post('login-link/{user}', [\App\Http\Controllers\Admin\PasswordManagementController::class, 'generateLoginLink'])->name('link');
            Route::post('impersonate/{user}', [\App\Http\Controllers\Admin\PasswordManagementController::class, 'impersonate'])->name('impersonate');
        });
    });

    // Stop impersonation (accessible from anywhere)
    Route::get('/stop-impersonation', [\App\Http\Controllers\Admin\PasswordManagementController::class, 'stopImpersonation'])->name('stop-impersonation');
    // User & Role Management
    Route::resource('users', \App\Http\Controllers\UserController::class);
});

// Super Admin Routes
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // Clients (Tenants)
    Route::resource('tenants', \App\Http\Controllers\SuperAdmin\TenantController::class);
    Route::post('tenants/{tenant}/toggle-status', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'toggleStatus'])
        ->name('tenants.toggle-status');

    // Subscription Plans
    Route::resource('subscription-plans', \App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class);
    Route::resource('roles', \App\Http\Controllers\SuperAdmin\SuperAdminRoleController::class);

    // Settings
    Route::get('settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'update'])->name('settings.upd

ate');

    // Email Templates
    Route::get('email-templates', [\App\Http\Controllers\SuperAdmin\EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('email-templates/{emailTemplate}/edit', [\App\Http\Controllers\SuperAdmin\EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::put('email-templates/{emailTemplate}', [\App\Http\Controllers\SuperAdmin\EmailTemplateController::class, 'update'])->name('email-templates.update');
    Route::post('email-templates/{emailTemplate}/send-test', [\App\Http\Controllers\SuperAdmin\EmailTemplateController::class, 'sendTest'])->name('email-templates.send-test');

    // Subscriptions
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'index'])->name('index');
        Route::post('/{tenant}/update-plan', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'updatePlan'])->name('update-plan');
        Route::post('/{tenant}/extend', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'extend'])->name('extend');
        Route::post('/{tenant}/payment', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'recordPayment'])->name('record-payment');
    });

    // Revenue
    Route::prefix('revenue')->name('revenue.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\RevenueController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\SuperAdmin\RevenueController::class, 'export'])->name('export');
    });

    // Password Management
    Route::prefix('passwords')->name('passwords.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\PasswordManagementController::class, 'index'])->name('index');
        Route::post('/{user}/reset', [\App\Http\Controllers\SuperAdmin\PasswordManagementController::class, 'resetPassword'])->name('reset');
        Route::get('/{user}/audit', [\App\Http\Controllers\SuperAdmin\PasswordManagementController::class, 'viewAuditLog'])->name('audit');
        Route::post('/{user}/generate-token', [\App\Http\Controllers\SuperAdmin\PasswordManagementController::class, 'generateLoginToken'])->name('generate-token');
        Route::post('/{user}/impersonate', [\App\Http\Controllers\SuperAdmin\PasswordManagementController::class, 'impersonate'])->

            name('impersonate');
    });

    // Stop impersonating (accessible even when impersonating)
    Route::post('/stop-impersonating', [\App\Http\Controllers\SuperAdmin\PasswordManagementController::class, 'stopImpersonating'])->name('stop-impersonating');

    // Super Admin Users
    Route::resource('super-admins', \App\Http\Controllers\SuperAdmin\SuperAdminUserController::class)->only(['index', 'store', 'destroy']);

    // Profile
    Route::get('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'update'])->name('profile.update');
});
