import { ref } from "vue";
import apiService from "@/services/api.service";

//-------------------------------------------------------------------
export function useFetchAvailableRoomsV2(date, startTime, endTime) {
  const data = ref(1);
  const isLoading = ref(false);
  const error = ref(null);

  //-------------------------------------------------------
  const fetchData = async (date, startTime, endTime) => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiService.getAvailableRoomsV2(date, startTime, endTime);

      data.value = response.data.data.available_rooms;
    } catch (err) {
      error.value = err.response ? err.response.data.message : err.message;
    } finally {
      isLoading.value = false;
    }
  };

  //-------------------------------------------------------
  // Automatically fetch data when the composable is used
  if (date && startTime && endTime) fetchData(date, startTime, endTime);

  return {
    data,
    isLoading,
    error,
    fetchData,
  };
}
