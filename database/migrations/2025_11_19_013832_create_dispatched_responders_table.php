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
        Schema::create('dispatched_responders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('vehicle_dispatches')->onDelete('cascade');
            $table->foreignId('responder_id')->constrained('users')->onDelete('cascade');
            $table->string('team_unit')->nullable();
            $table->string('position')->nullable()->comment('Driver, Medic, etc.');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Composite unique constraint
            $table->unique(['dispatch_id', 'responder_id']);
            $table->index('dispatch_id');
            $table->index('responder_id');
        });

        // Backfill dispatched_responders from vehicle_dispatches and vehicles.assigned_driver_id
        $dispatches = DB::table('vehicle_dispatches')->get();

        foreach ($dispatches as $dispatch) {
            $vehicle = DB::table('vehicles')->find($dispatch->vehicle_id);

            if ($vehicle && $vehicle->assigned_driver_id) {
                $user = DB::table('users')->find($vehicle->assigned_driver_id);

                // Only insert if user is a responder or if we don't have role info
                if ($user && ($user->role === 'responder' || !isset($user->role))) {
                    DB::table('dispatched_responders')->insertOrIgnore([
                        'dispatch_id' => $dispatch->id,
                        'responder_id' => $vehicle->assigned_driver_id,
                        'position' => 'Driver',
                        'notes' => 'Migrated from vehicle assigned driver',
                        'created_at' => $dispatch->created_at,
                        'updated_at' => $dispatch->updated_at,
                    ]);
                }
            }
        }

        // Also backfill from vehicle_utilizations.driver_id
        $utilizations = DB::table('vehicle_utilizations')->get();

        foreach ($utilizations as $util) {
            if ($util->driver_id && $util->incident_id) {
                $dispatch = DB::table('vehicle_dispatches')
                    ->where('vehicle_id', $util->vehicle_id)
                    ->where('incident_id', $util->incident_id)
                    ->first();

                if ($dispatch) {
                    $user = DB::table('users')->find($util->driver_id);

                    if ($user) {
                        DB::table('dispatched_responders')->insertOrIgnore([
                            'dispatch_id' => $dispatch->id,
                            'responder_id' => $util->driver_id,
                            'position' => 'Driver',
                            'notes' => 'Migrated from vehicle utilization',
                            'created_at' => $util->created_at,
                            'updated_at' => $util->updated_at,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatched_responders');
    }
};
