import { defineStore } from "pinia";
import axios from "axios";
import { updateEchoAuth } from "@/plugins/echo";

export interface User {
  id: number;
  full_name: string;
  email: string;
  phone_number?: string;
  address?: string;
  image?: string;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  full_name: string;
  email: string;
  phone_number?: string;
  address?: string;
  image?: string;
  password: string;
  password_confirmation: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
  loading: boolean;
  error: string | null;
}

export const useAuthStore = defineStore("auth", {
  state: (): AuthState => ({
    user: null,
    token: localStorage.getItem("auth_token"),
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    authHeader: (state) => (state.token ? `Bearer ${state.token}` : null),
  },

  actions: {
    /**
     * Initialize auth state from localStorage
     */
    initializeAuth() {
      const token = localStorage.getItem("auth_token");
      const user = localStorage.getItem("auth_user");

      if (token && user) {
        this.token = token;
        this.user = JSON.parse(user);
        this.setAxiosAuthHeader(token);
      }
    },

    /**
     * Set authorization header for axios and Echo
     */
    setAxiosAuthHeader(token: string) {
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
      updateEchoAuth(token);
    },

    /**
     * Remove authorization header from axios and Echo
     */
    removeAxiosAuthHeader() {
      delete axios.defaults.headers.common["Authorization"];
      updateEchoAuth(null);
    },

    /**
     * Login user
     */
    async login(credentials: LoginCredentials) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post("/api/auth/login", credentials);

        if (response.data.success) {
          const { user, authorization } = response.data;

          this.user = user;
          this.token = authorization.token;

          // Store in localStorage
          localStorage.setItem("auth_token", authorization.token);
          localStorage.setItem("auth_user", JSON.stringify(user));

          // Set axios header
          this.setAxiosAuthHeader(authorization.token);

          return { success: true };
        } else {
          this.error = response.data.message || "Login failed";
          return { success: false, message: this.error };
        }
      } catch (error: any) {
        console.error("Login error:", error);
        this.error = error.response?.data?.message || "Login failed";
        return { success: false, message: this.error };
      } finally {
        this.loading = false;
      }
    },

    /**
     * Register new user
     */
    async register(data: RegisterData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post("/api/auth/register", data);

        if (response.data.success) {
          const { user, authorization } = response.data;

          this.user = user;
          this.token = authorization.token;

          // Store in localStorage
          localStorage.setItem("auth_token", authorization.token);
          localStorage.setItem("auth_user", JSON.stringify(user));

          // Set axios header
          this.setAxiosAuthHeader(authorization.token);

          return { success: true };
        } else {
          this.error = response.data.message || "Registration failed";
          return { success: false, message: this.error };
        }
      } catch (error: any) {
        console.error("Registration error:", error);
        this.error = error.response?.data?.message || "Registration failed";
        return { success: false, message: this.error };
      } finally {
        this.loading = false;
      }
    },

    /**
     * Logout user
     */
    async logout() {
      this.loading = true;

      try {
        if (this.token) {
          await axios.post("/api/auth/logout");
        }
      } catch (error) {
        console.error("Logout error:", error);
      } finally {
        this.clearAuthData();
        this.loading = false;
      }
    },

    /**
     * Clear authentication data
     */
    clearAuthData() {
      this.user = null;
      this.token = null;
      this.error = null;

      localStorage.removeItem("auth_token");
      localStorage.removeItem("auth_user");

      this.removeAxiosAuthHeader();
    },

    /**
     * Get current user profile
     */
    async getMe() {
      if (!this.token) return;

      try {
        const response = await axios.get("/api/auth/me");

        if (response.data.success) {
          this.user = response.data.user;
          localStorage.setItem("auth_user", JSON.stringify(response.data.user));
        }
      } catch (error) {
        console.error("Get me error:", error);
        // Token might be expired, logout
        this.clearAuthData();
      }
    },

    /**
     * Refresh token
     */
    async refreshToken() {
      if (!this.token) return;

      try {
        const response = await axios.post("/api/auth/refresh");

        if (response.data.success) {
          const { user, authorization } = response.data;

          this.user = user;
          this.token = authorization.token;

          localStorage.setItem("auth_token", authorization.token);
          localStorage.setItem("auth_user", JSON.stringify(user));

          this.setAxiosAuthHeader(authorization.token);
        }
      } catch (error) {
        console.error("Refresh token error:", error);
        this.clearAuthData();
      }
    },
  },
});
