<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'user_name', 'date', 'start_time', 'end_time'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
