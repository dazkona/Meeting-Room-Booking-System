<template>
  <div class="block">
    <el-text class="mx-1" type="warning" v-show="!canCheckAvailability"
      >First, you need to fill the fields above to be able to continue booking.</el-text
    >
  </div>
  <el-card shadow="never">
    <span class="demonstration">Check Availability </span>
    <template v-if="isLoading">
      <el-button
        type="primary"
        plain
        v-bind:disabled="!canCheckAvailability || isLoading"
        @click="checkAvailability"
        loading
        >Checking</el-button
      >
    </template>
    <template v-else>
      <el-button
        type="primary"
        plain
        v-bind:disabled="!canCheckAvailability"
        @click="checkAvailability"
        >Check</el-button
      >
    </template>
  </el-card>
  <div class="block" v-show="showAvailableMessage">
    <el-icon :size="20" color="#409efc"><DocumentChecked /></el-icon
    ><el-text class="mx-1" type="primary"> You can book! Continue to the next step...</el-text>
  </div>
  <div class="block" v-show="showUnavailableMessage">
    <el-icon :size="20" color="#f21746"><DocumentDelete /></el-icon
    ><el-text class="mx-1" type="error"> This room is not available for these hours</el-text>
  </div>
  <div class="block" v-show="showErrorMessage">
    <el-icon :size="20" color="#f21746"><DocumentDelete /></el-icon
    ><el-text class="mx-1" type="error"> {{ errorMessage }}</el-text>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useFetchAvailableRoomsV2 } from "@/composables/useFetchAvailableRoomsV2";

//---------------------------------------------------------
const props = defineProps({
  bookingData: {
    type: Object,
    required: true,
  },
  canCheckAvailability: {
    type: Boolean,
    required: true,
    default: false,
  },
});

//---------------------------------------------------------
// checked is the value which indicates if we must show a status message
// after at least one availibility check
const checked = ref(false);
const errorMessage = ref("");

const emits = defineEmits(["onChangeAvailability"]);

//---------------------------------------------------------
const { data: roomsData, fetchData: fetchAvailableRoomsV2, isLoading } = useFetchAvailableRoomsV2();

//---------------------------------------------------------
const showAvailableMessage = computed(() => {
  return props.bookingData.isAvailable && checked.value;
});
const showUnavailableMessage = computed(() => {
  return !props.bookingData.isAvailable && checked.value;
});
const showErrorMessage = computed(() => {
  return errorMessage.value !== "";
});

//---------------------------------------------------------
const updateAvailability = (availableRooms) => {
  // as we receive the available rooms list, we have to compare with the room selected by the user
  const isAvailable = availableRooms.includes(parseInt(props.bookingData.valueRoom));

  checked.value = true;

  emits("onChangeAvailability", isAvailable);
};

//---------------------------------------------------------
const removeAvailability = () => {
  emits("onChangeAvailability", false);
};

//---------------------------------------------------------
const checkAvailability = async () => {
  try {
    errorMessage.value = "";

    await fetchAvailableRoomsV2(
      props.bookingData.valueDate,
      props.bookingData.valueStartTime,
      props.bookingData.valueEndTime
    );

    updateAvailability(roomsData.value);
  } catch (err) {
    removeAvailability();

    errorMessage.value =
      "End Time must be greater than Start Time, and the Date has to be on the future.";
  } finally {
  }
};

//---------------------------------------------------------
const onBooked = async () => {
  checked.value = false;
  errorMessage.value = "";
};

//---------------------------------------------------------
defineExpose({ onBooked });
</script>
