<?php

namespace Tests\Feature\Booking;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailableRoomsTest extends TestCase
{
    use RefreshDatabase;

	//-----------------------------------------------------
    public function test_ask_for_available_rooms_without_parameters()
    {
        $response = $this->getJson('/api/available-rooms');

        $response->assertStatus(422)
            ->assertJson([
                'error_message' => 'Validation errors'
            ]);		
    }

	//-----------------------------------------------------
    public function test_ask_for_available_rooms_without_parameter_date()
    {
        $response = $this->getJson('/api/available-rooms?time=09:00');

        $response->assertStatus(422)
            ->assertJson([
                'error_message' => 'Validation errors'
            ]);		
    }

	//-----------------------------------------------------
    public function test_ask_for_available_rooms_without_parameter_time()
    {
        $response = $this->getJson('/api/available-rooms?date='.date('Y-m-d'));

        $response->assertStatus(422)
            ->assertJson([
                'error_message' => 'Validation errors'
            ]);
    }

	//-----------------------------------------------------
    public function test_ask_for_available_rooms_correct()
    {
        $response = $this->getJson('/api/available-rooms?date='.date('Y-m-d').'&time=10:00');

        $response->assertStatus(201)
            ->assertJsonPath('data.available_rooms', [1, 2, 3, 4, 5]);
    }

	//-----------------------------------------------------
    public function test_ask_for_available_rooms_v2_correct()
    {
        $response = $this->getJson('/api/available-rooms-v2?date='.date('Y-m-d').'&start_time=10:00&end_time=13:00');

        $response->assertStatus(201)
            ->assertJsonPath('data.available_rooms', [1, 2, 3, 4, 5]);
    }	
}