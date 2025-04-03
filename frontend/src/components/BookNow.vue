<template>
  <div class="block">
    <el-text class="mx-1" type="warning" v-show="!canBook"
      >You need to check the availability and write your name to be able to book a room.</el-text
    >
  </div>
  <el-card shadow="never">
    <div class="block">
      <span>Write your name: </span>
      <el-input v-model="bookingData.inputName" placeholder="Your name" class="inputName" />
    </div>
    <div class="block">
      <span>Book Now </span>
      <template v-if="isLoading">
        <el-button type="success" plain v-bind:disabled="!canBook" @click="tryToBook" loading
          >Booking...</el-button
        >
      </template>
      <template v-else>
        <el-button type="success" plain v-bind:disabled="!canBook" @click="tryToBook"
          >Book Now</el-button
        >
      </template>
    </div>
    <div class="block" v-show="errorMsg !== ''">
      <el-icon :size="20" color="#f21746"><WarningFilled /></el-icon
      ><el-text class="mx-1" type="error">{{ errorMsg }}</el-text>
    </div>
    <div class="block" v-show="successMsg !== ''">
      <el-icon :size="20" color="#409efc"><SuccessFilled /></el-icon
      ><el-text class="mx-1" type="primary">{{ successMsg }}</el-text>
    </div>
  </el-card>
</template>

<script setup>
import { ref, computed } from "vue";
import { useBookNow } from "@/composables/useBookNow";

//---------------------------------------------------------
const props = defineProps({
  bookingData: {
    type: Object,
    required: true,
  },
});

//---------------------------------------------------------
const successMsg = ref("");
const errorMsg = ref("");

const emits = defineEmits(["onBooked"]);

//---------------------------------------------------------
const { postBooking, isLoading, error: postBookingError } = useBookNow(null);

//---------------------------------------------------------
const canBook = computed(() => {
  return !!props.bookingData.isAvailable && props.bookingData.inputName !== "";
});

//---------------------------------------------------------
const onSuccessBooking = () => {
  emits("onBooked");
};

//---------------------------------------------------------
const tryToBook = async () => {
  try {
    errorMsg.value = "";

    await postBooking({
      room_id: props.bookingData.valueRoom,
      user_name: props.bookingData.inputName,
      date: props.bookingData.valueDate,
      start_time: props.bookingData.valueStartTime,
      end_time: props.bookingData.valueEndTime,
    });

    successMsg.value = "You have succesfully booked the room!";

    onSuccessBooking();
  } catch (err) {
    errorMsg.value = postBookingError.value;
  } finally {
  }
};

//---------------------------------------------------------
const onCleanFeedback = async () => {
  errorMsg.value = "";
  successMsg.value = "";
};

//---------------------------------------------------------
defineExpose({ onCleanFeedback });
</script>
