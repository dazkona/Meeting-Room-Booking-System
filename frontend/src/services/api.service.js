import axios from "axios";

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

export default {
  getBookings() {
    return apiClient.get("/api/bookings");
  },
  getRooms() {
    return apiClient.get("/api/rooms");
  },
  getAvailableRoomsV2(date, startTime, endTime) {
    return apiClient.get(
      `/api/available-rooms-v2?date=${date}&start_time=${startTime}&end_time=${endTime}`
    );
  },
  createBooking(bookingData) {
    return apiClient.post("/api/bookings", bookingData);
  },
};
