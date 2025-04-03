import { ref } from "vue";
import apiService from "@/services/api.service";

//-------------------------------------------------------------------
export function useBookNow(bookingInfo) {
  const data = ref(1);
  const isLoading = ref(false);
  const error = ref(null);

  //-------------------------------------------------------
  const postBooking = async (bookingInfo) => {
    isLoading.value = true;
    error.value = null;

    try {
      await apiService.createBooking(bookingInfo);

      data.value = "ok";
    } catch (err) {
      error.value = err.response.data.error_message;
    } finally {
      isLoading.value = false;
    }
  };

  //-------------------------------------------------------
  // Automatically fetch data when the composable is used
  if (bookingInfo) postBooking(bookingInfo);

  return {
    data,
    isLoading,
    error,
    postBooking,
  };
}
