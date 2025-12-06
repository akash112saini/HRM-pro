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
            if (Schema::hasTable('super_admin_roles')) {
                $table->foreignId('super_admin_role_id')->nullable()->after('role')->constrained('super_admin_roles')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('super_admin_role_id')->nullable()->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['super_admin_role_id']);
            $table->dropColumn('super_admin_role_id');
        });
    }
};
