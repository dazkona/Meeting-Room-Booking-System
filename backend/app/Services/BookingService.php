<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\HourGap;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Responses\JsonApiResponse;

//-------------------------------------------------------------------
class BookingService
{
	//-----------------------------------------------------
	// YYYY-MM-DD to DD/MM/YYYY
	protected function convertComingDateToDatabase($date)
	{
		return date('d/m/Y', strtotime($date));
	}

	//-----------------------------------------------------
	// HH:mm to HH:mm:ii
	protected function convertComingHourToDatabase($hour)
	{
		return $hour.":00";
	}	

	//-----------------------------------------------------
	protected function convertComingHourToInteger($hour)
	{
		$parts = explode(":", $hour);
		return intval($parts[0]);
	}

	//-----------------------------------------------------
	protected function getAllRoomIds()
	{
		$rooms = Room::all();

		$rooms = array_map(function($r) {
			return $r['id'];
		}, $rooms->toArray());

		return $rooms;
	}

	//-----------------------------------------------------
	protected function getAllReservedRoomIds($queryDate, $queryTime)
	{
		$reservedRoomsForDateTime = Booking::select('room_id')->where([
			['date', $queryDate],
			['start_time', '<=', $queryTime],
			['end_time', '>', $queryTime]
		])->get();

		$reservedRoomsForDateTime = array_map(function($r) {
			return $r['room_id'];
		}, $reservedRoomsForDateTime->toArray());

		return $reservedRoomsForDateTime;
	}

	//-----------------------------------------------------
	// Similar to the previous but filtering with enter time and exit time
	// instead of a single hour
	protected function getAllReservedRoomIdsWithEnterAndExit($queryDate, $queryStartTime, $queryEndTime)
	{
		$reservedRoomsForDateTime = Booking::select('room_id')->where(
			function($query) use($queryDate, $queryStartTime, $queryEndTime) {
				$query->where([
							['date', $queryDate],
							['start_time', '>=', $queryStartTime],
							['start_time', '<', $queryEndTime]
						])
						->orWhere(
							function($query2) use($queryDate, $queryStartTime, $queryEndTime) {
								$query2->where([
									['date', $queryDate],
									['start_time', '<', $queryStartTime],
									['end_time', '>', $queryStartTime],
									['end_time', '<=', $queryEndTime]
								]);
						});
			}
		)->get();


		$reservedRoomsForDateTime = array_map(function($r) {
			return $r['room_id'];
		}, $reservedRoomsForDateTime->toArray());

		return $reservedRoomsForDateTime;
	}	

	//-----------------------------------------------------
	protected function filterAvailableRoomIds($rooms, $reservedRoomsForDateTime)
	{
		$availableRooms = array_filter($rooms, function($roomId) use($reservedRoomsForDateTime) {
			return !in_array($roomId, $reservedRoomsForDateTime);
		});

		return $availableRooms;
	}

	//-----------------------------------------------------
	public function getAvailableRooms($request)
	{
		$queryDate = $this->convertComingDateToDatabase($request->get('date'));
		$queryTime = $this->convertComingHourToDatabase($request->get('time'));

		$rooms = $this->getAllRoomIds();
		$reservedRoomsForDateTime = $this->getAllReservedRoomIds($queryDate, $queryTime);

		$availableRooms = $this->filterAvailableRoomIds($rooms, $reservedRoomsForDateTime);

		return $availableRooms;
	}

	//-----------------------------------------------------
	// Similar to the previous but filtering with enter time and exit time
	// instead of a single hour	
	public function getAvailableRoomsWithEnterAndExit($request)
	{
		$queryDate = $this->convertComingDateToDatabase($request->get('date'));
		$queryStartTime = $this->convertComingHourToDatabase($request->get('start_time'));
		$queryEndTime = $this->convertComingHourToDatabase($request->get('end_time'));

		$rooms = $this->getAllRoomIds();
		$reservedRoomsForDateTime = $this->getAllReservedRoomIdsWithEnterAndExit($queryDate, $queryStartTime, $queryEndTime);

		$availableRooms = $this->filterAvailableRoomIds($rooms, $reservedRoomsForDateTime);

		return $availableRooms;
	}	

	//-----------------------------------------------------
    public function checkRequestParamsInsideAvailableHours($request)
    {
		$startTime = $this->convertComingHourToInteger($request->input('start_time'));
		$endTime = $this->convertComingHourToInteger($request->input('end_time'));

		if($startTime < 9 || $endTime > 18 || $startTime >= $endTime)
		{
			throw new HttpResponseException(
				JsonApiResponse::unprocessable('Paremeters out of range')->toResponse(null));			
		}		
	}

	//-----------------------------------------------------
    protected function initDailyHourGaps($day, $roomId)
    {
		$sql = HourGap::where([["date", $day], ["room_id", $roomId]])->toSql();

		$existingTodayGaps = HourGap::where([["date", $day], ["room_id", $roomId]])->count();

		if($existingTodayGaps < 1)
		{
			$todayGaps = [];
			for($h = 9; $h < 19; $h++)
				$todayGaps[] = ["room_id" => $roomId, 'user_name' => "", 'date' => $day, 'hour' => $h];

			$r = HourGap::insertOrIgnore($todayGaps);
		}
	}

	//-----------------------------------------------------
    public function checkRoomIdExists($roomId)
    {
		$room = Room::find($roomId);
		if(!$room)
		{
			throw new HttpResponseException(
				JsonApiResponse::unprocessable("Room doesn't exist")->toResponse(null));			
		}		
	}	

	//-----------------------------------------------------
	protected function getWantedHours($startTime, $endTime)
	{
		$startTimeI = $this->convertComingHourToInteger($startTime);
		$endTimeI = $this->convertComingHourToInteger($endTime);

		$wantedHours = [];
		for($h = $startTimeI; $h < $endTimeI; $h++)
			$wantedHours[] = $h;

		return $wantedHours;
	}

	//-----------------------------------------------------
	protected function tryToLockWantedHourGaps($day, $roomId, $wantedHours)
	{
		// try to block asked hours gap
		$lockedHourGaps = HourGap::where([
			['date', $day], 
			['room_id', $roomId],
			['booked', 0]
		])->whereIn('hour', $wantedHours)->lockForUpdate()->get();

		return $lockedHourGaps;
	}

	//-----------------------------------------------------
	protected function checkAllWantedHoursLocked($lockedHourGaps, $wantedHours)
	{
		// check again the hour gaps are not booked
		if($lockedHourGaps->count() != count($wantedHours))
		{
			throw new HttpResponseException(
				JsonApiResponse::unprocessable('Not all hours are available')->toResponse(null));			
		}
	}

	//-----------------------------------------------------
	protected function reserveLockedHours($lockedHourGaps, $userName)
	{
		$lockedHourGaps->each->update(['user_name' => $userName, 'booked' => 1]);
	}

	//-----------------------------------------------------
	protected function createBooking($roomId, $userName, $day, $startTime, $endTime)
	{
		// and create the booking
		$booking = Booking::create([
			'room_id' => $roomId,
			'user_name' => $userName,
			'date' => $day,
			'start_time' => $this->convertComingHourToDatabase($startTime),
			'end_time' => $this->convertComingHourToDatabase($endTime)
		]);

		return $booking;
	}

	//-----------------------------------------------------
    public function tryToBook($request)
    {
		$day = $request->input('date');
		$day = $this->convertComingDateToDatabase($day);
		$userName = $request->input('user_name');
		$roomId = $request->input('room_id');
		$startTime = $request->input('start_time');
		$endTime = $request->input('end_time');

		$this->checkRoomIdExists($roomId);

		$this->initDailyHourGaps($day, $roomId);

		$wantedHours = $this->getWantedHours($startTime, $endTime);

		DB::beginTransaction();

		try
		{
			// try to block asked hours gap
			$lockedHourGaps = $this->tryToLockWantedHourGaps($day, $roomId, $wantedHours);

			$this->checkAllWantedHoursLocked($lockedHourGaps, $wantedHours);

			$this->reserveLockedHours($lockedHourGaps, $userName);

			$booking = $this->createBooking($roomId, $userName, $day, $startTime, $endTime);

			DB::commit();

			return $booking;
		} 
		catch (\Exception $e) 
		{
        	DB::rollBack();
        
        	throw $e; // Re-throw for controller to handle
    	}
	}
}