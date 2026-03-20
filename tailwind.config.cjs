const animate = require("tailwindcss-animate")

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: ["class"],
  safelist: ["dark"],
  prefix: "",
  
  content: [
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./storage/framework/views/*.php",
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.{js,jsx,vue}",
  ],
  
  theme: {
    container: {
      center: true,
      padding: "2rem",
      screens: {
        "2xl": "1400px",
      },
    },
    extend: {
      colors: {
        border: "hsl(var(--border))",
        input: "hsl(var(--input))",
        ring: "hsl(var(--ring))",
        background: "hsl(var(--background))",
        foreground: "hsl(var(--foreground))",
        primary: {
          DEFAULT: "hsl(var(--primary))",
          foreground: "hsl(var(--primary-foreground))",
        },
        secondary: {
          DEFAULT: "hsl(var(--secondary))",
          foreground: "hsl(var(--secondary-foreground))",
        },
        destructive: {
          DEFAULT: "hsl(var(--destructive))",
          foreground: "hsl(var(--destructive-foreground))",
        },
        muted: {
          DEFAULT: "hsl(var(--muted))",
          foreground: "hsl(var(--muted-foreground))",
        },
        accent: {
          DEFAULT: "hsl(var(--accent))",
          foreground: "hsl(var(--accent-foreground))",
        },
        popover: {
          DEFAULT: "hsl(var(--popover))",
          foreground: "hsl(var(--popover-foreground))",
        },
        card: {
          DEFAULT: "hsl(var(--card))",
          foreground: "hsl(var(--card-foreground))",
        },
        "bg-page": "oklch(0.99 0.005 60)",
        "bg-surface": "oklch(1 0 0)",
        "bg-surface-raised": "oklch(0.995 0.003 60)",
        "bg-input": "oklch(0.99 0.003 60)",
        "border-default": "oklch(0.91 0.005 60)",
        "border-subtle": "oklch(0.95 0.003 60)",
        "text-primary": "oklch(0.15 0.01 60)",
        "text-secondary": "oklch(0.45 0.01 60)",
        "text-muted": "oklch(0.55 0.008 60)",
        "text-inverse": "oklch(0.98 0 0)",
        "accent-primary": "oklch(0.65 0.18 50)",
        "accent-primary-hover": "oklch(0.60 0.20 45)",
        "accent-primary-subtle": "oklch(0.96 0.04 50)",
        "accent-secondary": "oklch(0.70 0.12 160)",
        "accent-secondary-subtle": "oklch(0.94 0.04 160)",
        "bubble-sent": "oklch(0.65 0.18 50)",
        "bubble-sent-text": "oklch(0.98 0 0)",
        "bubble-sent-border": "oklch(0.60 0.20 45)",
        "bubble-received": "oklch(0.995 0.003 60)",
        "bubble-received-text": "oklch(0.15 0.01 60)",
        "bubble-received-border": "oklch(0.91 0.005 60)",
        "status-online": "oklch(0.70 0.12 160)",
        "status-offline": "oklch(0.55 0.008 60)",
        "status-read": "oklch(0.65 0.18 50)",
        "status-unread": "oklch(0.65 0.18 50)",
        "input-bg": "oklch(0.99 0.003 60)",
        "input-border": "oklch(0.91 0.005 60)",
        "input-border-focus": "oklch(0.65 0.18 50)",
        "button-send": "oklch(0.65 0.18 50)",
        "button-send-hover": "oklch(0.60 0.20 45)",
        "button-attach": "oklch(0.55 0.01 60)",
        "avatar-1": "oklch(0.65 0.18 50)",
        "avatar-2": "oklch(0.70 0.12 160)",
        "avatar-3": "oklch(0.65 0.18 320)",
        "avatar-4": "oklch(0.65 0.18 30)",
        "avatar-5": "oklch(0.65 0.18 90)",
        "avatar-6": "oklch(0.65 0.18 200)",
        "avatar-7": "oklch(0.65 0.18 280)",
        "avatar-8": "oklch(0.65 0.18 350)",
      },
      borderRadius: {
        xl: "calc(var(--radius) + 4px)",
        lg: "var(--radius)",
        md: "calc(var(--radius) - 2px)",
        sm: "calc(var(--radius) - 4px)",
        bubble: "12px",
      },
      maxWidth: {
        bubble: "70%",
        "bubble-group": "65%",
      },
      fontFamily: {
        sans: ["Instrument Sans", "system-ui", "sans-serif"],
      },
      fontSize: {
        "app-title": ["20px", { fontWeight: "600" }],
        "conversation-name": ["16px", { fontWeight: "600" }],
        "message": ["14px", { fontWeight: "400", lineHeight: "1.4" }],
        timestamp: ["11px", { fontWeight: "400" }],
        caption: ["12px", { fontWeight: "400" }],
      },
      keyframes: {
        "accordion-down": {
          from: { height: 0 },
          to: { height: "var(--radix-accordion-content-height)" },
        },
        "accordion-up": {
          from: { height: "var(--radix-accordion-content-height)" },
          to: { height: 0 },
        },
        "collapsible-down": {
          from: { height: 0 },
          to: { height: 'var(--radix-collapsible-content-height)' },
        },
        "collapsible-up": {
          from: { height: 'var(--radix-collapsible-content-height)' },
          to: { height: 0 },
        },
        "slide-up": {
          "0%": { opacity: "0", transform: "translateY(10px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
        "slide-right": {
          "0%": { opacity: "0", transform: "translateX(-10px)" },
          "100%": { opacity: "1", transform: "translateX(0)" },
        },
        "slide-in-left": {
          "0%": { opacity: "0", transform: "translateX(-20px)" },
          "100%": { opacity: "1", transform: "translateX(0)" },
        },
        "bounce-dot": {
          "0%, 100%": { transform: "translateY(0)" },
          "50%": { transform: "translateY(-4px)" },
        },
        "scale-pop": {
          "0%": { transform: "scale(0)" },
          "100%": { transform: "scale(1)" },
        },
        "fade-in": {
          "0%": { opacity: "0" },
          "100%": { opacity: "1" },
        },
      },
      animation: {
        "accordion-down": "accordion-down 0.2s ease-out",
        "accordion-up": "accordion-up 0.2s ease-out",
        "collapsible-down": "collapsible-down 0.2s ease-in-out",
        "collapsible-up": "collapsible-up 0.2s ease-in-out",
        "slide-up": "slide-up 200ms ease-out",
        "slide-right": "slide-right 150ms ease-out",
        "slide-in-left": "slide-in-left 200ms ease-out",
        "bounce-dot": "bounce-dot 1.4s infinite",
        "scale-pop": "scale-pop 150ms ease-out",
        "fade-in": "fade-in 200ms ease-out",
      },
    },
  },
  plugins: [animate],
}
