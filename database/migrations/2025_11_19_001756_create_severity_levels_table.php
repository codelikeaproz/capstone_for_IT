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
        Schema::create('severity_levels', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., "critical", "high", "medium", "low"
            $table->string('name'); // e.g., "Critical", "High", "Medium", "Low"
            $table->text('description')->nullable();

            // Priority and configuration
            $table->integer('priority_level'); // 1 (highest) to 4 (lowest)
            $table->integer('response_time_minutes')->nullable(); // Target response time

            // UI customization
            $table->string('color'); // e.g., "red", "orange", "yellow", "green"
            $table->string('badge_class')->nullable(); // DaisyUI badge class
            $table->string('icon')->nullable(); // FontAwesome icon class

            // Alerting
            $table->boolean('requires_immediate_notification')->default(false);
            $table->boolean('requires_supervisor_approval')->default(false);

            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index(['is_active', 'priority_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('severity_levels');
    }
};
