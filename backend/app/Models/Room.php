<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function booking() 
    {
        return $this->hasMany(Booking::class);
    }	

	public function hourgap()
	{
		return $this->hasMany(HourGap::class);
	}
}
