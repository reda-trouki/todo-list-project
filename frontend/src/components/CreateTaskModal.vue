<template>
  <TaskModal 
    :task="null"
    :is-editing="false"
    @close="$emit('close')"
    @saved="$emit('created')"
  />
</template>

<script setup lang="ts">
import TaskModal from './TaskModal.vue'

defineEmits<{
  close: []
  created: []
}>()
</script>
