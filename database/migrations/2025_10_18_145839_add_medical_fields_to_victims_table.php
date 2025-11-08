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
        Schema::table('victims', function (Blueprint $table) {
            // Pregnancy-related fields (for female victims in medical emergencies)
            $table->boolean('is_pregnant')->default(false)->after('gender');
            $table->enum('pregnancy_trimester', ['first', 'second', 'third'])->nullable()->after('is_pregnant');
            $table->text('pregnancy_complications')->nullable()->after('pregnancy_trimester');
            $table->date('expected_delivery_date')->nullable()->after('pregnancy_complications');

            // Age-based care categorization
            $table->string('age_category', 20)->nullable()->after('age'); // child, teen, adult, elderly
            $table->boolean('requires_special_care')->default(false)->after('age_category');
            $table->text('special_care_notes')->nullable()->after('requires_special_care');

            // Medical vitals (for medical emergencies)
            $table->string('blood_pressure')->nullable()->after('medical_status');
            $table->integer('heart_rate')->nullable()->after('blood_pressure');
            $table->decimal('temperature', 4, 1)->nullable()->after('heart_rate'); // Celsius
            $table->integer('respiratory_rate')->nullable()->after('temperature');
            $table->enum('consciousness_level', ['alert', 'verbal', 'pain', 'unresponsive'])->nullable()->after('respiratory_rate');

            // Blood type
            $table->string('blood_type', 5)->nullable()->after('consciousness_level');

            // Medical history
            $table->text('known_allergies')->nullable()->after('blood_type');
            $table->text('existing_medical_conditions')->nullable()->after('known_allergies');
            $table->text('current_medications')->nullable()->after('existing_medical_conditions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('victims', function (Blueprint $table) {
            $table->dropColumn([
                'is_pregnant',
                'pregnancy_trimester',
                'pregnancy_complications',
                'expected_delivery_date',
                'age_category',
                'requires_special_care',
                'special_care_notes',
                'blood_pressure',
                'heart_rate',
                'temperature',
                'respiratory_rate',
                'consciousness_level',
                'blood_type',
                'known_allergies',
                'existing_medical_conditions',
                'current_medications',
            ]);
        });
    }
};
