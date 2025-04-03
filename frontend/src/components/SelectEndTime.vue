<template>
  <div class="block">
    <span class="demonstration">Choose the end time: </span>
    <el-time-select
      v-model="endTime.valueEndTime"
      style="width: 240px"
      start="09:00"
      step="01:00"
      end="18:00"
      placeholder="Select time"
      format="HH:mm"
    />
  </div>
</template>

<script setup>
defineProps({
  endTime: {
    type: Object,
    required: true,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
});
</script>
