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
        // For PostgreSQL, we need to drop the old check constraint and add a new one
        // Get the constraint name
        $checkConstraint = DB::select("
            SELECT con.conname
            FROM pg_constraint con
            INNER JOIN pg_class rel ON rel.oid = con.conrelid
            WHERE rel.relname = 'users'
            AND con.contype = 'c'
            AND pg_get_constraintdef(con.oid) LIKE '%role%'
        ");

        // Drop the old check constraint if it exists
        if (!empty($checkConstraint)) {
            $constraintName = $checkConstraint[0]->conname;
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS {$constraintName}");
        }

        // Add new check constraint with superadmin included
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT users_role_check
            CHECK (role IN ('superadmin', 'admin', 'staff', 'responder', 'citizen'))
        ");

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

        // Drop the current check constraint
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");

        // Add back the original check constraint without superadmin
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT users_role_check
            CHECK (role IN ('admin', 'staff', 'responder', 'citizen'))
        ");

        // Log warning
        \Log::warning('SuperAdmin role migration rolled back. All superadmin users converted to admin.');
    }
};
