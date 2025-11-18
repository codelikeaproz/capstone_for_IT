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
        Schema::create('vehicle_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('incident_id')->constrained('incidents')->onDelete('cascade');
            $table->foreignId('assignment_id')->nullable()->constrained('users')->onDelete('set null')->comment('User who assigned the vehicle');
            $table->string('dispatch_location')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['dispatched', 'en_route', 'arrived', 'completed', 'cancelled'])->default('dispatched');
            $table->dateTime('dispatched_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('arrived_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'incident_id']);
            $table->index('status');
            $table->index('dispatched_at');
        });

        // Backfill vehicle_dispatches from incidents with assigned_vehicle_id
        $incidents = DB::table('incidents')
            ->whereNotNull('assigned_vehicle_id')
            ->get();

        foreach ($incidents as $incident) {
            DB::table('vehicle_dispatches')->insert([
                'vehicle_id' => $incident->assigned_vehicle_id,
                'incident_id' => $incident->id,
                'assignment_id' => $incident->assigned_staff_id,
                'dispatch_location' => $incident->location,
                'notes' => 'Migrated from incident assignment',
                'status' => match($incident->status) {
                    'pending' => 'dispatched',
                    'active' => 'en_route',
                    'resolved', 'closed' => 'completed',
                    default => 'dispatched',
                },
                'dispatched_at' => $incident->response_time ?? $incident->incident_date,
                'completed_at' => $incident->resolved_at,
                'created_at' => $incident->created_at,
                'updated_at' => $incident->updated_at,
            ]);
        }

        // Also backfill from vehicle_utilizations
        $utilizations = DB::table('vehicle_utilizations')->get();

        foreach ($utilizations as $util) {
            if ($util->incident_id) {
                // Check if dispatch already exists
                $exists = DB::table('vehicle_dispatches')
                    ->where('vehicle_id', $util->vehicle_id)
                    ->where('incident_id', $util->incident_id)
                    ->exists();

                if (!$exists) {
                    DB::table('vehicle_dispatches')->insert([
                        'vehicle_id' => $util->vehicle_id,
                        'incident_id' => $util->incident_id,
                        'assignment_id' => $util->driver_id,
                        'dispatch_location' => $util->origin_address,
                        'notes' => "Service: {$util->service_type}. Origin: {$util->origin_address}. Destination: {$util->destination_address}.",
                        'status' => match($util->status) {
                            'scheduled' => 'dispatched',
                            'in_progress' => 'en_route',
                            'completed' => 'completed',
                            'cancelled' => 'cancelled',
                            default => 'dispatched',
                        },
                        'dispatched_at' => $util->service_date,
                        'completed_at' => $util->status === 'completed' ? $util->updated_at : null,
                        'created_at' => $util->created_at,
                        'updated_at' => $util->updated_at,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_dispatches');
    }
};
