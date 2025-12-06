<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('renewal_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->date('sent_date'); // Date when reminder was sent
            $table->date('expiry_date'); // Subscription expiry date at time of sending
            $table->integer('days_before_expiry'); // How many days before expiry
            $table->timestamps();

            // Prevent duplicate reminders on same day
            $table->unique(['tenant_id', 'sent_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renewal_reminder_logs');
    }
};
