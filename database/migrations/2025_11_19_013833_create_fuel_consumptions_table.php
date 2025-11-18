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
        Schema::create('fuel_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('vehicle_dispatches')->onDelete('cascade');
            $table->integer('starting_odometer')->nullable()->comment('Odometer reading at start in km');
            $table->integer('ending_odometer')->nullable()->comment('Odometer reading at end in km');
            $table->decimal('distance_traveled', 8, 2)->nullable()->comment('Distance in km');
            $table->decimal('fuel_consumed', 8, 2)->nullable()->comment('Fuel used in liters');
            $table->decimal('fuel_price_per_liter', 8, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->enum('fuel_type', ['gasoline', 'diesel', 'lpg', 'electric'])->default('diesel');
            $table->dateTime('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->index('dispatch_id');
            $table->index('timestamp');
        });

        // Backfill fuel_consumptions from vehicle_utilizations
        $utilizations = DB::table('vehicle_utilizations')
            ->whereNotNull('fuel_consumed')
            ->where('fuel_consumed', '>', 0)
            ->get();

        foreach ($utilizations as $util) {
            if ($util->incident_id) {
                $dispatch = DB::table('vehicle_dispatches')
                    ->where('vehicle_id', $util->vehicle_id)
                    ->where('incident_id', $util->incident_id)
                    ->first();

                if ($dispatch) {
                    // Calculate odometer readings if distance is available
                    $startingOdometer = null;
                    $endingOdometer = null;

                    if ($util->distance_traveled) {
                        $vehicle = DB::table('vehicles')->find($util->vehicle_id);
                        if ($vehicle) {
                            // Estimate based on current odometer minus distance
                            $endingOdometer = $vehicle->odometer_reading;
                            $startingOdometer = max(0, $endingOdometer - $util->distance_traveled);
                        }
                    }

                    // Estimate fuel cost (assuming average diesel price of PHP 60/liter)
                    $fuelCost = $util->fuel_consumed * 60;

                    DB::table('fuel_consumptions')->insert([
                        'dispatch_id' => $dispatch->id,
                        'starting_odometer' => $startingOdometer,
                        'ending_odometer' => $endingOdometer,
                        'distance_traveled' => $util->distance_traveled,
                        'fuel_consumed' => $util->fuel_consumed,
                        'fuel_price_per_liter' => 60.00, // Default estimate
                        'total_cost' => $fuelCost,
                        'fuel_type' => 'diesel', // Default assumption
                        'timestamp' => $util->service_date,
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
        Schema::dropIfExists('fuel_consumptions');
    }
};
