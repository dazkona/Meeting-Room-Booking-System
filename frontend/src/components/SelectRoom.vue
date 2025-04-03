<template>
  <div class="block">
    <span>Choose the room: </span>
    <el-select
      v-model="room.valueRoom"
      placeholder="Select room"
      size="large"
      style="width: 240px"
      remote
      :remote-method="fetchRoomsData"
      :loading="loading"
    >
      <el-option v-for="room in rooms" :key="room.id" :label="room.name" :value="room.id" />
    </el-select>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useFetchRooms } from "@/composables/useFetchRooms";

//---------------------------------------------------------
defineProps({
  room: {
    type: Object,
    required: true,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
});

//---------------------------------------------------------
const { data: rooms, fetchData: fetchRoomsData } = useFetchRooms();
onMounted(fetchRoomsData);
</script>
