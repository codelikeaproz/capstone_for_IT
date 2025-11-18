<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_name');
            $table->string('contact_number')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('hospital_name');
            $table->index('status');
        });

        // Backfill hospitals from existing victims.hospital_referred data
        $hospitals = DB::table('victims')
            ->whereNotNull('hospital_referred')
            ->where('hospital_referred', '!=', '')
            ->distinct()
            ->pluck('hospital_referred');

        foreach ($hospitals as $hospitalName) {
            if (!empty(trim($hospitalName))) {
                DB::table('hospitals')->insertOrIgnore([
                    'hospital_name' => trim($hospitalName),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
