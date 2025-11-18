<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique();
            $table->string('role_description')->nullable();
            $table->json('permissions')->nullable();
            $table->timestamps();

            $table->index('role_name');
        });

        // Seed initial roles from existing user.role enum values
        DB::table('account_roles')->insert([
            ['role_name' => 'superadmin', 'role_description' => 'System-wide administrator with full access', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'admin', 'role_description' => 'Municipality-level administrator', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'staff', 'role_description' => 'Municipal staff member', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'responder', 'role_description' => 'Emergency responder', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'citizen', 'role_description' => 'Citizen user', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_roles');
    }
};
