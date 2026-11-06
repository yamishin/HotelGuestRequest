<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code', 'stay_id', 'title', 'description',
        'department', 'status', 'assigned_to',
        'routed_by', 'routed_at', 'rejection_reason',
        'completed_at', 'staff_notes',
        'resolved_by', 'resolved_at',
        'marked_unresolved', 'unresolved_reason',
        'reassignment_count',
    ];

    protected $casts = [
        'completed_at'       => 'datetime',
        'routed_at'          => 'datetime',
        'resolved_at'        => 'datetime',
        'marked_unresolved'  => 'boolean',
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

    public function router()
    {
        return $this->belongsTo(User::class, 'routed_by');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /* ============================================================
     |  STATUS LABEL
     ============================================================ */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Pending Review',
            'routed'      => 'Routed to Department',
            'assigned'    => 'Assigned to Staff',
            'in_progress' => 'In Progress',
            'completed'   => 'Awaiting Supervisor Review',
            'resolved'    => 'Resolved ✓',
            'rejected'    => 'Rejected',
            'unavailable' => 'Unavailable',
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
            'routed'      => 'info',
            'assigned'    => 'info',
            'in_progress' => 'warning',
            'completed'   => 'orange',
            'resolved'    => 'success',
            'rejected'    => 'grey',
            'unavailable' => 'grey',
            default       => 'grey',
        };
    }

    /* ============================================================
     |  NOTIFY GUEST (helper)
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
     |  NOTIFY SUPERVISOR (helper)
     ============================================================ */
    public function notifySupervisor(string $title, string $message): void
    {
        \App\Models\Notification::create([
            'type'        => 'request',
            'title'       => $title,
            'message'     => $message,
            'target_role' => 'supervisor',
            'is_read'     => false,
        ]);
    }

    /* ============================================================
     |  NOTIFY SPECIFIC STAFF MEMBER (helper)
     ============================================================ */
    public function notifyStaff(string $title, string $message): void
    {
        if (!$this->assigned_to) return;
        \App\Models\Notification::create([
            'type'        => 'request',
            'title'       => $title,
            'message'     => $message,
            'target_role' => 'staff:' . $this->assigned_to,
            'is_read'     => false,
        ]);
    }

    /* ============================================================
     |  FRONT DESK: Accept + Route to Department
     ============================================================ */
    public function acceptByFrontDesk(int $frontDeskId, ?string $department = null): void
    {
        $this->update([
            'status'      => 'routed',
            'routed_by'   => $frontDeskId,
            'routed_at'   => now(),
            'department'  => $department ?? $this->department,
        ]);

        $this->notifySupervisor(
            'Request Routed to Department',
            "Room {$this->stay->room->room_number} — {$this->title} routed to {$this->department}"
        );

        $this->notifyGuest(
            'Request Accepted',
            "Your request \"{$this->title}\" has been accepted and sent to {$this->department}."
        );
    }

    /* ============================================================
     |  FRONT DESK: Reject
     ============================================================ */
    public function rejectByFrontDesk(int $frontDeskId, string $reason): void
    {
        $this->update([
            'status'           => 'rejected',
            'routed_by'        => $frontDeskId,
            'routed_at'        => now(),
            'rejection_reason' => $reason,
        ]);

        $this->notifyGuest(
            'Request Rejected',
            "Your request \"{$this->title}\" was rejected. Reason: {$reason}"
        );
    }

    /* ============================================================
     |  FRONT DESK: Mark Unavailable
     ============================================================ */
    public function markUnavailable(int $frontDeskId, string $reason): void
    {
        $this->update([
            'status'           => 'unavailable',
            'routed_by'        => $frontDeskId,
            'routed_at'        => now(),
            'rejection_reason' => $reason,
        ]);

        $this->notifyGuest(
            'Service Unavailable',
            "Your request \"{$this->title}\" could not be fulfilled. Reason: {$reason}"
        );
    }

    /* ============================================================
     |  SUPERVISOR: Assign to Staff
     ============================================================ */
    public function assignToStaff(int $staffId): void
    {
        $wasReassignment = $this->assigned_to !== null && $this->assigned_to !== $staffId;

        $this->update([
            'status'      => 'assigned',
            'assigned_to' => $staffId,
        ]);

        if ($wasReassignment) {
            $this->increment('reassignment_count');
        }

        $this->notifyStaff(
            $wasReassignment ? 'Task Reassigned' : 'New Task Assigned',
            "Room {$this->stay->room->room_number} — {$this->title}"
        );
    }

    /* ============================================================
     |  STAFF: Start Task
     ============================================================ */
    public function startByStaff(): void
    {
        if ($this->status === 'in_progress') return;

        $this->update(['status' => 'in_progress']);

        $this->notifyGuest(
            'Service In Progress',
            "Our staff is now working on: \"{$this->title}\"."
        );
    }

    /* ============================================================
     |  STAFF: Mark Completed (with notes)
     |  Notifies supervisor for final review. Does NOT notify guest yet.
     ============================================================ */
    public function completeByStaff(?string $notes = null): void
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'staff_notes'  => $notes ?? $this->staff_notes,
        ]);

        $this->notifySupervisor(
            'Task Awaiting Review',
            "Room {$this->stay->room->room_number} — {$this->title} completed by staff. Please review."
        );
    }

    /* ============================================================
     |  STAFF: Mark Could Not Resolve
     |  Sets back to in_progress, flags for supervisor follow-up.
     ============================================================ */
    public function markUnresolvedByStaff(string $reason): void
    {
        $this->update([
            'status'            => 'in_progress',
            'marked_unresolved' => true,
            'unresolved_reason' => $reason,
            'completed_at'      => null,
        ]);

        $this->notifySupervisor(
            'Task Flagged Unresolved',
            "Room {$this->stay->room->room_number} — {$this->title}: {$reason}"
        );
    }

    /* ============================================================
     |  SUPERVISOR: Confirm Resolved
     ============================================================ */
        public function confirmResolved(int $supervisorId): void
    {
        $this->update([
            'status'            => 'resolved',
            'resolved_by'       => $supervisorId,
            'resolved_at'       => now(),
            'marked_unresolved' => false,
        ]);

        // AUTO-UPDATE ROOM STATUS IF IT WAS A CLEANING REQUEST
        if ($this->title === 'Room Cleaning & Turnover' && $this->stay->room) {
            $this->stay->room->update(['status' => 'available']);
        }

        $this->notifyGuest(
            'Request Resolved',
            "Your request \"{$this->title}\" has been resolved. Thank you!"
        );
    }

    /* ============================================================
     |  SUPERVISOR: Mark Unresolved (send back to staff)
     ============================================================ */
    public function markUnresolvedBySupervisor(string $reason): void
    {
        $this->update([
            'status'            => 'in_progress',
            'marked_unresolved' => true,
            'unresolved_reason' => $reason,
            'completed_at'      => null,
        ]);

        $this->notifyStaff(
            'Task Returned for Follow-up',
            "Room {$this->stay->room->room_number} — {$this->title}: {$reason}"
        );
    }

    /* ============================================================
     |  SUPERVISOR: Reassign to Different Staff
     ============================================================ */
    public function reassignTo(int $newStaffId, int $supervisorId): void
    {
        $oldStaff = $this->assigned_to;

        $this->update([
            'assigned_to' => $newStaffId,
            'status'      => 'assigned',
            'completed_at'=> null,
        ]);

        $this->increment('reassignment_count');

        $this->notifyStaff(
            'Task Assigned to You',
            "Room {$this->stay->room->room_number} — {$this->title} (reassigned)"
        );
    }

    /* ============================================================
     |  LEGACY COMPATIBILITY — deprecated but kept to avoid
     |  breaking old code paths (calls will be silently ignored
     |  if status doesn't match)
     ============================================================ */
    public function updateStatus(string $newStatus): void
    {
        // Kept for backward compatibility — new code uses the
        // explicit methods above (acceptByFrontDesk, assignToStaff, etc.)
        $this->update(['status' => $newStatus]);
    }

        /* ============================================================
     |  AUTO-DETECT DEPARTMENT (For Front Desk Forwarding)
     ============================================================ */
    public function getSuggestedDepartmentAttribute(): string
    {
        // If the guest already selected a department, use it
        if ($this->department && $this->department !== 'Unassigned') {
            return $this->department;
        }

        $title = strtolower($this->title);

        if (str_contains($title, 'wifi') || str_contains($title, 'wi-fi') || str_contains($title, 'tv') || str_contains($title, 'telephone') || str_contains($title, 'internet')) {
            return 'IT';
        }
        
        if (str_contains($title, 'ac') || str_contains($title, 'aircon') || str_contains($title, 'plumb') || str_contains($title, 'leak') || str_contains($title, 'electric') || str_contains($title, 'door') || str_contains($title, 'light')) {
            return 'Maintenance';
        }
        
        if (str_contains($title, 'towel') || str_contains($title, 'pillow') || str_contains($title, 'clean') || str_contains($title, 'toiletries')) {
            return 'Housekeeping';
        }

        return 'Front Desk';
    }
}