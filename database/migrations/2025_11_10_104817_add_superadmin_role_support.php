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
        // PostgreSQL-compatible approach to add 'superadmin' role
        // We need to use raw SQL to properly modify the CHECK constraint

        // Step 1: Find and drop the existing CHECK constraint on role column
        $constraintName = DB::selectOne(
            "SELECT con.conname
             FROM pg_constraint con
             INNER JOIN pg_class rel ON rel.oid = con.conrelid
             INNER JOIN pg_attribute att ON att.attrelid = con.conrelid AND att.attnum = ANY(con.conkey)
             WHERE rel.relname = 'users' AND att.attname = 'role' AND con.contype = 'c'"
        );

        if ($constraintName) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT {$constraintName->conname}");
        }

        // Step 2: Add new CHECK constraint with 'superadmin' included
        DB::statement(
            "ALTER TABLE users ADD CONSTRAINT users_role_check
             CHECK (role::text = ANY (ARRAY['superadmin'::character varying, 'admin'::character varying, 'staff'::character varying, 'responder'::character varying, 'citizen'::character varying]::text[]))"
        );

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

        // PostgreSQL-compatible rollback
        // Drop the constraint we added
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");

        // Restore original CHECK constraint without 'superadmin'
        DB::statement(
            "ALTER TABLE users ADD CONSTRAINT users_role_check
             CHECK (role::text = ANY (ARRAY['admin'::character varying, 'staff'::character varying, 'responder'::character varying, 'citizen'::character varying]::text[]))"
        );

        // Log warning
        \Log::warning('SuperAdmin role migration rolled back. All superadmin users converted to admin.');
    }
};
