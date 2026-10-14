<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_code', 'first_name', 'last_name', 'email', 'phone', 'notes'
    ];

    public function stays()
    {
        return $this->hasMany(Stay::class);
    }

    public function activeStay()
    {
        return $this->hasOne(Stay::class)->where('status', 'active')->latest();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Helper: full name
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}