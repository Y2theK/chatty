# UI Implementation Plan

> Based on the design system in `colour-system.md` and `principles.md`
> **Stack:** Vue 3 / Inertia / Tailwind CSS / shadcn-vue

---

## Phase 1: CSS Foundation

### 1.1 Update Tailwind Config
**File:** `tailwind.config.cjs`

Add custom color tokens matching OKLCH values from design docs:

```js
colors: {
  // Foundation
  'bg-page': 'oklch(0.985 0.003 80)',
  'bg-surface': 'oklch(1 0 0)',
  'bg-surface-raised': 'oklch(0.993 0.002 80)',
  'bg-input': 'oklch(0.98 0.002 80)',
  // Borders
  'border-default': 'oklch(0.92 0.005 80)',
  'border-subtle': 'oklch(0.95 0.003 80)',
  // Text
  'text-primary': 'oklch(0.15 0.01 60)',
  'text-secondary': 'oklch(0.45 0.01 60)',
  'text-muted': 'oklch(0.60 0.008 60)',
  // Brand
  'accent-primary': 'oklch(0.55 0.15 250)',
  'accent-primary-hover': 'oklch(0.48 0.17 250)',
  'accent-primary-subtle': 'oklch(0.95 0.03 250)',
  'accent-secondary': 'oklch(0.65 0.12 160)',
  // Bubbles
  'bubble-sent': 'oklch(0.55 0.15 250)',
  'bubble-sent-text': 'oklch(0.98 0 0)',
  'bubble-received': 'oklch(0.993 0.002 80)',
  'bubble-received-text': 'oklch(0.15 0.01 60)',
  // Status
  'status-online': 'oklch(0.65 0.12 160)',
  'status-offline': 'oklch(0.60 0.008 60)',
  'status-read': 'oklch(0.55 0.15 250)',
}
```

### 1.2 Update CSS Variables
**File:** `resources/views/app.blade.php`

Add CSS custom properties for all design tokens.

---

## Phase 2: Layout & Shell

### 2.1 Authenticated Layout
**File:** `resources/js/Layouts/AuthenticatedLayout.vue`

- Implement sidebar layout structure (320px fixed sidebar)
- Add responsive behavior (mobile slide-over)

### 2.2 Sidebar
**File:** `resources/js/Components/Sidebar.vue`

- Warm background color
- App header with "Chatty" title
- Search bar styling
- Conversation list items

---

## Phase 3: Chat Page

### 3.1 Chat View
**File:** `resources/js/Pages/Chat/Chat.vue`

| Component | Implementation |
|-----------|---------------|
| ChatHeader | Avatar initials, online/offline status, action buttons |
| MessageBubble (sent) | Blue background, white text, right-aligned, 70% max-width |
| MessageBubble (received) | White background, dark text, left-aligned, 70% max-width |
| MessageList | Date separators, typing indicator, smooth scroll |
| MessageInput | Attachment, emoji picker, send button |
| MessageStatus | ✓ ✓✓ ✓✓(blue) for sent/delivered/read |
| TypingIndicator | "User is typing..." + bouncing dots |

### 3.2 Key Styling Changes

```vue
<!-- Sent message bubble -->
<div class="bg-accent-primary text-white rounded-xl px-4 py-2 max-w-[70%]">
  <p>{{ message }}</p>
  <span class="text-xs opacity-80">10:30 AM ✓✓</span>
</div>

<!-- Received message bubble -->
<div class="bg-white text-text-primary rounded-xl px-4 py-2 max-w-[70%]">
  <p>{{ message }}</p>
  <span class="text-xs text-muted">10:30 AM</span>
</div>
```

---

## Phase 4: Auth Pages

### 4.1 Guest Layout
**File:** `resources/js/Layouts/GuestLayout.vue`

- Warm page background (`--bg-page`)
- Centered card with `--bg-surface`
- App logo

### 4.2 Login Page
**File:** `resources/js/Pages/Auth/Login.vue`

- Update input styles with design tokens
- Style login button with `--accent-primary`
- Update link styles

### 4.3 Register Page
**File:** `resources/js/Pages/Auth/Register.vue`

- Consistent styling with Login page

---

## Phase 5: Reusable Components

### 5.1 AvatarInitials
**New component:** `resources/js/Components/AvatarInitials.vue`

- Generate background color from user ID hash
- Show user initials
- Support online indicator
- 8-color palette from design docs

```vue
<!-- Usage -->
<AvatarInitials :user="user" :show-online="true" size="md" />
```

### 5.2 OnlineIndicator
**Update:** `resources/js/Components/Sidebar.vue`

- Green dot positioning
- Proper sizing (8px default)

### 5.3 UnreadBadge
- Blue accent background
- White text
- Scale animation on appear

### 5.4 DateSeparator
**New component:** `resources/js/Components/DateSeparator.vue`

- "Today", "Yesterday", or formatted date
- Subtle styling

### 5.5 EmptyState
**New component:** `resources/js/Components/EmptyState.vue`

- Friendly messages
- Optional illustration

### 5.6 TypingIndicator
**New component:** `resources/js/Components/TypingIndicator.vue`

- Bouncing dots animation
- User name display

---

## Phase 6: Profile Page

### 6.1 Edit Profile
**File:** `resources/js/Pages/Profile/Edit.vue`

- Use `--bg-surface` for cards
- Consistent form styling
- Update button styles

---

## Phase 7: Animations & Polish

### 7.1 Tailwind Animations
Add to `tailwind.config.cjs`:

```js
keyframes: {
  'slide-up': {
    '0%': { opacity: '0', transform: 'translateY(10px)' },
    '100%': { opacity: '1', transform: 'translateY(0)' },
  },
  'slide-right': {
    '0%': { opacity: '0', transform: 'translateX(-10px)' },
    '100%': { opacity: '1', transform: 'translateX(0)' },
  },
  'bounce-dot': {
    '0%, 100%': { transform: 'translateY(0)' },
    '50%': { transform: 'translateY(-4px)' },
  },
  'scale-pop': {
    '0%': { transform: 'scale(0)' },
    '100%': { transform: 'scale(1)' },
  },
},
animation: {
  'slide-up': 'slide-up 200ms ease-out',
  'slide-right': 'slide-right 150ms ease-out',
  'bounce-dot': 'bounce-dot 1.4s infinite',
  'scale-pop': 'scale-pop 150ms ease-out',
}
```

### 7.2 Usage Examples

```vue
<!-- New message -->
<div class="animate-slide-up">New message</div>

<!-- Typing dots -->
<div class="flex gap-1">
  <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce-dot" style="animation-delay: 0ms"></span>
  <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce-dot" style="animation-delay: 200ms"></span>
  <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce-dot" style="animation-delay: 400ms"></span>
</div>
```

---

## Implementation Order

1. ✅ **Phase 1:** CSS Foundation (tailwind.config.cjs + app.blade.php)
2. **Phase 2:** Layout & Shell (AuthenticatedLayout + Sidebar)
3. **Phase 3:** Chat Page (biggest visual impact)
4. **Phase 4:** Auth Pages
5. **Phase 5:** Reusable Components
6. **Phase 6:** Profile Page
7. **Phase 7:** Animations & Polish

---

## Design Token Reference

### Foundation Palette
| Token | OKLCH Value | Usage |
|-------|-------------|-------|
| `--bg-page` | `oklch(0.985 0.003 80)` | Page background |
| `--bg-surface` | `oklch(1 0 0)` | Cards, panels |
| `--bg-surface-raised` | `oklch(0.993 0.002 80)` | Received bubbles |
| `--text-primary` | `oklch(0.15 0.01 60)` | Primary text |
| `--text-secondary` | `oklch(0.45 0.01 60)` | Secondary text |
| `--text-muted` | `oklch(0.60 0.008 60)` | Timestamps |

### Brand Colors
| Token | OKLCH Value | Usage |
|-------|-------------|-------|
| `--accent-primary` | `oklch(0.55 0.15 250)` | Sent messages, buttons |
| `--accent-secondary` | `oklch(0.65 0.12 160)` | Online indicator |

### Typography
| Usage | Weight | Size |
|-------|--------|------|
| App title | 600 | 20px |
| Conversation name | 600 | 16px |
| Message text | 400 | 14px |
| Timestamp | 400 | 11px |

### Avatar Colors (by index)
| Index | OKLCH | Approx Hex |
|-------|-------|------------|
| 1 | `oklch(0.65 0.15 250)` | #6B8FD4 |
| 2 | `oklch(0.65 0.15 160)` | #5BBFA0 |
| 3 | `oklch(0.65 0.15 320)` | #C48BD4 |
| 4 | `oklch(0.65 0.15 30)` | #D4896B |
| 5 | `oklch(0.65 0.15 90)` | #C4C86B |
| 6 | `oklch(0.65 0.15 200)` | #6BB5D4 |
| 7 | `oklch(0.65 0.15 280)` | #9B7DD4 |
| 8 | `oklch(0.65 0.15 350)` | #D46B8A |
