<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number', 'floor', 'type', 'status', 'notes'
    ];

    public function stays()
    {
        return $this->hasMany(Stay::class);
    }

    public function currentStay()
    {
        return $this->hasOne(Stay::class)->where('status', 'active')->latest();
    }
}