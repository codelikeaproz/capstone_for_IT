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
        Schema::create('medical_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., "uninjured", "minor_injury", "major_injury", "critical", "deceased"
            $table->string('name'); // e.g., "Uninjured", "Minor Injury", "Major Injury", "Critical", "Deceased"
            $table->text('description')->nullable();

            // Medical classification
            $table->integer('severity_level'); // 1 (least severe) to 5 (most severe)
            $table->boolean('requires_hospitalization')->default(false);
            $table->boolean('requires_ambulance')->default(false);
            $table->boolean('requires_immediate_care')->default(false);

            // UI customization
            $table->string('color'); // e.g., "green", "yellow", "orange", "red", "gray"
            $table->string('badge_class')->nullable(); // DaisyUI badge class
            $table->string('icon')->nullable(); // FontAwesome icon class

            // Reporting
            $table->boolean('is_fatality')->default(false);
            $table->boolean('counts_as_injury')->default(false);

            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index(['is_active', 'severity_level']);
            $table->index('is_fatality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_statuses');
    }
};
