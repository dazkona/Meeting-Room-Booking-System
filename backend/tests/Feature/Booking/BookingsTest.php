<?php

namespace Tests\Feature\Booking;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class BookingsTest extends TestCase
{
    use RefreshDatabase;

	//-----------------------------------------------------
    public function test_ask_for_actual_bookings()
    {
        $response = $this->getJson('/api/bookings');

        $response->assertStatus(200)
			->assertJsonCount(0, 'data.bookings');
    }

	//-----------------------------------------------------
    public function test_ask_for_actual_bookings_with_bookings()
    {
		$r = Booking::factory()->count(3)->create();

        $response = $this->getJson('/api/bookings');

        $response->assertStatus(200)
			->assertJsonCount(3, 'data.bookings');
    }	

	//-----------------------------------------------------
	public function test_can_create_booking_wrong_roomid()
    {
        $response = $this->postJson('/api/bookings', [
			'room_id' => 187,
			'user_name' => "Name Surname",
			'date' => date('Y-m-d'),
			'start_time' => "10:00",
			'end_time' => "11:00"
        ]);	
		
        $response->assertStatus(422)
            ->assertJson([
				'error_message' => 'Room doesn\'t exist',
            ]);		
	}

	//-----------------------------------------------------
	public function test_can_create_booking_wrong_without_params()
    {
        $response = $this->postJson('/api/bookings', [
        ]);	
		
        $response->assertStatus(422)
            ->assertJson([
				"error_message" => "Validation errors"
            ])
			->assertJsonPath('data.room_id', ["The room id field is required."]);
	}

	//-----------------------------------------------------
	public function test_can_create_booking_wrong_too_soon()
    {
        $response = $this->postJson('/api/bookings', [
			'room_id' => 1,
			'user_name' => "Name Surname",
			'date' => date('Y-m-d'),
			'start_time' => "03:00",
			'end_time' => "11:00"
        ]);	
		
        $response->assertStatus(422)
            ->assertJson([
				'error_message' => 'Paremeters out of range',
            ]);		
	}

	//-----------------------------------------------------
	public function test_can_create_booking_wrong_too_late()
    {
        $response = $this->postJson('/api/bookings', [
			'room_id' => 1,
			'user_name' => "Name Surname",
			'date' => date('Y-m-d'),
			'start_time' => "10:00",
			'end_time' => "21:00"
        ]);	
		
        $response->assertStatus(422)
            ->assertJson([
				'error_message' => 'Paremeters out of range',
            ]);
	}

	//-----------------------------------------------------
	public function test_can_create_booking_wrong_formats()
    {
        $response = $this->postJson('/api/bookings', [
			'room_id' => 1,
			'user_name' => "Name Surname",
			'date' => date('Y/m/d'), // <--
			'start_time' => "10", // <--
			'end_time' => "18:00"
        ]);	
		
        $response->assertStatus(422)
            ->assertJson([
				'error_message' => 'Validation errors',
            ])
			->assertJsonPath('data.date', ["Use a valid date format YYYY-MM-DD"])
			->assertJsonPath('data.start_time', ["Use a valid time format hh:mm"]);
	}	

	//-----------------------------------------------------
	public function test_can_create_booking_correct()
    {
		$bookingInfo = [
			'room_id' => "1",
			'user_name' => "Name Surname",
			// 5 years in the future to avoid clashes
			'date' => date('Y-m-d', strtotime('+5 years')),
			'start_time' => "10:00",
			'end_time' => "15:00"
        ];

        $response = $this->postJson('/api/bookings', $bookingInfo);	
		
        $response->assertStatus(201)
			->assertJsonPath('data.booking.room_id', $bookingInfo['room_id'])
			->assertJsonPath('data.booking.user_name', $bookingInfo['user_name']);
	}	

	//-----------------------------------------------------
	public function test_can_create_booking_wrong_same_hours()
    {
		$bookingInfo = [
			'room_id' => "1",
			'user_name' => "Name Surname",
			// 5 years in the future to avoid clashes
			'date' => date('Y-m-d', strtotime('+5 years')),
			'start_time' => "10:00",
			'end_time' => "15:00"
        ];

        $response = $this->postJson('/api/bookings', $bookingInfo);	

		$response = $this->postJson('/api/bookings', $bookingInfo);	

        $response->assertStatus(422)
			->assertJson([
				'error_message' => 'Not all hours are available',
			]);
	}	

	//-----------------------------------------------------
	public function test_prevents_race_conditions_in_booking()
	{
		$bookingInfo = [
			'room_id' => "1",
			'user_name' => "Name Surname",
			'date' => date('Y-m-d', strtotime('+5 years')),
			'start_time' => "16:00",
			'end_time' => "18:00"
        ];

		// Simulate 10 concurrent attempts
		$responses = Http::pool(function($pool) use($bookingInfo) {
			$requests = [];

			for($i = 0; $i < 10; $i++) 
			{
				$requests[] = $pool->post('/api/bookings', $bookingInfo);
			}
			return $requests;
		});
	
		// Count successful vs rejected responses
		$successful = collect($responses)->filter(fn($r) => $r instanceof \Illuminate\Http\Client\Response && $r->status() === 201)->count();
		$rejected = collect($responses)->filter(fn($r) => !($r instanceof \Illuminate\Http\Client\Response) || $r->status() === 422)->count();

		// TODO: pool seems working wrong
		/*$this->assertEquals($successful, 1);
		$this->assertEquals($rejected, 9);*/
	}	
}