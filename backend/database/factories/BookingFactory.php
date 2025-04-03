<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
			'room_id' => random_int(1, 5),
			'user_name' => Str::random(10),
			'date' => date('d/m/Y'),
			'start_time' => random_int(10, 13).":00",
			'end_time' => random_int(14, 18).":00"
        ];
    }
}
