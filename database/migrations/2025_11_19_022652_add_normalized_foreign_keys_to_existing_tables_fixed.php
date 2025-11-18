<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Disable transaction wrapping for this migration
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =======================
        // 1. ADD role_id TO USERS
        // =======================
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('role');
                $table->index('role_id');
            });

            // Backfill role_id from role string
            $roles = DB::table('account_roles')->get();
            foreach ($roles as $role) {
                DB::table('users')
                    ->where('role', $role->role_name)
                    ->update(['role_id' => $role->id]);
            }

            // Add foreign key constraint separately
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')
                    ->references('id')
                    ->on('account_roles')
                    ->onDelete('set null');
            });
        }

        // =======================
        // 2. ADD FOREIGN KEYS TO REQUESTS
        // =======================
        if (!Schema::hasColumn('requests', 'victim_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->unsignedBigInteger('victim_id')->nullable()->after('request_description');
                $table->index('victim_id');
            });

            Schema::table('requests', function (Blueprint $table) {
                $table->foreign('victim_id')
                    ->references('id')
                    ->on('victims')
                    ->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('requests', 'incident_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->unsignedBigInteger('incident_id')->nullable()->after('victim_id');
                $table->index('incident_id');
            });

            // Backfill incident_id from incident_case_number
            $requests = DB::table('requests')
                ->whereNotNull('incident_case_number')
                ->where('incident_case_number', '!=', '')
                ->get();

            foreach ($requests as $request) {
                $incident = DB::table('incidents')
                    ->where('incident_number', $request->incident_case_number)
                    ->first();

                if ($incident) {
                    DB::table('requests')
                        ->where('id', $request->id)
                        ->update(['incident_id' => $incident->id]);
                }
            }

            Schema::table('requests', function (Blueprint $table) {
                $table->foreign('incident_id')
                    ->references('id')
                    ->on('incidents')
                    ->onDelete('set null');
            });
        }

        // =======================
        // 3. FIX incidents.assigned_vehicle_id FK
        // =======================
        // Clean up invalid references first
        DB::statement("
            UPDATE incidents
            SET assigned_vehicle_id = NULL
            WHERE assigned_vehicle_id IS NOT NULL
            AND assigned_vehicle_id NOT IN (SELECT id FROM vehicles)
        ");

        // Add foreign key if not exists
        try {
            Schema::table('incidents', function (Blueprint $table) {
                $table->foreign('assigned_vehicle_id')
                    ->references('id')
                    ->on('vehicles')
                    ->onDelete('set null');
            });
        } catch (\Exception $e) {
            // FK may already exist, skip
        }

        // =======================
        // 4. FIX vehicles.current_incident_id FK
        // =======================
        // Clean up invalid references first
        DB::statement("
            UPDATE vehicles
            SET current_incident_id = NULL
            WHERE current_incident_id IS NOT NULL
            AND current_incident_id NOT IN (SELECT id FROM incidents)
        ");

        // Add foreign key if not exists
        try {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->foreign('current_incident_id')
                    ->references('id')
                    ->on('incidents')
                    ->onDelete('set null');
            });
        } catch (\Exception $e) {
            // FK may already exist, skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys and columns in reverse order
        try {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropForeign(['current_incident_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('incidents', function (Blueprint $table) {
                $table->dropForeign(['assigned_vehicle_id']);
            });
        } catch (\Exception $e) {}

        if (Schema::hasColumn('requests', 'incident_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropForeign(['incident_id']);
                $table->dropColumn('incident_id');
            });
        }

        if (Schema::hasColumn('requests', 'victim_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropForeign(['victim_id']);
                $table->dropColumn('victim_id');
            });
        }

        if (Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            });
        }
    }
};
