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
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', ['contract', 'id_proof', 'offer_letter', 'resignation', 'other'])->default('other');
            $table->string('document_name');
            $table->string('file_path');
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });

        Schema::create('company_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->enum('category', ['policy', 'form', 'handbook', 'other'])->default('other');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->boolean('is_public')->default(false); // Visible to all employees
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();

            $table->index(['tenant_id', 'category']);
        });

        Schema::create('employee_banking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('account_holder_name');
            $table->text('account_number'); // Encrypted
            $table->string('bank_name');
            $table->string('branch_name')->nullable();
            $table->string('ifsc_code');
            $table->boolean('is_primary')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });

        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('relationship');
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });

        /*
        Schema::create('resignations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('resignation_date');
            $table->date('last_working_date');
            $table->integer('notice_period_days')->default(30);
            $table->text('reason')->nullable();
            $table->enum('status', ['submitted', 'accepted', 'withdrawn'])->default('submitted');
            $table->foreignId('accepted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });

        Schema::create('fnf_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('resignation_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('pending_salary', 10, 2)->default(0);
            $table->decimal('leave_encashment', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->json('deductions')->nullable(); // {reason: amount}
            $table->decimal('total_payable', 10, 2);
            $table->enum('status', ['pending', 'processed', 'paid'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fnf_settlements');
        Schema::dropIfExists('resignations');
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('employee_banking');
        Schema::dropIfExists('company_documents');
        Schema::dropIfExists('employee_documents');
    }
};
