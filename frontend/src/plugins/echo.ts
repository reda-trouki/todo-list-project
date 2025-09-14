import Echo from "laravel-echo";
import Pusher from "pusher-js";

declare global {
  interface Window {
    Pusher: typeof Pusher;
    Echo: Echo;
  }
}

// Configure Pusher globally
window.Pusher = Pusher;

// Test Pusher connection directly first
console.log("🔧 Testing Pusher connection with key:", import.meta.env.VITE_PUSHER_APP_KEY);

/**
 * Configure Laravel Echo with Pusher
 *
 * This setup enables real-time communication between the Vue.js frontend
 * and Laravel backend using WebSockets through Pusher.
 */
const echo = new Echo({
  broadcaster: "pusher",
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: "eu",
  forceTLS: true,
  encrypted: true,
  authEndpoint: `${import.meta.env.VITE_API_URL}/api/broadcasting/auth`,
  auth: {
    headers: {
      Authorization: "",
    },
  },
});

/**
 * Update Echo authorization header
 * Call this whenever the auth token changes
 */
export const updateEchoAuth = (token: string | null) => {
  console.log("🔑 Updating Echo auth token:", token ? "Token set" : "Token cleared");

  if (token) {
    echo.connector.options.auth.headers.Authorization = `Bearer ${token}`;
  } else {
    delete echo.connector.options.auth.headers.Authorization;
  }

  console.log("🔒 Current auth headers:", echo.connector.options.auth.headers);
};

// Add event listeners for debugging with proper type checking
if ("pusher" in echo.connector) {
  const pusherConnector = echo.connector as {
    pusher: { connection: { bind: (event: string, callback: (data?: unknown) => void) => void } };
  };

  pusherConnector.pusher.connection.bind("connected", () => {
    console.log("✅ Pusher connected successfully");
  });

  pusherConnector.pusher.connection.bind("disconnected", () => {
    console.log("❌ Pusher disconnected");
  });

  pusherConnector.pusher.connection.bind("error", (error: unknown) => {
    console.error("🚨 Pusher connection error:", error);
  });

  pusherConnector.pusher.connection.bind("failed", (error: unknown) => {
    console.error("💥 Pusher connection failed:", error);
  });
}

/**
 * Get the configured Echo instance
 */
export const getEcho = () => echo;

// Make Echo available globally
window.Echo = echo;

export default echo;
