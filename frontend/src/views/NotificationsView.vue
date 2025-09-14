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
            <h1 class="text-xl font-semibold text-gray-900">Notifications</h1>
          </div>

          <div class="flex items-center space-x-3">
            <button
              v-if="notifications.length > 0"
              @click="clearAllNotifications"
              class="text-sm text-red-600 hover:text-red-700"
            >
              Clear All
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Connection Status -->
      <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-3 h-3 rounded-full" :class="connectionDotClass"></div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">
              {{ connectionStatusText }}
            </p>
          </div>
        </div>
      </div>

      <!-- Notifications List -->
      <div v-if="notifications.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
        <Bell class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
        <p class="mt-1 text-sm text-gray-500">Your notifications will appear here in real-time</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
          :class="{ 'border-l-4 border-indigo-500': !notification.read }"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-2">
                <span class="text-2xl">{{ getNotificationIcon(notification.type) }}</span>
                <h3 class="text-lg font-medium text-gray-900">
                  {{ notification.title }}
                </h3>

                <span
                  v-if="!notification.read"
                  class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full"
                >
                  New
                </span>
              </div>

              <p class="text-gray-600 mb-3">
                {{ notification.message }}
              </p>

              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">
                  {{ formatNotificationDate(notification.created_at) }}
                </span>

                <div class="flex items-center space-x-2">
                  <button
                    v-if="!notification.read"
                    @click="markAsRead(notification.id)"
                    class="text-sm text-indigo-600 hover:text-indigo-700"
                  >
                    Mark as read
                  </button>

                  <button
                    v-if="notification.task_id"
                    @click="goToTask(notification.task_id)"
                    class="text-sm text-gray-600 hover:text-gray-700"
                  >
                    View Task
                  </button>

                  <button
                    @click="deleteNotification(notification.id)"
                    class="text-sm text-red-600 hover:text-red-700"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Test Notification Button (Development) -->
      <div v-if="isDevelopment" class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h4 class="font-medium text-gray-900 mb-2">Development Mode</h4>
        <button
          @click="addTestNotification"
          class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700"
        >
          Ajouter une notification test
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { getEcho } from '@/plugins/echo'
import { ChevronLeft, Bell } from 'lucide-vue-next'

interface Notification {
  id: string
  type: 'task_created' | 'task_updated' | 'task_completed' | 'system'
  title: string
  message: string
  task_id?: number
  read: boolean
  created_at: string
}

const router = useRouter()
const authStore = useAuthStore()

// Reactive state
const notifications = ref<Notification[]>([])
const isConnected = ref(false)
const isDevelopment = ref(import.meta.env.DEV)

// Computed properties
const connectionDotClass = computed(() => {
  return isConnected.value ? 'bg-green-500' : 'bg-red-500'
})

const connectionStatusText = computed(() => {
  return isConnected.value
    ? 'Connected - Real-time notifications enabled'
    : 'Disconnected - Real-time notifications unavailable'
})

// Lifecycle
onMounted(() => {
  setupEchoConnection()
  loadStoredNotifications()
})

onUnmounted(() => {
  // Clean up Echo connection if needed
})

// Methods
const setupEchoConnection = () => {
  try {
    const echo = getEcho()

    const user = authStore.user
    if (user) {
      console.log('🔧 Setting up Echo for user:', user.id)

      // Listen to the public tasks channel for all users
      const channel = echo.channel('tasks')

      console.log('📡 Listening on public channel: tasks')

      // Add channel subscription events for debugging
      channel.subscribed(() => {
        console.log('✅ Successfully subscribed to public channel tasks')
      })

      channel.error((error: unknown) => {
        console.error('❌ Channel subscription error:', error)
      })

      channel
        .listen('.task.created', (e: { task?: { id: number; title: string }; user?: { id: number; name: string; email: string }; message?: string }) => {
          console.log('🎉 Received task.created event:', e)
          const taskName = e.task?.title || 'Unknown Task'
          const creatorName = e.user?.name || 'Unknown User'
          const notificationMessage = e.message || `New task: ${taskName} has been created by: ${creatorName}`

          addNotification({
            type: 'task_created',
            title: 'Nouvelle tâche créée',
            message: notificationMessage,
            task_id: e.task?.id
          })
        })
        .listen('.task.updated', (e: { task?: { id: number; title: string }; user?: { id: number; name: string; email: string }; message?: string }) => {
          console.log('📝 Received task.updated event:', e)
          const taskName = e.task?.title || 'Unknown Task'
          const updaterName = e.user?.name || 'Unknown User'
          const notificationMessage = e.message || `Task: ${taskName} has been updated by: ${updaterName}`

          addNotification({
            type: 'task_updated',
            title: 'Tâche mise à jour',
            message: notificationMessage,
            task_id: e.task?.id
          })
        })
        .listen('.task.completed', (e: { task?: { id: number; title: string }; user?: { id: number; name: string; email: string }; message?: string }) => {
          console.log('✅ Received task.completed event:', e)
          const taskName = e.task?.title || 'Unknown Task'
          const completerName = e.user?.name || 'Unknown User'
          const notificationMessage = e.message || `Task: ${taskName} has been completed by: ${completerName}`

          addNotification({
            type: 'task_completed',
            title: 'Tâche terminée',
            message: notificationMessage,
            task_id: e.task?.id
          })
        })      // Connection status handlers for Pusher
      if (echo.connector && 'pusher' in echo.connector) {
        const pusherConnector = echo.connector as { pusher: { connection: { state: string; bind: (event: string, callback: (data?: unknown) => void) => void } } }

        // Check if already connected
        if (pusherConnector.pusher.connection.state === 'connected') {
          isConnected.value = true
        }

        pusherConnector.pusher.connection.bind('connected', () => {
          isConnected.value = true
          addNotification({
            type: 'system',
            title: 'Connexion établie',
            message: 'Les notifications temps réel sont maintenant actives.'
          })
        })

        pusherConnector.pusher.connection.bind('disconnected', () => {
          isConnected.value = false
        })

        pusherConnector.pusher.connection.bind('failed', () => {
          isConnected.value = false
        })
      }
    }
  } catch (error) {
    console.error('Failed to setup Echo connection:', error)
    isConnected.value = false
  }
}

const addNotification = (notificationData: Omit<Notification, 'id' | 'read' | 'created_at'>) => {
  const notification: Notification = {
    id: `notif-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
    ...notificationData,
    read: false,
    created_at: new Date().toISOString()
  }

  notifications.value.unshift(notification)
  saveNotifications()

  // Show browser notification if permission is granted
  showBrowserNotification(notification)
}

const showBrowserNotification = (notification: Notification) => {
  if ('Notification' in window && Notification.permission === 'granted') {
    new Notification(notification.title, {
      body: notification.message,
      icon: '/favicon.ico'
    })
  } else if ('Notification' in window && Notification.permission !== 'denied') {
    Notification.requestPermission().then(permission => {
      if (permission === 'granted') {
        new Notification(notification.title, {
          body: notification.message,
          icon: '/favicon.ico'
        })
      }
    })
  }
}

const markAsRead = (notificationId: string) => {
  const notification = notifications.value.find(n => n.id === notificationId)
  if (notification) {
    notification.read = true
    saveNotifications()
  }
}

const deleteNotification = (notificationId: string) => {
  notifications.value = notifications.value.filter(n => n.id !== notificationId)
  saveNotifications()
}

const clearAllNotifications = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer toutes les notifications ?')) {
    notifications.value = []
    saveNotifications()
  }
}

const goToTask = (taskId: number) => {
  router.push(`/tasks/${taskId}`)
}

const addTestNotification = () => {
  addNotification({
    type: 'task_created',
    title: 'Notification Test',
    message: 'Ceci est une notification de test pour vérifier le bon fonctionnement du système.',
    task_id: Math.floor(Math.random() * 100)
  })
}

const loadStoredNotifications = () => {
  const stored = localStorage.getItem('notifications')
  if (stored) {
    try {
      notifications.value = JSON.parse(stored)
    } catch (error) {
      console.error('Failed to load stored notifications:', error)
    }
  }
}

const saveNotifications = () => {
  // Keep only the last 50 notifications
  const toKeep = notifications.value.slice(0, 50)
  localStorage.setItem('notifications', JSON.stringify(toKeep))
}

// Helper functions
const getNotificationIcon = (type: string) => {
  const icons = {
    task_created: '📝',
    task_updated: '✏️',
    task_completed: '✅',
    system: '🔧'
  }
  return icons[type as keyof typeof icons] || '📢'
}

const formatNotificationDate = (dateString: string) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = now.getTime() - date.getTime()
  const diffMins = Math.floor(diffMs / 60000)
  const diffHours = Math.floor(diffMs / 3600000)
  const diffDays = Math.floor(diffMs / 86400000)

  if (diffMins < 1) return 'À l\'instant'
  if (diffMins < 60) return `Il y a ${diffMins} min`
  if (diffHours < 24) return `Il y a ${diffHours}h`
  if (diffDays < 7) return `Il y a ${diffDays}j`

  return date.toLocaleDateString('fr-FR')
}
</script>
