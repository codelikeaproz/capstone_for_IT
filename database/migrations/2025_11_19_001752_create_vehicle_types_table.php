<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., "ambulance", "fire_truck"
            $table->string('name'); // e.g., "Ambulance", "Fire Truck"
            $table->text('description')->nullable();

            // UI customization
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->string('color')->default('blue'); // Tailwind color

            // Configuration
            $table->json('typical_equipment')->nullable(); // Standard equipment for this vehicle type
            $table->integer('typical_capacity')->nullable(); // Passenger/patient capacity
            $table->decimal('typical_fuel_capacity', 8, 2)->nullable(); // Liters

            // Response capabilities
            $table->json('response_types')->nullable(); // Array of incident types this vehicle responds to
            $table->integer('priority_level')->default(3); // Dispatch priority

            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
    }
};
