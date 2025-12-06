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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverting to enum might be tricky if data doesn't fit, but for now we define the original state
            // We can't easily revert to enum with data that doesn't match, so we might leave it as string or try best effort
            // For safety in dev, we can try to revert to enum
            // $table->enum('role', ['super_admin', 'company_admin', 'manager', 'employee'])->change();
        });
    }
};
