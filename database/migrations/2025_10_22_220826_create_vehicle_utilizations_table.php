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
        Schema::create('vehicle_utilizations', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('victim_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('incident_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');

            // Service information
            $table->date('service_date');
            $table->string('trip_ticket_number')->nullable();

            // Location details
            $table->string('origin_address');
            $table->string('destination_address');

            // Service classification
            $table->enum('service_category', ['health', 'non_health']);
            $table->enum('service_type', [
                // Health services
                'vehicular_accident',
                'maternity',
                'stabbing_shooting',
                'transport_to_hospital',
                'transport_mentally_ill',
                'transport_cadaver',
                'discharge_transport',
                'hospital_transfer',
                'other_health',
                // Non-health services
                'equipment_transport',
                'materials_transport',
                'personnel_transport',
                'other_non_health'
            ]);

            // Fuel tracking
            $table->decimal('fuel_consumed', 8, 2)->nullable()->comment('Liters');
            $table->decimal('distance_traveled', 8, 2)->nullable()->comment('Kilometers');

            // Status tracking
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();

            // Municipality
            $table->string('municipality');

            $table->timestamps();

            // Indexes
            $table->index(['vehicle_id', 'service_date']);
            $table->index(['service_category', 'service_type']);
            $table->index(['municipality', 'service_date']);
            $table->index('service_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_utilizations');
    }
};
