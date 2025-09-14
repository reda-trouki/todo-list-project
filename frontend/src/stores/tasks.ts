import { defineStore } from "pinia";
import axios from "axios";

export interface User {
  id: number;
  name: string;
  email: string;
}

export interface Task {
  id: number;
  title: string;
  description?: string;
  status: "pending" | "in_progress" | "completed";
  priority: "low" | "medium" | "high";
  due_date?: string;
  user_id: number;
  assigned_to?: number | User; // Can be either the ID or the full user object
  created_at: string;
  updated_at: string;
  user?: User;
  assigned_to_user?: User; // Legacy field for compatibility
}

export interface CreateTaskData {
  title: string;
  description?: string;
  status?: "pending" | "in_progress" | "completed";
  priority?: "low" | "medium" | "high";
  due_date?: string;
  assigned_to?: number | null;
}

export interface UpdateTaskData {
  title?: string;
  description?: string;
  status?: "pending" | "in_progress" | "completed";
  priority?: "low" | "medium" | "high";
  due_date?: string;
  assigned_to?: number | null;
}

export interface TaskFilters {
  status?: string;
  priority?: string;
}

interface TasksState {
  tasks: Task[];
  users: User[];
  loading: boolean;
  error: string | null;
  currentTask: Task | null;
}

export const useTasksStore = defineStore("tasks", {
  state: (): TasksState => ({
    tasks: [],
    users: [],
    loading: false,
    error: null,
    currentTask: null,
  }),

  getters: {
    pendingTasks: (state) => state.tasks.filter((task) => task.status === "pending"),
    inProgressTasks: (state) => state.tasks.filter((task) => task.status === "in_progress"),
    completedTasks: (state) => state.tasks.filter((task) => task.status === "completed"),

    tasksByPriority: (state) => (priority: string) =>
      state.tasks.filter((task) => task.priority === priority),

    overdueTasks: (state) => {
      const today = new Date().toISOString().split("T")[0];
      return state.tasks.filter(
        (task) => task.due_date && task.due_date < today && task.status !== "completed"
      );
    },

    taskStats: (state) => {
      const total = state.tasks.length;
      const pending = state.tasks.filter((t) => t.status === "pending").length;
      const inProgress = state.tasks.filter((t) => t.status === "in_progress").length;
      const completed = state.tasks.filter((t) => t.status === "completed").length;

      return {
        total,
        pending,
        inProgress,
        completed,
        completionRate: total > 0 ? Math.round((completed / total) * 100) : 0,
      };
    },
  },

  actions: {
    /**
     * Fetch all tasks with optional filters
     */
    async fetchTasks(filters: TaskFilters = {}) {
      this.loading = true;
      this.error = null;

      try {
        const params = new URLSearchParams();
        if (filters.status) params.append("status", filters.status);
        if (filters.priority) params.append("priority", filters.priority);

        const response = await axios.get(`/api/tasks?${params.toString()}`);

        if (response.data.success) {
          this.tasks = response.data.tasks;
          return { success: true };
        } else {
          this.error = response.data.message || "Failed to fetch tasks";
          return { success: false, message: this.error };
        }
      } catch (error: any) {
        console.error("Fetch tasks error:", error);
        this.error = error.response?.data?.message || "Failed to fetch tasks";
        return { success: false, message: this.error };
      } finally {
        this.loading = false;
      }
    },

    /**
     * Create a new task
     */
    async createTask(data: CreateTaskData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post("/api/tasks", data);

        if (response.data.success) {
          const newTask = response.data.task;
          this.tasks.unshift(newTask); // Add to beginning of list
          return { success: true, task: newTask };
        } else {
          this.error = response.data.message || "Failed to create task";
          return { success: false, message: this.error };
        }
      } catch (error: any) {
        console.error("Create task error:", error);
        this.error = error.response?.data?.message || "Failed to create task";
        return { success: false, message: this.error };
      } finally {
        this.loading = false;
      }
    },

    /**
     * Get a specific task
     */
    async getTask(id: number) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get(`/api/tasks/${id}`);

        if (response.data.success) {
          this.currentTask = response.data.task;
          return { success: true, task: response.data.task };
        } else {
          this.error = response.data.message || "Task not found";
          return { success: false, message: this.error };
        }
      } catch (error: any) {
        console.error("Get task error:", error);
        this.error = error.response?.data?.message || "Failed to get task";
        return { success: false, message: this.error };
      } finally {
        this.loading = false;
      }
    },

    /**
     * Update a task
     */
    async updateTask(id: number, data: UpdateTaskData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.put(`/api/tasks/${id}`, data);

        if (response.data.success) {
          const updatedTask = response.data.task;

          // Update in tasks array
          const index = this.tasks.findIndex((task) => task.id === id);
          if (index !== -1) {
            this.tasks[index] = updatedTask;
          }

          // Update current task if it's the same
          if (this.currentTask && this.currentTask.id === id) {
            this.currentTask = updatedTask;
          }

          return { success: true, task: updatedTask };
        } else {
          this.error = response.data.message || "Failed to update task";
          return { success: false, message: this.error };
        }
      } catch (error: any) {
        console.error("Update task error:", error);
        this.error = error.response?.data?.message || "Failed to update task";
        return { success: false, message: this.error };
      } finally {
        this.loading = false;
      }
    },

    /**
     * Delete a task
     */
    async deleteTask(id: number) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.delete(`/api/tasks/${id}`);

        if (response.data.success) {
          // Remove from tasks array
          this.tasks = this.tasks.filter((task) => task.id !== id);

          // Clear current task if it was deleted
          if (this.currentTask && this.currentTask.id === id) {
            this.currentTask = null;
          }

          return { success: true };
        } else {
          this.error = response.data.message || "Failed to delete task";
          return { success: false, message: this.error };
        }
      } catch (error: any) {
        console.error("Delete task error:", error);
        this.error = error.response?.data?.message || "Failed to delete task";
        return { success: false, message: this.error };
      } finally {
        this.loading = false;
      }
    },

    /**
     * Mark task as completed
     */
    async completeTask(id: number) {
      return await this.updateTask(id, { status: "completed" });
    },

    /**
     * Mark task as in progress
     */
    async startTask(id: number) {
      return await this.updateTask(id, { status: "in_progress" });
    },

    /**
     * Mark task as pending
     */
    async pendTask(id: number) {
      return await this.updateTask(id, { status: "pending" });
    },

    /**
     * Clear current task
     */
    clearCurrentTask() {
      this.currentTask = null;
    },

    /**
     * Clear all tasks (for logout)
     */
    clearTasks() {
      this.tasks = [];
      this.currentTask = null;
      this.error = null;
    },

    /**
     * Handle real-time task creation
     */
    handleTaskCreated(task: Task) {
      // Only add if not already in list
      if (!this.tasks.find((t) => t.id === task.id)) {
        this.tasks.unshift(task);
      }
    },

    /**
     * Fetch all users for task assignment
     */
    async fetchUsers() {
      try {
        const response = await axios.get("/api/users");

        if (response.data.success) {
          this.users = response.data.users;
          return { success: true, users: response.data.users };
        } else {
          this.error = response.data.message || "Failed to fetch users";
          return { success: false, message: this.error };
        }
      } catch (error: any) {
        console.error("Fetch users error:", error);
        this.error = error.response?.data?.message || "Failed to fetch users";
        return { success: false, message: this.error };
      }
    },
  },
});
