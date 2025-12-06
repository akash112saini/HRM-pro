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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('user_id')->nullable(); // No foreign key constraint as it's cross-database
            $table->string('employee_code');

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('blood_group')->nullable();

            // Address
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();

            // Employment Details
            $table->date('joining_date');
            $table->date('confirmation_date')->nullable();
            $table->enum('employment_status', ['probation', 'confirmed', 'resigned', 'terminated'])->default('probation');

            // Organizational Relationships
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('designation_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');

            $table->string('profile_photo')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'employment_status']);
            $table->index(['tenant_id', 'department_id']);
            $table->index(['tenant_id', 'manager_id']);
            $table->unique(['tenant_id', 'employee_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
