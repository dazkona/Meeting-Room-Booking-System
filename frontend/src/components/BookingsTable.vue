<script setup>
import { useFetchBookingsData } from "@/composables/useFetchBookingsData";

//---------------------------------------------------------
const { data: bookingData, fetchData: fetchBookingData } = useFetchBookingsData();

const updateData = async () => {
  await fetchBookingData();
};

//---------------------------------------------------------
defineExpose({ updateData });
</script>

<template>
  <el-card shadow="never">
    <h4>Actual bookings</h4>
    <el-table :data="bookingData" border style="width: 50%" class="bookingTable">
      <el-table-column prop="date" label="Date" />
      <el-table-column prop="start_time" label="Start" />
      <el-table-column prop="end_time" label="End" />
      <el-table-column prop="user_name" label="Name" />
      <el-table-column prop="room" label="Room" />
    </el-table>
  </el-card>
</template>

<style scoped></style>
