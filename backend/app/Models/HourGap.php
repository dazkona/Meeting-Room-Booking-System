<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HourGap extends Model
{
	protected $fillable = ['user_name', 'date', 'hour', 'room_id', 'booked'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
