import { ref } from "vue";
import apiService from "@/services/api.service";

//-------------------------------------------------------------------
export function useFetchRooms() {
  const data = ref(null);
  const isLoading = ref(false);
  const error = ref(null);

  //-------------------------------------------------------
  const fetchData = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiService.getRooms();
      data.value = response.data.data.rooms;
    } catch (err) {
      error.value = err.response ? err.response.data.message : err.message;
    } finally {
      isLoading.value = false;
    }
  };

  //-------------------------------------------------------
  // Automatically fetch data when the composable is used
  fetchData();

  return {
    data,
    isLoading,
    error,
    fetchData,
  };
}
