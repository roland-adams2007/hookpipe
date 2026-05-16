# 🪝 HookPipe

> Production-grade webhook processing system built with Laravel — receive, validate, store, and process webhook events asynchronously using Jobs, Queues, and Events.

---

## What is HookPipe?

HookPipe is a clean, scalable backend system for handling incoming webhook payloads. It exposes a token-based endpoint per user that validates incoming requests, persists events to the database, and dispatches background jobs to process them — keeping your response time fast and your processing reliable.

Users can also dispatch outbound webhooks to external callback URLs, test payloads against a local echo endpoint, and monitor incoming events via a live polling interface.

---

## Features

- ✅ Token-based webhook URL per user (`POST /token/{webhook_token}`)
- ✅ Payload validation before any processing
- ✅ Persistent event storage in the database
- ✅ Asynchronous job dispatch via Laravel Queues
- ✅ Event broadcasting using Laravel Events
- ✅ Outbound webhook dispatch with callback URL support
- ✅ Local echo endpoint for test mode
- ✅ Live polling interface for real-time event monitoring
- ✅ Auth system (register, login, logout)
- ✅ Clean, production-ready architecture

---

## Tech Stack

- **Framework:** Laravel 11
- **Queue Driver:** Database
- **Database:** PostgreSQL
- **Language:** PHP 8.2+

---

## How It Works

```
Incoming Webhook
      │
      ▼
POST /token/{webhook_token}
      │
      ▼
Validate Payload (WebhookRequest) ──► 422 on failure
      │
      ▼
Store WebhookEvent (DB)
      │
      ▼
Dispatch ProcessWebhookJob
      │
      ▼
Queue Worker picks up job
      │
      ▼
Fire WebhookProcessed Event
```

Outbound flow (user-initiated):

```
User submits payload + callback URL
      │
      ▼
WebhookController stores event
      │
      ▼
Dispatch SendWebhookJob
      │
      ▼
Job POSTs payload to callback URL
```

---

## Getting Started

### 1. Clone the repo

```bash
git clone https://github.com/roland-adams2007/hookpipe.git
cd hookpipe
```

### 2. Install dependencies

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Configure your environment

```env
QUEUE_CONNECTION=database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hookpipe
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Start the queue worker

```bash
php artisan queue:work
```

### 6. Send a test webhook

```bash
curl -X POST http://localhost:8000/token/{your_webhook_token} \
  -H "Content-Type: application/json" \
  -d '{"event": "order.created", "data": {"id": 1, "amount": 5000}}'
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php          # Register, login, logout
│   │   ├── HomeController.php          # Dashboard / landing
│   │   ├── WebhookController.php       # Receive, store & dispatch webhooks
│   │   └── TokenController.php         # Token-based webhook ingestion
│   └── Requests/
│       └── WebhookRequest.php          # Validates incoming webhook payload
├── Jobs/
│   ├── ProcessWebhookJob.php           # Processes stored webhook events
│   └── SendWebhookJob.php              # Dispatches outbound webhook to callback URL
├── Events/
│   └── WebhookProcessed.php            # Fired after successful processing
├── Models/
│   ├── User.php                        # Auth user with webhook_token
│   └── WebhookEvent.php               # Stores raw event data
database/
└── migrations/
    ├── create_users_table.php
    ├── create_jobs_table.php
    └── create_webhook_events_table.php
```

---

## API Reference

### `POST /token/{webhook_token}`

Receives an incoming webhook event for the user identified by the token.

**Request Body:**

```json
{
  "event": "order.created",
  "data": {
    "id": 1,
    "amount": 5000
  }
}
```

**Success Response — `202 Accepted`:**

```json
{
  "message": "Webhook received",
  "event_id": "uuid-here"
}
```

**Validation Failure — `422 Unprocessable Entity`:**

```json
{
  "message": "The event field is required."
}
```

---

### `POST /webhook/send`

Dispatches an outbound webhook to an external callback URL.

**Request Body:**

```json
{
  "callback_url": "https://your-server.com/receive",
  "payload": {
    "event": "user.payment_updated",
    "data": { "id": 1 }
  }
}
```

---

### `GET /webhook/echo`

Local echo endpoint used in test mode. Returns the received payload as-is — no external URL needed.

---

### `GET /webhook/latest`

Returns the most recently received webhook event. Used by the polling interface.

---

### `GET /webhook/verify/{id}`

Returns the stored log and delivery status for a given event ID.

---

## Concepts Demonstrated

| Concept | Implementation |
|---|---|
| **Jobs** | `ProcessWebhookJob` handles async processing; `SendWebhookJob` handles outbound delivery |
| **Queues** | Database-backed queue — jobs are never processed in the request cycle |
| **Events** | `WebhookProcessed` event fired after job completes |
| **Validation** | `WebhookRequest` validates payload before storage |
| **Persistence** | Every webhook is stored — nothing is lost |
| **Auth** | `AuthController` handles registration, login and logout |
| **Token routing** | Each user gets a unique `webhook_token` embedded in their personal URL |

---

## License

MIT