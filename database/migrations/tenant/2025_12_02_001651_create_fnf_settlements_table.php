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
        Schema::create('fnf_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('resignation_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('pending_salary', 10, 2)->default(0);
            $table->decimal('leave_encashment', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->json('deductions')->nullable(); // {reason: amount}
            $table->decimal('total_payable', 10, 2);
            $table->enum('status', ['pending', 'processed', 'paid'])->default('pending');
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fnf_settlements');
    }
};
