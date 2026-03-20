<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Chatty') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        <style>
            :root {
                /* Foundation Palette - Warm White */
                --bg-page: oklch(0.99 0.005 60);
                --bg-surface: oklch(1 0 0);
                --bg-surface-raised: oklch(0.995 0.003 60);
                --bg-input: oklch(0.99 0.003 60);
                
                --border-default: oklch(0.91 0.005 60);
                --border-subtle: oklch(0.95 0.003 60);
                
                --text-primary: oklch(0.15 0.01 60);
                --text-secondary: oklch(0.45 0.01 60);
                --text-muted: oklch(0.55 0.008 60);
                --text-inverse: oklch(0.98 0 0);
                
                /* Brand / Accent Colours - Coral */
                --accent-primary: oklch(0.65 0.18 50);
                --accent-primary-hover: oklch(0.60 0.20 45);
                --accent-primary-subtle: oklch(0.96 0.04 50);
                --accent-secondary: oklch(0.70 0.12 160);
                --accent-secondary-subtle: oklch(0.94 0.04 160);
                
                /* Semantic Colours */
                --status-online: oklch(0.70 0.12 160);
                --status-offline: oklch(0.55 0.008 60);
                --status-sent: oklch(0.55 0.01 60);
                --status-delivered: oklch(0.55 0.01 60);
                --status-read: oklch(0.65 0.18 50);
                --status-typing: oklch(0.55 0.008 60);
                --status-unread: oklch(0.65 0.18 50);
                
                /* Message Bubble Colours - Coral */
                --bubble-sent-bg: oklch(0.65 0.18 50);
                --bubble-sent-text: oklch(0.98 0 0);
                --bubble-sent-border: oklch(0.60 0.20 45);
                --bubble-received-bg: oklch(0.995 0.003 60);
                --bubble-received-text: oklch(0.15 0.01 60);
                --bubble-received-border: oklch(0.91 0.005 60);
                
                /* Input & Action Colours */
                --input-bg: oklch(0.99 0.003 60);
                --input-border: oklch(0.91 0.005 60);
                --input-border-focus: oklch(0.65 0.18 50);
                --button-send: oklch(0.65 0.18 50);
                --button-send-hover: oklch(0.60 0.20 45);
                --button-attach: oklch(0.55 0.01 60);
                
                /* Avatar Colours */
                --avatar-1: oklch(0.65 0.18 50);
                --avatar-2: oklch(0.70 0.12 160);
                --avatar-3: oklch(0.65 0.18 320);
                --avatar-4: oklch(0.65 0.18 30);
                --avatar-5: oklch(0.65 0.18 90);
                --avatar-6: oklch(0.65 0.18 200);
                --avatar-7: oklch(0.65 0.18 280);
                --avatar-8: oklch(0.65 0.18 350);
                
                /* shadcn-vue compatibility */
                --background: oklch(0.99 0.005 60);
                --foreground: oklch(0.15 0.01 60);
                --card: oklch(1 0 0);
                --card-foreground: oklch(0.15 0.01 60);
                --popover: oklch(1 0 0);
                --popover-foreground: oklch(0.15 0.01 60);
                --primary: oklch(0.65 0.18 50);
                --primary-foreground: oklch(0.98 0 0);
                --secondary: oklch(0.94 0.04 160);
                --secondary-foreground: oklch(0.15 0.01 60);
                --muted: oklch(0.96 0.04 50);
                --muted-foreground: oklch(0.45 0.01 60);
                --accent: oklch(0.96 0.04 50);
                --accent-foreground: oklch(0.15 0.01 60);
                --destructive: oklch(0.55 0.15 25);
                --destructive-foreground: oklch(0.98 0 0);
                --border: oklch(0.91 0.005 60);
                --input: oklch(0.91 0.005 60);
                --ring: oklch(0.65 0.18 50);
                --radius: 0.75rem;
            }

            * {
                font-family: 'Instrument Sans', system-ui, sans-serif;
            }
        </style>
    </head>
    <body class="font-sans antialiased" style="background-color: oklch(0.99 0.005 60);">
        @inertia
    </body>
</html>
