<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <router-link to="/dashboard" class="mr-4 text-gray-400 hover:text-gray-600">
              <ChevronLeft class="h-5 w-5" />
            </router-link>
            <h1 class="text-xl font-semibold text-gray-900">All Tasks</h1>
          </div>

          <div class="flex items-center space-x-3">
            <button
              @click="showCreateModal = true"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <Plus class="-ml-1 mr-2 h-4 w-4" />
              New Task
            </button>
          </div>
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filters -->
      <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Search
            </label>
            <div class="relative">
              <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
              <input
                v-model="filters.search"
                @input="applyFilters"
                type="text"
                placeholder="Search tasks..."
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Status
            </label>
            <select
              v-model="filters.status"
              @change="applyFilters"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Priority
            </label>
            <select
              v-model="filters.priority"
              @change="applyFilters"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Priorities</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>

          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Tasks List -->
      <div class="space-y-4">
        <div v-if="loading" class="text-center py-8">
          Loading tasks...
        </div>

        <div v-else-if="filteredTasks.length === 0" class="text-center py-12">
          <div class="text-gray-400 text-6xl mb-4">📝</div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks found</h3>
          <p class="text-gray-500 mb-4">
            {{ hasFilters ? 'No tasks match the current filters.' : 'Get started by creating your first task!' }}
          </p>
          <button
            v-if="!hasFilters"
            @click="showCreateModal = true"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
          >
            Create my first task
          </button>
        </div>

        <div v-else>
          <div
            v-for="task in filteredTasks"
            :key="task.id"
            class="bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div class="p-6">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-2">
                    <h3 class="text-lg font-medium text-gray-900">
                      {{ task.title }}
                    </h3>

                    <span
                      :class="getStatusColor(task.status)"
                      class="px-2 py-1 text-xs font-medium rounded-full"
                    >
                      {{ getStatusLabel(task.status) }}
                    </span>

                    <span
                      :class="getPriorityColor(task.priority)"
                      class="px-2 py-1 text-xs font-medium rounded-full"
                    >
                      {{ getPriorityLabel(task.priority) }}
                    </span>
                  </div>

                  <p v-if="task.description" class="text-gray-600 mb-3">
                    {{ task.description }}
                  </p>

                  <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span>
                      Created by: {{ task.user?.name || 'Unknown' }}
                    </span>

                    <span>
                      Created: {{ formatDate(task.created_at) }}
                    </span>

                    <span v-if="task.due_date" :class="getDueDateColor(task.due_date, task.status)">
                      Due: {{ formatDate(task.due_date) }}
                    </span>

                    <span v-if="task.assigned_to && typeof task.assigned_to === 'object'" class="text-blue-600 font-medium">
                      Assigned to: {{ (task.assigned_to as any).name }}
                    </span>

                    <span v-else class="text-orange-600">
                      Unassigned
                    </span>
                  </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center space-x-2 ml-4">
                  <div class="relative">
                    <button
                      @click.stop="toggleTaskMenu(task.id)"
                      class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100"
                    >
                      ⋮
                    </button>

                    <!-- Dropdown menu -->
                    <div
                      v-if="activeTaskMenu === task.id"
                      class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg z-20 border"
                      @click.stop
                    >
                      <div class="py-1">
                        <!-- Debug info -->
                        <!-- <div class="px-4 py-1 text-xs text-gray-500 border-b">
                          Debug: Creator={{ task.user_id }}, Current={{ getCurrentUser().id }}, isCreator={{ isTaskCreator(task) }}, canChangeStatus={{ canChangeStatus(task) }}, assignedTo={{ task.assigned_to }}
                        </div> -->

                        <!-- Status change options - only for assigned user or creator -->
                        <button
                          v-if="task.status === 'pending' && canChangeStatus(task)"
                          @click="startTask(task.id)"
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left"
                        >
                          🚀 Start Task
                        </button>

                        <button
                          v-if="task.status !== 'completed' && canChangeStatus(task)"
                          @click="completeTask(task.id)"
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left"
                        >
                          ✅ Mark as Complete
                        </button>

                        <button
                          v-if="task.status === 'completed' && canChangeStatus(task)"
                          @click="pendTask(task.id)"
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left"
                        >
                          🔄 Mark as Pending
                        </button>

                        <!-- Edit/Delete options - only for task creator -->
                        <button
                          v-if="isTaskCreator(task)"
                          @click="editTask(task)"
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left"
                        >
                          ✏️ Edit Task
                        </button>

                        <button
                          v-if="isTaskCreator(task)"
                          @click="deleteTask(task.id)"
                          class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50 w-full text-left"
                        >
                          🗑️ Delete Task
                        </button>

                        <!-- Assignment options - only for task creator -->
                        <button
                          v-if="!task.assigned_to && isTaskCreator(task)"
                          @click="assignToMe(task.id)"
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left"
                        >
                          👤 Assign to Me
                        </button>

                        <button
                          v-if="(typeof task.assigned_to === 'number' || typeof task.assigned_to === 'object') && isTaskCreator(task)"
                          @click="unassignTask(task.id)"
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left"
                        >
                          🚫 Unassign
                        </button>

                        <!-- Fallback message if no actions are available -->
                        <div
                          v-if="!canChangeStatus(task) && !isTaskCreator(task)"
                          class="px-4 py-2 text-sm text-gray-500 italic"
                        >
                          No actions available
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Task Modal -->
    <TaskModal
      v-if="showCreateModal || showEditModal"
      :task="editingTask"
      :is-editing="showEditModal"
      @close="closeModals"
      @saved="handleTaskSaved"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useTasksStore, type Task } from '@/stores/tasks'
import TaskModal from '@/components/TaskModal.vue'
import {
  ChevronLeft,
  Plus,
  Search
} from 'lucide-vue-next'

const tasksStore = useTasksStore()

// Reactive state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingTask = ref<Task | null>(null)
const activeTaskMenu = ref<number | null>(null)

const filters = ref({
  search: '',
  status: '',
  priority: ''
})

// Computed properties
const loading = computed(() => tasksStore.loading)
const tasks = computed(() => tasksStore.tasks)

const filteredTasks = computed(() => {
  let result = tasks.value

  if (filters.value.search) {
    result = result.filter(task =>
      task.title.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      (task.description && task.description.toLowerCase().includes(filters.value.search.toLowerCase()))
    )
  }

  if (filters.value.status) {
    result = result.filter(task => task.status === filters.value.status)
  }

  if (filters.value.priority) {
    result = result.filter(task => task.priority === filters.value.priority)
  }

  return result
})

const hasFilters = computed(() => {
  return filters.value.search !== '' || filters.value.status !== '' || filters.value.priority !== ''
})

// Lifecycle
onMounted(() => {
  tasksStore.fetchTasks()
  document.addEventListener('click', closeTaskMenu)
})

onUnmounted(() => {
  document.removeEventListener('click', closeTaskMenu)
})

// Methods
const applyFilters = () => {
  // Filter is applied automatically through computed property
}

const clearFilters = () => {
  filters.value.search = ''
  filters.value.status = ''
  filters.value.priority = ''
}

const toggleTaskMenu = (taskId: number) => {
  activeTaskMenu.value = activeTaskMenu.value === taskId ? null : taskId
}

const closeTaskMenu = () => {
  activeTaskMenu.value = null
}

const getCurrentUser = () => {
  const userStr = localStorage.getItem('auth_user') || '{}'
  return JSON.parse(userStr)
}

const startTask = async (taskId: number) => {
  await tasksStore.startTask(taskId)
  closeTaskMenu()
}

const completeTask = async (taskId: number) => {
  await tasksStore.completeTask(taskId)
  closeTaskMenu()
}

const pendTask = async (taskId: number) => {
  await tasksStore.pendTask(taskId)
  closeTaskMenu()
}

const editTask = (task: Task) => {
  editingTask.value = task
  showEditModal.value = true
  closeTaskMenu()
}

const deleteTask = async (taskId: number) => {
  if (confirm('Are you sure you want to delete this task?')) {
    await tasksStore.deleteTask(taskId)
  }
  closeTaskMenu()
}

const assignToMe = async (taskId: number) => {
  // Get current user from auth store or local storage
  const authUser = JSON.parse(localStorage.getItem('auth_user') || '{}')
  if (authUser.id) {
    await tasksStore.updateTask(taskId, { assigned_to: authUser.id })
  }
  closeTaskMenu()
}

const unassignTask = async (taskId: number) => {
  await tasksStore.updateTask(taskId, { assigned_to: null })
  closeTaskMenu()
}

const closeModals = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingTask.value = null
}

const handleTaskSaved = () => {
  closeModals()
  tasksStore.fetchTasks()
}

// Permission helper functions
const isTaskCreator = (task: Task) => {
  const currentUser = getCurrentUser()
  return task.user_id === currentUser.id
}

const isAssignedToMe = (task: Task) => {
  const currentUser = getCurrentUser()
  // Check both number ID and object assignment
  if (typeof task.assigned_to === 'number') {
    return task.assigned_to === currentUser.id
  } else if (typeof task.assigned_to === 'object' && task.assigned_to && 'id' in task.assigned_to) {
    return (task.assigned_to as { id: number }).id === currentUser.id
  }
  return false
}

const canChangeStatus = (task: Task) => {
  return isTaskCreator(task) || isAssignedToMe(task)
}

// Helper functions
const getStatusColor = (status: string) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800'
  }
  return colors[status as keyof typeof colors] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status: string) => {
  const labels = {
    pending: 'Pending',
    in_progress: 'In Progress',
    completed: 'Completed'
  }
  return labels[status as keyof typeof labels] || status
}

const getPriorityColor = (priority: string) => {
  const colors = {
    low: 'bg-gray-100 text-gray-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-red-100 text-red-800'
  }
  return colors[priority as keyof typeof colors] || 'bg-gray-100 text-gray-800'
}

const getPriorityLabel = (priority: string) => {
  const labels = {
    low: 'Low',
    medium: 'Medium',
    high: 'High'
  }
  return labels[priority as keyof typeof labels] || priority
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US')
}

const getDueDateColor = (dueDate: string, status: string) => {
  if (status === 'completed') return 'text-green-600'

  const due = new Date(dueDate)
  const today = new Date()
  const diffTime = due.getTime() - today.getTime()
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays < 0) return 'text-red-600 font-semibold' // Overdue
  if (diffDays <= 1) return 'text-orange-600 font-semibold' // Due soon
  return 'text-gray-600'
}
</script>
