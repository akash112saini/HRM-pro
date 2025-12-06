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
        Schema::create('appraisal_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('employee_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('appraisal_cycle_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('target_value')->nullable();
            $table->string('achieved_value')->nullable();
            $table->integer('weightage')->default(0); // Percentage
            $table->enum('status', ['in_progress', 'achieved', 'not_achieved'])->default('in_progress');
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });

        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('appraisal_cycle_id')->constrained()->onDelete('cascade');
            $table->decimal('self_rating', 3, 2)->nullable(); // 1.00 to 5.00
            $table->decimal('manager_rating', 3, 2)->nullable();
            $table->decimal('final_rating', 3, 2)->nullable();
            $table->text('self_comments')->nullable();
            $table->text('manager_comments')->nullable();
            $table->enum('status', ['pending', 'submitted', 'reviewed', 'completed'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id', 'appraisal_cycle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisals');
        Schema::dropIfExists('employee_goals');
        Schema::dropIfExists('appraisal_cycles');
    }
};
