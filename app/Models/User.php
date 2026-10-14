<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

        protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'phone',
        'employee_code',
        'hire_date',
        'shift',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hire_date' => 'date',
        ];
    }

    /* ============================================================
     |  ROLE HELPERS
     ============================================================ */

    public function isFrontDesk(): bool
    {
        return $this->role === 'frontdesk';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /* ============================================================
     |  STATUS HELPERS
     ============================================================ */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 2));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'     => 'success',
            'inactive'   => 'warning',
            'terminated' => 'grey',
            default      => 'grey',
        };
    }

    /* ============================================================
     |  RELATIONSHIPS
     ============================================================ */

    public function assignedRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_to');
    }

    public function activeRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_to')
            ->whereIn('status', ['assigned', 'in_progress']);
    }
}