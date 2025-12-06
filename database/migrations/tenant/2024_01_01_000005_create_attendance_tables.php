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
        Schema::create('biometric_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->string('device_id')->unique();
            $table->string('device_name');
            $table->string('location')->nullable();
            $table->text('api_token'); // Encrypted
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');

            // Punch Times
            $table->dateTime('punch_in')->nullable();
            $table->dateTime('punch_out')->nullable();
            $table->string('punch_in_device_id')->nullable();
            $table->string('punch_out_device_id')->nullable();
            $table->json('punch_in_location')->nullable(); // {lat, lng}
            $table->json('punch_out_location')->nullable();

            // Status
            $table->enum('status', ['present', 'absent', 'half_day', 'on_leave', 'holiday', 'week_off'])->default('absent');

            // Calculations
            $table->decimal('total_work_hours', 5, 2)->default(0);
            $table->boolean('is_late')->default(false);
            $table->integer('late_minutes')->default(0);
            $table->boolean('is_early_departure')->default(false);
            $table->integer('early_departure_minutes')->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);

            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'employee_id', 'date']);
            $table->index(['tenant_id', 'date']);
            $table->index(['tenant_id', 'status']);
            $table->unique(['tenant_id', 'employee_id', 'date']);
        });

        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->dateTime('requested_punch_in')->nullable();
            $table->dateTime('requested_punch_out')->nullable();
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('biometric_devices');
    }
};
