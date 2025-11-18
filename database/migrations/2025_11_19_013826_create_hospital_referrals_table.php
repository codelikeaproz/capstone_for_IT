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
        Schema::create('hospital_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('victim_id')->constrained('victims')->onDelete('cascade');
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('initial_hospital_id')->nullable()->constrained('hospitals')->onDelete('set null');
            $table->text('referral_reason')->nullable();
            $table->text('medical_notes')->nullable();
            $table->dateTime('transported_at')->nullable();
            $table->enum('status', ['pending', 'in_transit', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['victim_id', 'hospital_id']);
            $table->index('status');
        });

        // Backfill hospital_referrals from victims with hospital_referred data
        $victims = DB::table('victims')
            ->whereNotNull('hospital_referred')
            ->where('hospital_referred', '!=', '')
            ->get();

        foreach ($victims as $victim) {
            $hospital = DB::table('hospitals')
                ->where('hospital_name', trim($victim->hospital_referred))
                ->first();

            if ($hospital) {
                DB::table('hospital_referrals')->insert([
                    'victim_id' => $victim->id,
                    'hospital_id' => $hospital->id,
                    'initial_hospital_id' => null,
                    'referral_reason' => $victim->injury_description ?? 'Medical treatment required',
                    'medical_notes' => $victim->medical_treatment,
                    'transported_at' => $victim->hospital_arrival_time,
                    'status' => $victim->hospital_arrival_time ? 'completed' : 'pending',
                    'created_at' => $victim->created_at,
                    'updated_at' => $victim->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_referrals');
    }
};
