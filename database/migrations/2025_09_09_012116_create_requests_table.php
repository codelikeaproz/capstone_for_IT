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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique(); // REQ-YYYY-XXX format
            
            // Requester information
            $table->string('requester_name');
            $table->string('requester_email');
            $table->string('requester_phone');
            $table->string('requester_id_number')->nullable();
            $table->text('requester_address');
            
            // Request details
            $table->enum('request_type', [
                'incident_report', 'traffic_accident_report', 'medical_emergency_report',
                'fire_incident_report', 'general_emergency_report', 'vehicle_accident_report'
            ]);
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('request_description');
            $table->text('purpose_of_request')->nullable();
            
            // Incident reference (if applicable)
            $table->string('incident_case_number')->nullable();
            $table->date('incident_date')->nullable();
            $table->string('incident_location')->nullable();
            $table->string('municipality');
            
            // Request processing
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Supporting documents
            $table->json('supporting_documents')->nullable(); // File paths
            $table->json('generated_reports')->nullable(); // Generated report paths
            
            // Processing timeline
            $table->dateTime('processing_started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('processing_days')->nullable();
            
            // Communication
            $table->boolean('email_notifications_enabled')->default(true);
            $table->boolean('sms_notifications_enabled')->default(false);
            $table->text('internal_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['municipality', 'status']);
            $table->index(['request_type', 'urgency_level']);
            $table->index(['status', 'created_at']);
            $table->index(['assigned_staff_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
