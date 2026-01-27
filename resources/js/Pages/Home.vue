<script setup>
defineProps({
  message: String,
})
</script>

<template>
  <div style="padding: 2rem">
    <h1>Inertia + Vue 3</h1>
    <p>{{ message }}</p>
  </div>
</template>
