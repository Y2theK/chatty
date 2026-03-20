# Video Call Implementation (LiveKit Cloud)

**Stack:** Laravel + Vue 3 + Inertia + Laravel Reverb
**Provider:** [LiveKit Cloud](https://livekit.io) — managed, no infrastructure to run
**SDK:** `agence104/livekit-server-sdk` (PHP) + `livekit-client` (JS)
**Scope:** 1-on-1 and small group calls (up to ~6 participants)

---

## How It Works (High Level)

```
User clicks "Call"
  → Laravel generates a LiveKit access token (room + user identity)
  → Broadcasts CallInitiated on each member's personal user.{id} channel via Reverb
  → Dashboard.vue receives it everywhere (not just on the conversation page)
  → Caller's VideoCall.vue mounts and connects to LiveKit room

Other members receive CallInitiated via their personal Echo channel
  → IncomingCall.vue modal appears (Accept / Decline) — works from any page
  → On Accept: POST /call/join → get token → join same LiveKit room

User clicks Leave (↪)
  → Disconnects from LiveKit only for themselves
  → Room stays alive for other participants
  → Chat header shows green "Rejoin" button
  → Clicking Rejoin: POST /call/join silently (no broadcast) → reconnect

User clicks End (📵)
  → Disconnects from LiveKit
  → POST /call/end → broadcasts CallEnded to conversation channel
  → All clients dismiss the call UI
  → "Video call ended" system message posted to conversation
```

**Reverb = signals call events between members.
LiveKit = handles all WebRTC, media, and SFU internally.**

---

## Prerequisites

- Sign up at [https://livekit.io](https://livekit.io) and create a project
- Get credentials from LiveKit Cloud dashboard and add to `.env`:

```env
LIVEKIT_API_KEY=your_api_key
LIVEKIT_API_SECRET=your_api_secret
LIVEKIT_URL=wss://your-project.livekit.cloud
```

---

## File Breakdown

### New Files

| File | Purpose |
|---|---|
| `app/Services/LiveKitService.php` | Generates signed JWT access tokens |
| `app/Events/CallInitiated.php` | Broadcasts call started to each member's `user.{id}` channel |
| `app/Events/CallEnded.php` | Broadcasts call ended to `conversation.{id}` channel |
| `app/Http/Controllers/CallController.php` | `start`, `join`, `end` actions |
| `database/migrations/..._add_type_column_to_chat_messages_table.php` | Adds `type` column for system messages |
| `resources/js/Components/VideoCall.vue` | Full-screen call UI (video grid, controls) |
| `resources/js/Components/IncomingCall.vue` | Incoming call modal (accept/decline) |

### Modified Files

| File | Change |
|---|---|
| `config/services.php` | Add `livekit` credentials block |
| `routes/web.php` | Add 3 call routes |
| `resources/js/Pages/Dashboard.vue` | Mount IncomingCall + VideoCall, listen on `user.{id}` channel |
| `resources/js/Pages/Chat/Chat.vue` | Call/Rejoin button, `call:started`/`call:ended` window events |

---

## Architecture Decisions

### Why broadcast on `user.{id}` channels, not `conversation.{id}`?

`CallInitiated` broadcasts on each member's personal channel so they receive the notification regardless of which page they're currently viewing. If we only broadcast on `conversation.{id}`, members who are on a different conversation or the dashboard miss the call entirely.

`CallEnded` still broadcasts on `conversation.{id}` because by then all active call participants are on that page.

### Why use window custom events between Chat.vue and Dashboard.vue?

`VideoCall` and `IncomingCall` live in `Dashboard.vue` (so they persist across Inertia navigation). `Chat.vue` is a child rendered via `<slot>`. Since Inertia pages can't directly emit to their layout, we use browser custom events (`call:started`, `call:ended`, `call:left`) as a bridge.

### Leave vs End

| Action | Button | What happens |
|---|---|---|
| **Leave** | `↪` (gray) | You disconnect. Room stays alive. Rejoin button appears in chat header. |
| **End** | `📵` (red) | Everyone disconnects. `CallEnded` broadcast. "Video call ended" message posted. |

---

## Backend

### LiveKitService

```php
// app/Services/LiveKitService.php
generateToken(roomName, userId, userName): string
  → AccessTokenOptions: identity="user-{id}", name, ttl=3600
  → VideoGrant: roomJoin, roomName, canPublish, canSubscribe
  → AccessToken(apiKey, apiSecret, $options)->setGrant($grant)->toJwt()

getRoomName(conversationId): string
  → returns "conv-{conversationId}"
```

> **Important:** `AccessTokenOptions` is passed as the **3rd constructor argument** to `AccessToken`, not via a setter. The grant method is `setGrant()`, not `addGrant()`.

### CallController

```
POST /conversations/{conversation}/call/start
  → load conversation users
  → generateToken for caller
  → broadcast CallInitiated (on each member's user.{id} channel, excluding caller)
  → return { token, room_name, livekit_url }

POST /conversations/{conversation}/call/join
  → generateToken for joiner (room_name from request body)
  → return { token, room_name, livekit_url }

POST /conversations/{conversation}/call/end
  → create ChatMessage { type: 'system', message: 'Video call ended' }
  → broadcast ChatMessageSent (so it appears in real-time)
  → broadcast CallEnded (on conversation.{id})
  → return { success: true }
```

### System Messages

Added `type` column to `chat_messages` (default: `'message'`). Call end creates a message with `type = 'system'`. These are rendered differently in the UI (centered pill with video icon).

---

## Frontend

### VideoCall.vue

**Props:** `token`, `livekitUrl`, `roomName`, `conversationId`, `currentUser`
**Emits:** `call-left`, `call-ended`

**Connection (onMounted):**
```
1. new Room()  — no options (avoids structuredClone Vite bug)
2. Register RoomEvent listeners
3. room.connect(livekitUrl, token)  — in its own try/catch
4. createLocalVideoTrack() + createLocalAudioTrack()  — separate from connect
5. publishTrack() for both tracks
6. Attach local video track to <video> ref
```

> **Gotcha:** Do NOT use `room.localParticipant.enableCameraAndMicrophone()` — it triggers a `structuredClone` error in Vite. Use `createLocalVideoTrack()` + `createLocalAudioTrack()` + `publishTrack()` separately.

> **Gotcha:** Do NOT pass `{ adaptiveStream: true, dynacast: true }` to `new Room()` — same `structuredClone` issue.

**Controls:**
- 🎤 Mic toggle → `localParticipant.setMicrophoneEnabled()`
- 📷 Camera toggle → `localParticipant.setCameraEnabled()`
- `↪` Leave → `room.disconnect()` + `emit('call-left')` (no server call)
- `📵` End → `room.disconnect()` + POST `/call/end` + `emit('call-ended')`

**onBeforeUnmount:** always calls `room.disconnect()`

### IncomingCall.vue

**Props:** `callerName`, `callerImage`, `conversationId`, `roomName`
**Emits:** `accepted { token, livekitUrl }`, `declined`

On Accept: POST `/call/join` → emit `accepted` with token

### Dashboard.vue (call orchestrator)

Owns `activeCall`, `incomingCall`, `activeRoom` as local refs.

**onMounted:**
- Subscribes to `user.{id}` Echo channel → listens for `CallInitiated`
- Registers `call:started` and `call:ended` window event listeners

**Call flow handlers:**
```
onCallAccepted  → set activeCall + activeRoom, clear incomingCall
onCallDeclined  → clear incomingCall
onCallLeft      → clear activeCall, keep activeRoom, dispatch call:left
onCallEnded     → clear activeCall + activeRoom, dispatch call:ended
```

### Chat.vue (call trigger)

**Call button in header:**
```html
<!-- Shows "Rejoin" (green) if user left this conversation's call -->
<Button v-if="activeRoom && activeRoom.conversationId === conversation.id"
        @click="rejoinCall">Rejoin</Button>
<Button v-else @click="startCall">Call</Button>
```

**startCall():** POST `/call/start` → dispatch `call:started` window event
**rejoinCall():** POST `/call/join` → dispatch `call:started` window event (no broadcast)

**window event listeners (onMounted):**
- `call:left` → set local `activeRoom` if conversationId matches
- `call:ended` → clear local `activeRoom`

**Echo listener:**
- `CallEnded` → dispatch `call:ended` window event + clear `activeRoom`

### System message rendering

```html
<template v-for="message in messages" :key="message.id">
  <!-- System message pill -->
  <div v-if="message.type === 'system'" class="flex justify-center">
    <div class="...rounded-full">
      <Video /> {{ message.message }} · time
    </div>
  </div>
  <!-- Regular message -->
  <div v-else>...</div>
</template>
```

---

## Data Flow

```
┌─────────────────────────────────────────────────────────┐
│  User A — Caller                                        │
│  Clicks "Call" → POST /call/start                       │
│  → generateToken("conv-123")                            │
│  → broadcast CallInitiated on user.B.id, user.C.id...  │
│  → dispatch call:started → Dashboard sets activeCall    │
│  → VideoCall.vue mounts → connects to LiveKit           │
└─────────────────────────────────────────────────────────┘
           │ Reverb (user.{id} channels)
           ↓
┌─────────────────────────────────────────────────────────┐
│  Users B, C, D — anywhere in the app                    │
│  Dashboard.vue hears CallInitiated (personal channel)   │
│  → IncomingCall.vue modal appears                       │
│  → Accept → POST /call/join → generateToken same room   │
│  → dispatch call:started → VideoCall.vue mounts         │
│  → connects to same LiveKit room                        │
└─────────────────────────────────────────────────────────┘
```

---

## UI Layout

```
┌───────────────────────────────────────────────────────┐
│  [Fixed full-screen overlay — z-50 — bg-gray-900]     │
│                                                       │
│  ┌─────────────┐  ┌─────────────┐                     │
│  │   User A    │  │   User B    │                     │
│  │  (local)    │  │  (remote)   │                     │
│  └─────────────┘  └─────────────┘                     │
│  ┌─────────────┐  ┌─────────────┐                     │
│  │   User C    │  │   User D    │                     │
│  └─────────────┘  └─────────────┘                     │
│                                                       │
│        [🎤]   [📷]   [↪ Leave]   [📵 End]             │
└───────────────────────────────────────────────────────┘
```

Grid auto-adjusts:
- 1 tile → centered
- 2 tiles → `grid-cols-1 md:grid-cols-2`
- 3–4 tiles → `grid-cols-2`
- 5–6 tiles → `grid-cols-2 md:grid-cols-3`

---

## Known Limitations

- **Browser back button** dismisses the call UI (Inertia unmounts the component). Accepted behaviour — not fixed.
- **Token expiry:** Tokens last 1 hour. Calls lasting longer will drop.
- **6+ participants:** Mesh degrades. Would need a dedicated SFU config.
