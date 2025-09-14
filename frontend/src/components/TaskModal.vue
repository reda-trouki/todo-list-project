<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative w-full max-w-md mx-auto bg-white rounded-lg shadow-xl">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          {{ isEditing ? 'Edit Task' : 'Create New Task' }}
        </h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-500 transition-colors"
        >
          <X class="h-5 w-5" />
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
        <div v-if="error" class="bg-red-50 border border-red-200 rounded-md p-3">
          <p class="text-red-600 text-sm">{{ error }}</p>
        </div>

        <!-- Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
            Title *
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Enter task title"
          />
        </div>

        <!-- Description -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Description
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Enter task description (optional)"
          ></textarea>
        </div>

        <!-- Status and Priority Row -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
              Status
            </label>
            <select
              id="status"
              v-model="form.status"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>

          <!-- Priority -->
          <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
              Priority
            </label>
            <select
              id="priority"
              v-model="form.priority"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>
        </div>

        <!-- Due Date and Assignment Row -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Due Date -->
          <div>
            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
              Due Date
            </label>
            <input
              id="due_date"
              v-model="form.due_date"
              type="date"
              :min="today"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <!-- Assign To -->
          <div>
            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
              Assign To
            </label>
            <select
              id="assigned_to"
              v-model="form.assigned_to"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">Select user (optional)</option>
              <option
                v-for="user in users"
                :key="user.id"
                :value="user.id"
              >
                {{ user.name }} ({{ user.email }})
              </option>
            </select>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading">
              {{ isEditing ? 'Updating...' : 'Creating...' }}
            </span>
            <span v-else>
              {{ isEditing ? 'Update Task' : 'Create Task' }}
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { X } from 'lucide-vue-next'
import { useTasksStore, type Task } from '@/stores/tasks'

interface Props {
  task?: Task | null
  isEditing?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  task: null,
  isEditing: false
})

const emit = defineEmits<{
  close: []
  saved: []
}>()

const tasksStore = useTasksStore()

const loading = ref(false)
const error = ref('')

const form = reactive({
  title: '',
  description: '',
  status: 'pending' as 'pending' | 'in_progress' | 'completed',
  priority: 'medium' as 'low' | 'medium' | 'high',
  due_date: '',
  assigned_to: null as number | null
})

const today = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const users = computed(() => tasksStore.users)

onMounted(async () => {
  // Fetch users for assignment
  await tasksStore.fetchUsers()

  if (props.isEditing && props.task) {
    // Populate form with existing task data
    form.title = props.task.title
    form.description = props.task.description || ''
    form.status = props.task.status
    form.priority = props.task.priority
    form.due_date = props.task.due_date || ''
    form.assigned_to = props.task.assigned_to || null
  }
})

const handleSubmit = async () => {
  if (!form.title.trim()) {
    error.value = 'Le titre est requis'
    return
  }

  loading.value = true
  error.value = ''

  try {
    const taskData = {
      title: form.title.trim(),
      description: form.description.trim() || undefined,
      status: form.status,
      priority: form.priority,
      due_date: form.due_date || undefined,
      assigned_to: form.assigned_to || undefined
    }

    let result
    if (props.isEditing && props.task) {
      result = await tasksStore.updateTask(props.task.id, taskData)
    } else {
      result = await tasksStore.createTask(taskData)
    }

    if (result.success) {
      emit('saved')
    } else {
      error.value = result.message || 'An error occurred'
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'An error occurred'
  } finally {
    loading.value = false
  }
}
</script>
