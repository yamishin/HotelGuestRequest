<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code', 'stay_id', 'title', 'description',
        'department', 'status', 'assigned_to', 'completed_at',
        'guest_confirmed_at', 'staff_notes', 'returned_by_guest',
    ];

    protected $casts = [
        'completed_at'       => 'datetime',
        'guest_confirmed_at' => 'datetime',
        'returned_by_guest'  => 'boolean',
    ];

    /* ============================================================
     |  RELATIONSHIPS
     ============================================================ */

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /* ============================================================
     |  NOTIFY GUEST
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
     |  STATUS LABEL
     ============================================================ */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Pending',
            'assigned'    => 'Assigned to Staff',
            'in_progress' => 'In Progress',
            'completed'   => $this->guest_confirmed_at ? 'Verified' : 'Awaiting Confirmation',
            'verified'    => 'Verified ✓',
            'cancelled'   => 'Cancelled',
            default       => ucfirst($this->status),
        };
    }

    /* ============================================================
     |  STATUS COLOR
     ============================================================ */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'danger',
            'assigned'    => 'info',
            'in_progress' => 'warning',
            'completed'   => 'orange',
            'verified'    => 'success',
            'cancelled'   => 'grey',
            default       => 'grey',
        };
    }

    /* ============================================================
     |  UPDATE STATUS + NOTIFY GUEST
     |  (used for 'assigned' and 'in_progress')
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
     |  STAFF MARKS COMPLETED (with optional notes)
     |  Notifies guest to CONFIRM + notifies supervisor
     ============================================================ */
    public function markCompleted(?string $notes = null): void
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'staff_notes'  => $notes ?? $this->staff_notes,
        ]);

        // Notify guest — awaiting their confirmation
        \App\Models\Notification::create([
            'type'        => 'request',
            'title'       => 'Please Confirm Completion',
            'message'     => "Your request \"{$this->title}\" has been marked as done. Please confirm if it's resolved.",
            'target_role' => 'guest',
            'is_read'     => false,
        ]);

        // Notify supervisor
        \App\Models\Notification::create([
            'type'        => 'request',
            'title'       => 'Task Awaiting Guest Confirmation',
            'message'     => "{$this->title} (Room {$this->stay->room->room_number}) awaiting guest confirmation",
            'target_role' => 'supervisor',
            'is_read'     => false,
        ]);
    }

    /* ============================================================
     |  GUEST CONFIRMS — service verified
     ============================================================ */
    public function confirmByGuest(): void
    {
        $this->update([
            'status'             => 'verified',
            'guest_confirmed_at' => now(),
            'returned_by_guest'  => false,
        ]);

        // Notify staff
        if ($this->assigned_to) {
            \App\Models\Notification::create([
                'type'        => 'request',
                'title'       => 'Guest Confirmed',
                'message'     => "Room {$this->stay->room->room_number} confirmed: {$this->title}",
                'target_role' => 'staff:' . $this->assigned_to,
                'is_read'     => false,
            ]);
        }

        // Notify supervisor
        \App\Models\Notification::create([
            'type'        => 'request',
            'title'       => 'Service Verified',
            'message'     => "Room {$this->stay->room->room_number} confirmed {$this->title} is resolved",
            'target_role' => 'supervisor',
            'is_read'     => false,
        ]);
    }

    /* ============================================================
     |  GUEST REJECTS — returns to staff for follow-up
     ============================================================ */
    public function rejectByGuest(?string $reason = null): void
    {
        $this->update([
            'status'            => 'in_progress',
            'returned_by_guest' => true,
            'completed_at'      => null,
        ]);

        $message = "Room {$this->stay->room->room_number} reports \"{$this->title}\" is NOT resolved.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        // Notify staff
        if ($this->assigned_to) {
            \App\Models\Notification::create([
                'type'        => 'request',
                'title'       => 'Guest Reported Issue',
                'message'     => $message,
                'target_role' => 'staff:' . $this->assigned_to,
                'is_read'     => false,
            ]);
        }

        // Notify supervisor
        \App\Models\Notification::create([
            'type'        => 'request',
            'title'       => 'Request Returned by Guest',
            'message'     => $message,
            'target_role' => 'supervisor',
            'is_read'     => false,
        ]);
    }
}