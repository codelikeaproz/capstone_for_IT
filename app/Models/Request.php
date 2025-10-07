<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Request extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'request_number',
        'requester_name',
        'requester_email',
        'requester_phone',
        'requester_id_number',
        'requester_address',
        'request_type',
        'urgency_level',
        'request_description',
        'purpose_of_request',
        'incident_case_number',
        'incident_date',
        'incident_location',
        'municipality',
        'status',
        'assigned_staff_id',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'supporting_documents',
        'generated_reports',
        'processing_started_at',
        'completed_at',
        'processing_days',
        'email_notifications_enabled',
        'sms_notifications_enabled',
        'internal_notes',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'approved_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'completed_at' => 'datetime',
        'supporting_documents' => 'array',
        'generated_reports' => 'array',
        'email_notifications_enabled' => 'boolean',
        'sms_notifications_enabled' => 'boolean',
        'processing_days' => 'integer',
    ];

    // Activity logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'assigned_staff_id', 'approved_by', 'urgency_level'])
            ->logOnlyDirty();
    }

    // Relationships
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }

    public function scopeByMunicipality($query, $municipality)
    {
        return $query->where('municipality', $municipality);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['approved', 'completed']);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'processing' => 'badge-info',
            'approved' => 'badge-success',
            'rejected' => 'badge-error',
            'completed' => 'badge-success',
            default => 'badge-ghost'
        };
    }

    public function getUrgencyColorAttribute()
    {
        return match($this->urgency_level) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y H:i') : null;
    }

    public function getProcessingTimeAttribute()
    {
        if ($this->processing_started_at && $this->completed_at) {
            return $this->processing_started_at->diffInDays($this->completed_at);
        }
        return null;
    }

    // Methods
    public static function generateRequestNumber()
    {
        $year = now()->year;
        $lastRequest = self::where('request_number', 'like', "REQ-{$year}-%")
                          ->orderBy('id', 'desc')
                          ->first();
        
        if ($lastRequest) {
            $lastNumber = intval(substr($lastRequest->request_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "REQ-{$year}-{$newNumber}";
    }

    public function assignToStaff($staffId)
    {
        $this->update([
            'assigned_staff_id' => $staffId,
            'status' => 'processing',
            'processing_started_at' => now(),
        ]);
    }

    public function approve($approvedBy, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    public function reject($rejectedBy, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $rejectedBy,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'processing_days' => $this->processing_started_at ? 
                now()->diffInDays($this->processing_started_at) : null,
        ]);
    }
}
