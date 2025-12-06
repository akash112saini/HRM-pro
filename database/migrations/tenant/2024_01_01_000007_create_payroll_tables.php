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
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->string('name');
            $table->json('components')->nullable(); // [{name, type: earning/deduction, calculation_type, value}]
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('salary_structure_id')->nullable()->constrained()->onDelete('set null');
            $table->date('effective_from');

            // Salary Components
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('conveyance', 10, 2)->default(0);
            $table->decimal('medical', 10, 2)->default(0);
            $table->decimal('special_allowance', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2); // Calculated

            $table->json('custom_components')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id', 'is_active']);
        });

        Schema::create('tax_slabs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('financial_year');
            $table->decimal('min_amount', 12, 2);
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->decimal('tax_rate', 5, 2); // Percentage
            $table->timestamps();

            $table->index(['tenant_id', 'financial_year']);
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->foreignId('salary_structure_id')->nullable()->constrained()->onDelete('set null');

            // Salary Breakdown
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('net_salary', 10, 2);

            $table->json('earnings')->nullable(); // {component: amount}
            $table->json('deductions')->nullable(); // {component: amount}

            // Attendance Impact
            $table->integer('total_working_days');
            $table->integer('present_days');
            $table->integer('absent_days');
            $table->integer('leave_days');
            $table->decimal('loss_of_pay_amount', 10, 2)->default(0);

            // Overtime
            $table->decimal('overtime_amount', 10, 2)->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);

            // Tax
            $table->decimal('tax_deducted', 10, 2)->default(0);

            // Status
            $table->enum('status', ['draft', 'processed', 'paid'])->default('draft');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'employee_id', 'month', 'year']);
            $table->index(['tenant_id', 'status']);
            $table->unique(['tenant_id', 'employee_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('tax_slabs');
        Schema::dropIfExists('employee_salaries');
        Schema::dropIfExists('salary_structures');
    }
};
