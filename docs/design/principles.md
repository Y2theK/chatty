# Design Principles & System

> **Stack:** Vue 3 / Inertia / Tailwind CSS / shadcn-vue
> **Foundation:** Lucide Icons, OKLCH theming, sidebar layout
> **Philosophy:** Conversational, friendly, intimate. Not a corporate tool -- this is for friends and family.

---

## 1. Design Principles

### 1.1 What We Are NOT

The biggest risk with shadcn-vue + Tailwind is producing a generic chat UI. Flat bubbles, identical message lists, no personality.

**Avoid:**

- Walls of text bubbles with identical styling
- Icon-heavy interfaces where every action needs an icon
- Cold, clinical colour palettes
- Message lists that feel like a spreadsheet
- Overly complex nested menus
- Desktop-only experiences (mobile-first)

### 1.2 What We ARE

This is a **conversation app**. Every screen should feel personal and inviting.

| Principle | How It Shows Up |
|---|---|
| **Conversational** | Message bubbles feel like real conversation. Clear sender distinction. Natural flow. |
| **Intimate, not corporate** | Warm colours, friendly typography. This is family and friends, not a workplace tool. |
| **Instant feedback** | Typing indicators, read receipts, delivery status. You always know what's happening. |
| **Media-forward** | Photos, videos, and links are first-class citizens, not afterthoughts. |
| **Presence awareness** | Online/offline status, last seen, active now. Know when friends are around. |

---

## 2. Visual Identity Markers

Things that make this app recognisable:

- **Coloured avatar initials** -- consistent per-user background colours seeded from user ID
- **Rounded message bubbles** -- soft corners, clear sender alignment (left vs right)
- **Presence indicators** -- online dots, typing animations, read receipts
- **Media previews** -- image thumbnails, link previews, video thumbnails
- **Conversation avatars** -- group photos stacked, individual for private chats
- **Smooth transitions** -- messages slide in, typing indicator bounces

---

## 3. Typography System

### 3.1 Font Stack

**Instrument Sans** or system font for consistency.

| Usage | Weight | Size |
|---|---|---|
| App title | 600 (Semibold) | 20px |
| Conversation name | 600 | 16px |
| Message text | 400 | 14px |
| Timestamp | 400 | 11px |
| Input placeholder | 400 | 14px |
| Caption / meta | 400 | 12px |

### 3.2 Typography Rules

- Line height for messages: 1.4 (tighter for chat feel)
- Timestamps use `--text-muted` colour
- Never use font sizes below 11px for timestamps
- Message text is regular weight for readability

---

## 4. Iconography

### 4.1 Approach

Lucide icons for actions. Icons are used purposefully.

**Rules:**

- Navigation uses text labels + optional small icons
- Message input: send button icon, attach button, emoji picker
- Conversation list: mute, pin, delete icons
- Action menu: more options (three dots)
- Maximum 2 icons visible per conversation list item

### 4.2 Custom Visual Elements

- **Avatar initials** with per-person consistent background colours
- **Online indicator** -- small green dot on avatar
- **Typing indicator** -- animated bouncing dots
- **Message status** -- sent (single check), delivered (double check), read (blue double check)
- **Media thumbnails** -- rounded images with play button for videos

---

## 5. Layout Architecture

### 5.1 Application Shell

```
+--------------------------------------------------+
|                                                   |
|  +-------------+ +-------------------------------+ |
|  |             | |  Conversation Header          | |
|  | Conversation| |  Name / Avatar / Status / ... | |
|  |    List     | +-------------------------------+ |
|  |             | |                               | |
|  |  320px      | |  Message Area                 | |
|  |  fixed      | |  (scrollable)                 | |
|  |             | |                               | |
|  |             | |                               | |
|  |             | +-------------------------------+ |
|  |             | |  Message Input Area           | |
|  +-------------+ |  [Attach] [Input] [Send]      | |
|                  +-------------------------------+ |
+--------------------------------------------------+
```

### 5.2 Conversation List

```
+--------------------------------+
|  Chatty                    [+] |  <- App name + new chat
|  [Search conversations...]     |  <- Search bar
+--------------------------------+
|  [AV] Alice Johnson           |  <- Avatar + name
|       Hey! How are you?       |  <- Last message preview
|       2m ago            [2]    |  <- Time + unread count
+--------------------------------+
|  [AV] Family Group     [5]    |  <- Group avatar
|       Mom: See you tonight     |
|       1h ago                   |
+--------------------------------+
|  [AV] Bob Smith                |
|       That sounds great 👍      |
|       Yesterday                |
+--------------------------------+
```

**List Item Structure:**

- Avatar (40px for private, 48px for groups)
- Name (bold if unread)
- Last message preview (truncated, 1 line)
- Timestamp (right-aligned)
- Unread badge (count, if any)
- Online indicator (green dot on avatar)

**List Behaviour:**

- Active conversation highlighted with accent colour
- Swipe left: mute, pin, delete actions
- Long press: context menu
- New message notification: slide animation

### 5.3 Chat View

```
+--------------------------------------------------+
|  [←] [AV] Alice Johnson           [ℹ] [⋮]        |
|       Online                                    |
+--------------------------------------------------+
|                                                 |
|                    March 19, 2026                |  <- Date separator
|                                                 |
|  [AV]                              Hello!        |  <- Received (left)
|       10:30 AM                                    |
|                                                 |
|                              Hi there!  [AV]    |  <- Sent (right)
|                              10:32 AM           |
|                                                 |
|  [AV]                     [Image preview]       |  <- Sent image
|       10:35 AM                                    |
|                                                 |
|                              [Typing...]         |  <- Typing indicator
|                                                 |
+--------------------------------------------------+
|  [+] [                    Message...         ] [→]|  <- Input area
+--------------------------------------------------+
```

### 5.4 Message Bubble Styles

| Type | Alignment | Background | Max Width |
|---|---|---|---|
| Sent (private) | Right | `--accent-primary` | 70% |
| Received (private) | Left | `--bg-surface-raised` | 70% |
| Sent (group) | Right | `--accent-primary` | 65% |
| Received (group) | Left | `--bg-surface-raised` | 65% |
| System message | Center | Transparent | 80% |

**Bubble Anatomy:**

```
+--------------------------------+
| Message text content           |
|                                |
| 10:30 AM ✓✓                    |  <- Time + read receipt
+--------------------------------+
```

### 5.5 Responsive Behaviour

| Breakpoint | Layout |
|---|---|
| **Mobile** (< 768px) | Full-screen chat, swipe right for conversation list |
| **Tablet** (768 -- 1024px) | Slide-over conversation list |
| **Desktop** (> 1024px) | Side-by-side list + chat |

**Mobile Patterns:**

- Swipe from left edge to reveal conversation list
- Sticky input at bottom
- Pull to load older messages
- Tap header to view conversation details

---

## 6. Interaction Patterns

### 6.1 New Message

- Tap FAB (+) in conversation list
- Opens user search
- Select user to start conversation
- Or select "Create Group"

### 6.2 Sending a Message

1. Type in input field
2. Press send button or Enter key
3. Message appears immediately (optimistic UI)
4. Single checkmark when sent to server
5. Double checkmark when delivered
6. Blue double checkmark when read

### 6.3 Message Actions

Long press on message reveals:

- Reply
- Forward
- Copy
- Delete (own messages only)

### 6.4 Typing Indicator

When someone is typing:

```
Alice is typing...
```

- Animated bouncing dots
- Appears below last message
- Auto-hides after 5 seconds of no typing

### 6.5 Online / Offline Status

| Status | Indicator |
|---|---|
| Online now | Green dot on avatar |
| Offline | No indicator |
| Last seen: "2m ago" | Shown below name in header |

**Privacy Note:** Users can disable "last seen" visibility.

### 6.6 Media Handling

- **Images:** Thumbnail preview, tap to fullscreen, pinch to zoom
- **Videos:** Thumbnail with play button, tap to play inline
- **Links:** Auto-preview card with title, description, thumbnail

### 6.7 Empty States

| Screen | Message |
|---|---|
| No conversations | "No chats yet. Start a conversation!" |
| No messages in chat | "Say hi! 👋" |
| No search results | "No users found" |
| Group empty | "No messages yet. Start the conversation!" |

### 6.8 Loading States

- **Initial load:** Skeleton screens for conversation list
- **Chat load:** Skeleton bubbles (varied widths)
- **Sending:** Message appears with opacity 0.7, full opacity when sent
- **Image uploading:** Progress indicator overlay

---

## 7. Animation & Micro-Interactions

| Interaction | Animation | Duration |
|---|---|---|
| New message | Slide up + fade in | 200ms |
| Send message | Slide to right | 150ms |
| Typing indicator | Bounce loop | Continuous |
| Read receipt | Checkmarks turn blue | 200ms |
| Conversation list item | Slide in from left | 200ms |
| Unread badge | Scale pop | 150ms |
| Pull to refresh | Spinner rotation | Continuous |
| Image preview | Fade in | 200ms |

---

## 8. Accessibility Requirements

| Requirement | Implementation |
|---|---|
| **Colour contrast** | All text meets WCAG AA (4.5:1 minimum) |
| **Keyboard navigation** | Tab through messages, Enter to send |
| **Screen readers** | ARIA labels, message announcements |
| **Reduced motion** | Respect `prefers-reduced-motion` |
| **Focus management** | Auto-focus input when opening chat |
| **Message identification** | Each message has unique ID for navigation |

---

## 9. Component Inventory

| Component | Description | Used In |
|---|---|---|
| `ConversationList` | Scrollable list of conversations | Sidebar |
| `ConversationItem` | Single conversation row | List |
| `AvatarInitials` | User avatar with initials | Throughout |
| `OnlineIndicator` | Green dot for online status | Throughout |
| `UnreadBadge` | Count badge for unread messages | List items |
| `ChatHeader` | Conversation name, avatar, actions | Chat view |
| `MessageList` | Scrollable message container | Chat view |
| `MessageBubble` | Individual message | Message list |
| `MessageInput` | Text input with send button | Chat view |
| `TypingIndicator` | "User is typing..." animation | Chat view |
| `MessageStatus` | Checkmarks for sent/delivered/read | Bubbles |
| `MediaPreview` | Image/video thumbnail | Bubbles |
| `LinkPreview` | Card showing link metadata | Bubbles |
| `DateSeparator` | Date divider between message groups | Message list |
| `SearchBar` | Search conversations/users | List |
| `UserSearch` | Search and select users | New chat |
| `MessageActions` | Long-press context menu | Messages |
| `EmptyState` | Empty list illustrations | Lists |
| `TypingDots` | Animated bouncing dots | Indicator |
