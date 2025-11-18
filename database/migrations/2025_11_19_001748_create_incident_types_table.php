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
        Schema::create('incident_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., "traffic_accident", "fire_incident"
            $table->string('name'); // e.g., "Traffic Accident", "Fire Incident"
            $table->text('description')->nullable();

            // Default severity for this type
            $table->string('default_severity')->default('medium'); // critical, high, medium, low

            // UI customization
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->string('color')->default('blue'); // Tailwind color

            // Configuration
            $table->boolean('requires_vehicle')->default(false);
            $table->boolean('requires_medical_response')->default(false);
            $table->integer('priority_level')->default(3); // 1 (highest) to 5 (lowest)

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
        Schema::dropIfExists('incident_types');
    }
};
