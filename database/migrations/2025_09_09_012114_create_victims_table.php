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
        Schema::create('victims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->onDelete('cascade');
            
            // Personal information
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->string('id_number')->nullable(); // Government ID
            
            // Medical information
            $table->enum('medical_status', ['uninjured', 'minor_injury', 'major_injury', 'critical', 'deceased']);
            $table->text('injury_description')->nullable();
            $table->text('medical_treatment')->nullable();
            
            // Hospital and transportation
            $table->string('hospital_referred')->nullable();
            $table->enum('transportation_method', ['ambulance', 'private_vehicle', 'helicopter', 'on_foot', 'other'])->nullable();
            $table->dateTime('hospital_arrival_time')->nullable();
            
            // Safety equipment usage
            $table->boolean('helmet_used')->nullable();
            $table->boolean('seatbelt_used')->nullable();
            $table->boolean('protective_gear_used')->nullable();
            
            // Vehicle involvement details
            $table->enum('victim_role', ['driver', 'passenger', 'pedestrian', 'cyclist', 'bystander', 'other'])->nullable();
            $table->string('vehicle_type_involved')->nullable();
            $table->string('seating_position')->nullable(); // Front, rear, driver, passenger
            
            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Insurance and legal
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->boolean('legal_action_required')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['incident_id', 'medical_status']);
            $table->index(['medical_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('victims');
    }
};
