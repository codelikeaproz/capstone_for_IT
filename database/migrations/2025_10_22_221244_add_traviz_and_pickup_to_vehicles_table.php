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
        // For PostgreSQL, we need to add new values to the enum type
        DB::statement("ALTER TABLE vehicles ALTER COLUMN vehicle_type TYPE VARCHAR(50)");

        // Now we can use the check constraint approach
        DB::statement("ALTER TABLE vehicles DROP CONSTRAINT IF EXISTS vehicles_vehicle_type_check");
        DB::statement("ALTER TABLE vehicles ADD CONSTRAINT vehicles_vehicle_type_check CHECK (vehicle_type IN ('ambulance', 'fire_truck', 'rescue_vehicle', 'patrol_car', 'support_vehicle', 'traviz', 'pick_up'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE vehicles DROP CONSTRAINT IF EXISTS vehicles_vehicle_type_check");
        DB::statement("ALTER TABLE vehicles ADD CONSTRAINT vehicles_vehicle_type_check CHECK (vehicle_type IN ('ambulance', 'fire_truck', 'rescue_vehicle', 'patrol_car', 'support_vehicle'))");
    }
};
