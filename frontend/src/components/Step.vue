<template>
  <el-card shadow="never">
    <h4>{{ title }}</h4>
    <slot />
  </el-card>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
});
</script>
