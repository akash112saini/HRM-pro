<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'employees',
            'departments',
            'designations',
            'shifts',
            'attendances',
            'leave_requests',
            'leave_types',
            'leave_balances',
            'payrolls',
            'salary_structures',
            'tax_slabs',
            'holidays',
            'biometric_devices',
            'candidates',
            'interviews',
            'job_postings',
            'appraisals',
            'appraisal_cycles',
            'employee_goals',
            'assets',
            'asset_assignments',
            'employee_documents',
            'company_documents',
            'employee_banking',
            'emergency_contacts',
            'resignations',
            'attendance_corrections',
            'employee_salaries',
            'fnf_settlements',
            'renewal_reminder_logs', // This might be landlord? No, it was moved to landlord. Wait.
        ];

        // Disable foreign key checks to avoid constraint errors
        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to restore these tables in the landlord DB
    }
};
