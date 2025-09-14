<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-semibold text-gray-900">Task Manager</h1>
          </div>

          <div class="flex items-center space-x-4">
            <router-link
              to="/notifications"
              @click="resetNotificationCount"
              class="p-2 text-gray-400 hover:text-gray-500 relative"
            >
              <Bell class="h-6 w-6" />
              <span v-if="notificationCount > 0" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium shadow-lg">
                {{ notificationCount > 99 ? '99+' : notificationCount }}
              </span>
            </router-link>

            <div class="flex items-center space-x-3">
              <span class="text-sm text-gray-700">Welcome, {{ user?.full_name }}</span>
              <button
                @click="handleLogout"
                class="text-sm text-gray-500 hover:text-gray-700"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
              <ClipboardList class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Total Tasks</p>
              <p class="text-2xl font-semibold text-gray-900">{{ taskStats.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-lg">
              <Clock class="h-6 w-6 text-yellow-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Pending</p>
              <p class="text-2xl font-semibold text-gray-900">{{ taskStats.pending || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
              <Zap class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">In Progress</p>
              <p class="text-2xl font-semibold text-gray-900">{{ taskStats.inProgress || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
              <CheckCircle class="h-6 w-6 text-green-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Completed</p>
              <p class="text-2xl font-semibold text-gray-900">{{ taskStats.completed || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tasks Section -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Recent Tasks</h2>
            <button
              @click="showCreateTaskModal = true"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <Plus class="-ml-1 mr-2 h-4 w-4" />
              New Task
            </button>
          </div>
        </div>

        <!-- Tasks List -->
        <div class="divide-y divide-gray-200">
          <div v-if="loading" class="px-6 py-8 text-center">
            <div class="inline-flex items-center">
              <Loader2 class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500" />
              Loading tasks...
            </div>
          </div>

          <div v-else-if="recentTasks.length === 0" class="px-6 py-8 text-center">
            <ClipboardList class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new task.</p>
          </div>

          <div v-else>
            <div
              v-for="task in recentTasks"
              :key="task.id"
              class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                  <div class="flex-shrink-0">
                    <span
                      :class="[
                        'inline-flex items-center justify-center h-3 w-3 rounded-full',
                        task.status === 'completed' ? 'bg-green-400' :
                        task.status === 'in_progress' ? 'bg-blue-400' : 'bg-gray-300'
                      ]"
                    ></span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ task.title }}
                    </p>
                    <div class="flex items-center space-x-2 mt-1">
                      <span
                        :class="getStatusColor(task.status)"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                      >
                        {{ getStatusLabel(task.status) }}
                      </span>
                      <span
                        :class="getPriorityColor(task.priority)"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                      >
                        {{ getPriorityLabel(task.priority) }}
                      </span>
                      <span
                        v-if="task.assigned_to_user"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                      >
                        → {{ task.assigned_to_user.name }}
                      </span>
                      <span
                        v-else-if="task.user_id !== getCurrentUserId()"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600"
                      >
                        Created by {{ task.user?.name }}
                      </span>
                      <span
                        v-else-if="!task.assigned_to"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800"
                      >
                        Unassigned
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <button
                    v-if="task.status !== 'completed'"
                    @click="completeTask(task.id)"
                    class="p-1 text-gray-400 hover:text-green-600 transition-colors"
                    title="Mark as completed"
                  >
                    <Check class="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- View All Tasks Link -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="text-center">
            <router-link
              to="/tasks"
              class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
            >
              View all tasks →
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Modal -->
    <CreateTaskModal
      v-if="showCreateTaskModal"
      @close="showCreateTaskModal = false"
      @created="handleTaskCreated"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTasksStore } from '@/stores/tasks'
import CreateTaskModal from '@/components/CreateTaskModal.vue'
import { getEcho } from '@/plugins/echo'
import {
  ClipboardList,
  Clock,
  Zap,
  CheckCircle,
  Bell,
  Plus,
  Loader2,
  Check
} from 'lucide-vue-next'

const router = useRouter()
const authStore = useAuthStore()
const tasksStore = useTasksStore()

const showCreateTaskModal = ref(false)
// Calculate unread notification count from stored notifications
const notificationCount = ref(0)

// Interface matching the notification structure
interface StoredNotification {
  id: string
  type: string
  title: string
  message: string
  task_id?: number
  read: boolean
  created_at: string
}

const updateNotificationCount = () => {
  const stored = localStorage.getItem('notifications')
  if (stored) {
    try {
      const notifications: StoredNotification[] = JSON.parse(stored)
      notificationCount.value = notifications.filter(n => !n.read).length
    } catch (error) {
      console.error('Failed to load notifications for count:', error)
      notificationCount.value = 0
    }
  } else {
    notificationCount.value = 0
  }
}

const user = computed(() => authStore.user)
const loading = computed(() => tasksStore.loading)
const taskStats = computed(() => tasksStore.taskStats)

// Get first 5 tasks for recent tasks preview
const recentTasks = computed(() => tasksStore.tasks.slice(0, 5))

onMounted(async () => {
  await tasksStore.fetchTasks()
  updateNotificationCount() // Load initial count
  setupNotificationListener()

  // Update count when page becomes visible (e.g., returning from notifications page)
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
      updateNotificationCount()
    }
  })
})

// Watch for localStorage changes (when notifications are updated)
const originalSetItem = localStorage.setItem
localStorage.setItem = function(key: string, value: string) {
  originalSetItem.apply(this, [key, value])
  if (key === 'notifications') {
    updateNotificationCount()
  }
}

onUnmounted(() => {
  // Clean up event listeners
  document.removeEventListener('visibilitychange', updateNotificationCount)
})

const setupNotificationListener = () => {
  try {
    const echo = getEcho()
    const user = authStore.user

    if (user) {
      console.log('🔧 Setting up notification listener for dashboard')

      // Listen to the public tasks channel
      const channel = echo.channel('tasks')

      // Listen for task events and refresh notification count
      channel.listen('.task.created', (data: unknown) => {
        console.log('📬 Dashboard received task notification:', data)
        const taskData = data as { user?: { id: number } }
        // Only count if it's not the current user creating the task
        if (taskData.user && taskData.user.id !== user.id) {
          // Wait a bit for the notification to be stored, then update count
          setTimeout(() => updateNotificationCount(), 100)
        }
      })

      channel.listen('.task.updated', (data: unknown) => {
        console.log('📬 Dashboard received task update:', data)
        const taskData = data as { user?: { id: number } }
        if (taskData.user && taskData.user.id !== user.id) {
          setTimeout(() => updateNotificationCount(), 100)
        }
      })

      channel.listen('.task.completed', (data: unknown) => {
        console.log('📬 Dashboard received task completion:', data)
        const taskData = data as { user?: { id: number } }
        if (taskData.user && taskData.user.id !== user.id) {
          setTimeout(() => updateNotificationCount(), 100)
        }
      })
    }
  } catch (error) {
    console.error('❌ Error setting up notification listener:', error)
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

const completeTask = async (taskId: number) => {
  await tasksStore.completeTask(taskId)
}

const getCurrentUserId = () => {
  const currentUser = JSON.parse(localStorage.getItem('user') || '{}')
  return currentUser.id
}

const handleTaskCreated = () => {
  showCreateTaskModal.value = false
  // Refresh tasks
  tasksStore.fetchTasks()
}

const resetNotificationCount = () => {
  // Mark all notifications as read when clicking the notification bell
  const stored = localStorage.getItem('notifications')
  if (stored) {
    try {
      const notifications: StoredNotification[] = JSON.parse(stored)
      const updatedNotifications = notifications.map(n => ({ ...n, read: true }))
      localStorage.setItem('notifications', JSON.stringify(updatedNotifications))
      updateNotificationCount()
    } catch (error) {
      console.error('Failed to mark notifications as read:', error)
    }
  }
}

// Helper functions for status and priority display
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
</script>
