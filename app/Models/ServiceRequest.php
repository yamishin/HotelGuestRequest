<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code', 'stay_id', 'title', 'description',
        'department', 'status', 'assigned_to', 'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    /* ============================================================
     |  Notify the guest about a status change
     ============================================================ */
    public function notifyGuest(string $title, string $message): void
    {
        \App\Models\Notification::create([
            'type'        => 'request',
            'title'       => $title,
            'message'     => $message,
            'target_role' => 'guest',
            'is_read'     => false,
        ]);
    }

    /* ============================================================
     |  Human-readable status label
     ============================================================ */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Pending',
            'assigned'    => 'Assigned to Staff',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
            default       => ucfirst($this->status),
        };
    }
    /* ============================================================
     |  Update status + notify guest
     ============================================================ */
    public function updateStatus(string $newStatus): void
    {
        $oldStatus = $this->status;
        if ($oldStatus === $newStatus) {
            return;
        }

        $this->update([
            'status'       => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null,
        ]);

        // Notify the guest with a context-aware message
        $roomNumber = $this->stay->room->room_number ?? 'your room';
        $title = match ($newStatus) {
            'assigned'    => 'Request Assigned',
            'in_progress' => 'Request In Progress',
            'completed'   => 'Request Completed',
            'cancelled'   => 'Request Cancelled',
            default       => 'Request Updated',
        };

        $message = match ($newStatus) {
            'assigned'    => "Your request \"{$this->title}\" has been assigned to a staff member.",
            'in_progress' => "A staff member is now working on your request: \"{$this->title}\".",
            'completed'   => "Your request \"{$this->title}\" has been completed. Thank you!",
            'cancelled'   => "Your request \"{$this->title}\" was cancelled. Please contact the front desk if this was a mistake.",
            default       => "Your request \"{$this->title}\" status has been updated.",
        };

        $this->notifyGuest($title, $message);
    }

        /* ============================================================
     |  Status badge color for UI
     ============================================================ */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'danger',
            'assigned'    => 'info',
            'in_progress' => 'warning',
            'completed'   => 'success',
            'cancelled'   => 'grey',
            default       => 'grey',
        };
    }
}