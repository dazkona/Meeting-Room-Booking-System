<template>
  <div class="block">
    <span class="demonstration">Choose the day: </span>
    <el-date-picker
      v-model="date.valueDate"
      type="date"
      placeholder="Pick a day"
      value-format="YYYY-MM-DD"
    />
  </div>
</template>

<script setup>
defineProps({
  date: {
    type: Object,
    required: true,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
});
</script>
