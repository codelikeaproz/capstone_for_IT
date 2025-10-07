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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique();
            $table->string('license_plate')->unique();
            $table->enum('vehicle_type', ['ambulance', 'fire_truck', 'rescue_vehicle', 'patrol_car', 'support_vehicle']);
            $table->enum('status', ['available', 'in_use', 'maintenance', 'out_of_service'])->default('available');
            
            // Vehicle specifications
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('color');
            
            // Fuel management
            $table->decimal('fuel_capacity', 8, 2); // in liters
            $table->decimal('current_fuel_level', 5, 2)->default(100); // percentage
            $table->decimal('fuel_consumption_rate', 8, 2)->nullable(); // km per liter
            
            // Mileage tracking
            $table->integer('odometer_reading')->default(0); // in kilometers
            $table->integer('total_distance')->default(0); // total km traveled
            
            // Assignment and location
            $table->string('municipality');
            $table->foreignId('assigned_driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('current_incident_id')->nullable(); // Will add foreign key later
            
            // Maintenance tracking
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_due')->nullable();
            $table->text('maintenance_notes')->nullable();
            
            // Insurance and registration
            $table->string('insurance_policy')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('registration_expiry')->nullable();
            
            // Equipment and features
            $table->json('equipment_list')->nullable(); // Medical equipment, fire equipment, etc.
            $table->boolean('gps_enabled')->default(true);
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['municipality', 'status']);
            $table->index(['vehicle_type', 'status']);
            $table->index(['status', 'assigned_driver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
