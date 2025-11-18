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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_title');
            $table->text('report_content')->nullable();
            $table->enum('report_type', ['incident_summary', 'monthly_report', 'annual_report', 'custom'])->default('incident_summary');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('report_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->index('report_type');
            $table->index('report_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
