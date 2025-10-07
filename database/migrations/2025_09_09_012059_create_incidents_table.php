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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_number')->unique(); // INC-YYYY-XXX format
            $table->enum('incident_type', [
                'traffic_accident', 'medical_emergency', 'fire_incident', 
                'natural_disaster', 'criminal_activity', 'other'
            ]);
            $table->enum('severity_level', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['pending', 'active', 'resolved', 'closed'])->default('pending');
            
            // Location information
            $table->string('location');
            $table->string('municipality');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Incident details
            $table->text('description');
            $table->dateTime('incident_date');
            $table->enum('weather_condition', ['clear', 'cloudy', 'rainy', 'stormy', 'foggy'])->nullable();
            $table->enum('road_condition', ['dry', 'wet', 'slippery', 'damaged', 'under_construction'])->nullable();
            
            // Casualty information
            $table->integer('casualty_count')->default(0);
            $table->integer('injury_count')->default(0);
            $table->integer('fatality_count')->default(0);
            
            // Property damage
            $table->decimal('property_damage_estimate', 12, 2)->nullable();
            $table->text('damage_description')->nullable();
            
            // Vehicle involvement
            $table->boolean('vehicle_involved')->default(false);
            $table->text('vehicle_details')->nullable();
            
            // Assignment and tracking
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('assigned_vehicle_id')->nullable(); // Will add foreign key later
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Media and documentation
            $table->json('photos')->nullable(); // Store photo paths
            $table->json('documents')->nullable(); // Store document paths
            
            // Response tracking
            $table->dateTime('response_time')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['municipality', 'incident_date']);
            $table->index(['severity_level', 'status']);
            $table->index(['incident_type', 'municipality']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
