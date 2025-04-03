<script setup>
import { reactive, computed, ref } from "vue";

import Step from "./components/Step.vue";
import SelectRoom from "./components/SelectRoom.vue";
import SelectDate from "./components/SelectDate.vue";
import SelectStartTime from "./components/SelectStartTime.vue";
import SelectEndTime from "./components/SelectEndTime.vue";
import CheckAvailability from "./components/CheckAvailability.vue";
import BookNow from "./components/BookNow.vue";
import BookingsTable from "./components/BookingsTable.vue";

//---------------------------------------------------------
const checkAvailabilityRef = ref(null);
const bookingsTableRef = ref(null);
const bookNowRef = ref(null);

//---------------------------------------------------------
const formData = reactive({
  data: {
    valueRoom: "",
    valueDate: "",
    valueStartTime: "",
    valueEndTime: "",
    inputName: "",
    isAvailable: false,
    errors: {},
  },
});

//---------------------------------------------------------
// Flag who activates when the first group of fields are completed by the user
const canCheckAvailability = computed(() => {
  return (
    formData.data.valueRoom !== "" &&
    formData.data.valueDate !== "" &&
    formData.data.valueStartTime !== "" &&
    formData.data.valueEndTime !== ""
  );
});

//---------------------------------------------------------
const onChangeAvailability = (isAvailable) => {
  formData.data.isAvailable = isAvailable;
};

//---------------------------------------------------------
const cleanFormData = () => {
  formData.data.valueRoom = "";
  formData.data.valueDate = "";
  formData.data.valueStartTime = "";
  formData.data.valueEndTime = "";
  formData.data.inputName = "";
  formData.data.isAvailable = false;

  checkAvailabilityRef.value.onBooked();
};

//---------------------------------------------------------
const onBooked = async () => {
  await bookingsTableRef.value.updateData();

  // clean form data
  setTimeout(() => {
    cleanFormData();
  }, 2000);

  // clean feedback messages at Book Now zone
  setTimeout(() => {
    bookNowRef.value.onCleanFeedback();
  }, 5000);
};
</script>

<template>
  <div class="common-layout">
    <el-container>
      <el-header><h2>Meeting Room Booking System</h2></el-header>
      <el-main>
        <Step title="Step 1">
          <SelectRoom v-model:room="formData.data" v-model:errors="formData.data.errors" />
          <SelectDate v-model:date="formData.data" v-model:errors="formData.data.errors" />
          <SelectStartTime
            v-model:startTime="formData.data"
            v-model:errors="formData.data.errors"
          />
          <SelectEndTime v-model:endTime="formData.data" v-model:errors="formData.data.errors" />
        </Step>
        <Step title="Step 2">
          <CheckAvailability
            v-model:bookingData="formData.data"
            :canCheckAvailability="canCheckAvailability"
            @on-change-availability="onChangeAvailability"
            ref="checkAvailabilityRef"
          />
        </Step>
        <Step title="Step 3"
          ><BookNow v-model:bookingData="formData.data" @on-booked="onBooked" ref="bookNowRef"
        /></Step>
        <BookingsTable ref="bookingsTableRef" />
      </el-main>
    </el-container>
  </div>
</template>

<style scoped>
.common-layout {
  margin: 0;
  background-color: rgb(184, 216, 253);
}
.common-layout h2 {
  color: #11567e;
}
.el-main .el-card:not(:first-child) {
  margin-top: 1vh;
}
</style>
