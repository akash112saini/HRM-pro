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
        Schema::create('resignations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');

            $table->date('resignation_date');
            $table->date('last_working_day');
            $table->text('reason');

            // Manager approval
            $table->enum('manager_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('manager_approved_by')->nullable();
            $table->timestamp('manager_approved_at')->nullable();
            $table->text('manager_remarks')->nullable();

            // Admin approval
            $table->enum('admin_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('admin_approved_by')->nullable();
            $table->timestamp('admin_approved_at')->nullable();
            $table->text('admin_remarks')->nullable();

            // Final status
            $table->enum('final_status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resignations');
    }
};
