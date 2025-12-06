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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name'); // e.g., "HR Manager"
            $table->string('slug'); // e.g., "hr_manager"
            $table->text('description')->nullable();
            $table->json('permissions'); // Array of permissions
            $table->boolean('is_system_role')->default(false); // Cannot be deleted
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->unique(['tenant_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
