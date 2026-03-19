# Colour System

The colour system uses warm, friendly colours suited for personal messaging. All values are specified in OKLCH colour space for perceptual uniformity.

---

## 1. Foundation Palette (Light Mode)

The foundation palette provides the base surface colours, borders, and text tones.

| Token | OKLCH Value | Usage |
|---|---|---|
| `--bg-page` | `oklch(0.985 0.003 80)` | Page background (warm off-white) |
| `--bg-surface` | `oklch(1 0 0)` | Cards, panels (pure white) |
| `--bg-surface-raised` | `oklch(0.993 0.002 80)` | Elevated surfaces, received message bubbles |
| `--bg-input` | `oklch(0.98 0.002 80)` | Input fields |
| `--border-default` | `oklch(0.92 0.005 80)` | Default borders |
| `--border-subtle` | `oklch(0.95 0.003 80)` | Subtle dividers |
| `--text-primary` | `oklch(0.15 0.01 60)` | Primary text (warm black) |
| `--text-secondary` | `oklch(0.45 0.01 60)` | Secondary text |
| `--text-muted` | `oklch(0.60 0.008 60)` | Timestamps, placeholders |
| `--text-inverse` | `oklch(0.98 0 0)` | Text on dark backgrounds |

---

## 2. Brand / Accent Colours

The brand palette uses a friendly blue as the primary accent, with a soft green secondary.

| Token | OKLCH Value | Usage |
|---|---|---|
| `--accent-primary` | `oklch(0.55 0.15 250)` | Sent messages, primary buttons, links |
| `--accent-primary-hover` | `oklch(0.48 0.17 250)` | Hover state |
| `--accent-primary-subtle` | `oklch(0.95 0.03 250)` | Selected states, highlights |
| `--accent-secondary` | `oklch(0.65 0.12 160)` | Online status, success states |
| `--accent-secondary-subtle` | `oklch(0.93 0.04 160)` | Online indicator backgrounds |

---

## 3. Semantic Colours

| Token | OKLCH Value | Usage |
|---|---|---|
| `--status-online` | `oklch(0.65 0.12 160)` | Online indicator (green) |
| `--status-offline` | `oklch(0.60 0.008 60)` | Offline, muted |
| `--status-sent` | `oklch(0.55 0.01 60)` | Message sent (gray check) |
| `--status-delivered` | `oklch(0.55 0.01 60)` | Message delivered (gray double check) |
| `--status-read` | `oklch(0.55 0.15 250)` | Message read (blue double check) |
| `--status-typing` | `oklch(0.60 0.008 60)` | Typing indicator |
| `--status-unread` | `oklch(0.55 0.15 250)` | Unread badge background |

---

## 4. Conversation Type Colours

Distinct colours for different conversation types in the list.

| Type | Usage |
|---|---|
| Private chat | Avatar with user's colour |
| Group chat | Avatar with gradient or first letter colour |
| Unread | Bold text + accent badge |
| Muted | Muted text colour |
| Pinned | Pin icon indicator |

---

## 5. Message Bubble Colours

### Sent Messages (Right-aligned)

| Token | OKLCH Value | Usage |
|---|---|---|
| `--bubble-sent-bg` | `oklch(0.55 0.15 250)` | Sent message background |
| `--bubble-sent-text` | `oklch(0.98 0 0)` | Sent message text |
| `--bubble-sent-border` | `oklch(0.48 0.17 250)` | Sent bubble border (subtle) |

### Received Messages (Left-aligned)

| Token | OKLCH Value | Usage |
|---|---|---|
| `--bubble-received-bg` | `oklch(0.993 0.002 80)` | Received message background |
| `--bubble-received-text` | `oklch(0.15 0.01 60)` | Received message text |
| `--bubble-received-border` | `oklch(0.92 0.005 80)` | Received bubble border |

---

## 6. Avatar Colours

Avatar backgrounds are generated from user ID hash for consistency.

| Index | OKLCH Value | Hex Approx |
|---|---|---|
| 1 | `oklch(0.65 0.15 250)` | #6B8FD4 |
| 2 | `oklch(0.65 0.15 160)` | #5BBFA0 |
| 3 | `oklch(0.65 0.15 320)` | #C48BD4 |
| 4 | `oklch(0.65 0.15 30)` | #D4896B |
| 5 | `oklch(0.65 0.15 90)` | #C4C86B |
| 6 | `oklch(0.65 0.15 200)` | #6BB5D4 |
| 7 | `oklch(0.65 0.15 280)` | #9B7DD4 |
| 8 | `oklch(0.65 0.15 350)` | #D46B8A |

---

## 7. Input & Action Colours

| Token | OKLCH Value | Usage |
|---|---|---|
| `--input-bg` | `oklch(0.98 0.002 80)` | Input field background |
| `--input-border` | `oklch(0.92 0.005 80)` | Input field border |
| `--input-border-focus` | `oklch(0.55 0.15 250)` | Input field focus border |
| `--button-send` | `oklch(0.55 0.15 250)` | Send button |
| `--button-send-hover` | `oklch(0.48 0.17 250)` | Send button hover |
| `--button-attach` | `oklch(0.55 0.01 60)` | Attachment button |

---

## 8. Dark Mode (Future)

| Token | Light | Dark |
|---|---|---|
| `--bg-page` | `oklch(0.985 0.003 80)` | `oklch(0.15 0.02 250)` |
| `--bg-surface` | `oklch(1 0 0)` | `oklch(0.20 0.02 250)` |
| `--text-primary` | `oklch(0.15 0.01 60)` | `oklch(0.95 0 0)` |
| `--bubble-sent-bg` | `oklch(0.55 0.15 250)` | `oklch(0.50 0.15 250)` |
| `--bubble-received-bg` | `oklch(0.993 0.002 80)` | `oklch(0.25 0.02 250)` |

---

## 9. Design Rationale

- **Warm backgrounds** -- slight cream tint makes the app feel friendly, not clinical
- **Blue accent** -- universally associated with messaging and communication
- **Green online indicator** -- instantly recognizable as "active/present"
- **Blue read receipts** -- clear distinction from gray sent/delivered
- **Avatar colours** -- vibrant but not harsh, each user gets a consistent colour
- **Message bubbles** -- rounded corners (12px) feel modern and approachable
