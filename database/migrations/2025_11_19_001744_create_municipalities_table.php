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
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Valencia"
            $table->string('province')->default('Bukidnon'); // Province name
            $table->string('region')->default('Region X'); // Region
            $table->string('zip_code')->nullable(); // Postal code

            // Geographic data
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Administrative data
            $table->integer('population')->nullable();
            $table->decimal('land_area_sqkm', 10, 2)->nullable(); // Land area in square kilometers

            // Contact information
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable(); // Municipal hall address

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
