<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add 'superadmin' role support to users table
     *
     * Role Hierarchy:
     * - superadmin: Full system access, sees all municipalities
     * - admin: Municipality-level admin, sees only their municipality
     * - staff: MDRRMO staff with incident management access
     * - responder: Mobile field responder
     * - citizen: Public user for request submission
     */
    public function up(): void
    {
        // Laravel's enum() creates a string column, so we just need to modify the check constraint
        // This is a documentation migration - the actual role validation happens at application level

        // Update the role column to allow 'superadmin' value
        Schema::table('users', function (Blueprint $table) {
            // Drop old role check if exists and create new one
            $table->enum('role', ['superadmin', 'admin', 'staff', 'responder', 'citizen'])
                  ->default('staff')
                  ->change();
        });

        // Log the change
        \Log::info('SuperAdmin role support added to users table');
    }

    /**
     * Reverse the migrations.
     *
     * Converts all superadmin users to admin role before removing superadmin option
     */
    public function down(): void
    {
        // Update all superadmin users to admin
        DB::table('users')
            ->where('role', 'superadmin')
            ->update(['role' => 'admin']);

        // Revert enum values
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'responder', 'citizen'])
                  ->default('staff')
                  ->change();
        });

        // Log warning
        \Log::warning('SuperAdmin role migration rolled back. All superadmin users converted to admin.');
    }
};
