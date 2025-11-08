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
        Schema::table('incidents', function (Blueprint $table) {
            // Traffic Accident specific fields
            $table->integer('vehicle_count')->nullable()->after('vehicle_involved');
            $table->json('license_plates')->nullable()->after('vehicle_count');
            $table->text('driver_information')->nullable()->after('license_plates');

            // Medical Emergency specific fields
            $table->enum('medical_emergency_type', [
                'heart_attack', 'stroke', 'trauma', 'respiratory',
                'allergic_reaction', 'seizure', 'poisoning', 'other'
            ])->nullable()->after('incident_type');
            $table->boolean('ambulance_requested')->default(false)->after('medical_emergency_type');
            $table->integer('patient_count')->nullable()->after('ambulance_requested');
            $table->text('patient_symptoms')->nullable()->after('patient_count');

            // Fire Incident specific fields
            $table->enum('building_type', [
                'residential', 'commercial', 'industrial', 'government', 'agricultural', 'other'
            ])->nullable()->after('incident_type');
            $table->enum('fire_spread_level', [
                'contained', 'spreading', 'widespread', 'controlled', 'extinguished'
            ])->nullable()->after('building_type');
            $table->boolean('evacuation_required')->default(false)->after('fire_spread_level');
            $table->integer('evacuated_count')->nullable()->after('evacuation_required');
            $table->text('fire_cause')->nullable()->after('evacuated_count');
            $table->integer('buildings_affected')->nullable()->after('fire_cause');

            // Natural Disaster specific fields
            $table->enum('disaster_type', [
                'flood', 'earthquake', 'landslide', 'typhoon', 'drought', 'volcanic', 'tsunami', 'other'
            ])->nullable()->after('incident_type');
            $table->decimal('affected_area_size', 10, 2)->nullable()->after('disaster_type'); // in square kilometers
            $table->boolean('shelter_needed')->default(false)->after('affected_area_size');
            $table->integer('families_affected')->nullable()->after('shelter_needed');
            $table->integer('structures_damaged')->nullable()->after('families_affected');
            $table->text('infrastructure_damage')->nullable()->after('structures_damaged');

            // Criminal Activity specific fields
            $table->enum('crime_type', [
                'assault', 'theft', 'vandalism', 'domestic_violence', 'other'
            ])->nullable()->after('incident_type');
            $table->boolean('police_notified')->default(false)->after('crime_type');
            $table->string('case_number')->nullable()->after('police_notified');
            $table->text('suspect_description')->nullable()->after('case_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn([
                // Traffic Accident
                'vehicle_count',
                'license_plates',
                'driver_information',
                // Medical Emergency
                'medical_emergency_type',
                'ambulance_requested',
                'patient_count',
                'patient_symptoms',
                // Fire Incident
                'building_type',
                'fire_spread_level',
                'evacuation_required',
                'evacuated_count',
                'fire_cause',
                'buildings_affected',
                // Natural Disaster
                'disaster_type',
                'affected_area_size',
                'shelter_needed',
                'families_affected',
                'structures_damaged',
                'infrastructure_damage',
                // Criminal Activity
                'crime_type',
                'police_notified',
                'case_number',
                'suspect_description',
            ]);
        });
    }
};
