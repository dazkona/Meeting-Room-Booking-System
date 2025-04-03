<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $a = [
			'id' => (string)$this->id,
			'room_id' => (string)$this->room_id,
			'user_name' => $this->user_name,
			'date' => $this->date,
			'start_time' => $this->start_time,
			'end_time' => $this->end_time
		];

		if($this->room)
		{
			if(is_object($this->room))
				$a['room'] = $this->room->name;
			else
				$a['room'] = $this->room;
		}

		return $a;
    }
}
