<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingPostRequest;
use App\Http\Requests\AvailableRoomsGetRequest;
use App\Http\Requests\AvailableRoomsV2GetRequest;
use App\Http\Resources\BookingResource;
use App\Http\Responses\JsonApiResponse;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//-------------------------------------------------------------------
class BookingController extends Controller
{
	//-----------------------------------------------------
	public function __construct(private BookingService $bookingService) {}

	//-----------------------------------------------------
    public function index()
    {
		$bookings = Booking::all()->sortBy("start_time")->sortBy("date");

        return JsonApiResponse::ok(['bookings' => BookingResource::collection($bookings)]);
    }

	//-----------------------------------------------------
    public function store(BookingPostRequest $request)
    {
		$this->bookingService->checkRequestParamsInsideAvailableHours($request);

		$booking = $this->bookingService->tryToBook($request);

        return JsonApiResponse::created(['booking' => new BookingResource($booking)]);
    }

	//-----------------------------------------------------
	public function availableRooms(AvailableRoomsGetRequest $request)
	{
		if($request->has('date') && $request->has('time'))
		{
			$availableRooms = $this->bookingService->getAvailableRooms($request);

			return JsonApiResponse::created(['available_rooms' => array_values($availableRooms)]);
		}
	}

	//-----------------------------------------------------
	public function availableRoomsV2(AvailableRoomsV2GetRequest $request)
	{
		$availableRooms = $this->bookingService->getAvailableRoomsWithEnterAndExit($request);

		return JsonApiResponse::created(['available_rooms' => array_values($availableRooms)]);
	}
}
