# 🪝 HookPipe

> Production-grade webhook processing system built with Laravel — receive, validate, store, and process webhook events asynchronously using Jobs, Queues, and Events.

---

## What is HookPipe?

HookPipe is a clean, scalable backend system for handling incoming webhook payloads. It exposes a single endpoint that validates incoming requests, persists events to the database, and dispatches background jobs to process them — keeping your response time fast and your processing reliable.

---

## Features

- ✅ `POST /webhook` endpoint for receiving payloads
- ✅ Payload validation before any processing
- ✅ Persistent event storage in the database
- ✅ Asynchronous job dispatch via Laravel Queues
- ✅ Event broadcasting using Laravel Events
- ✅ Clean, production-ready architecture

---

## Tech Stack

- **Framework:** Laravel 11
- **Queue Driver:** Redis (or database for local dev)
- **Language:** PHP 8.2+

---

## How It Works

```
Incoming Webhook
      │
      ▼
POST /webhook
      │
      ▼
Validate Payload ──► 422 on failure
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
QUEUE_CONNECTION=redis
DB_CONNECTION=mysql
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
curl -X POST http://localhost:8000/webhook \
  -H "Content-Type: application/json" \
  -d '{"event": "order.created", "payload": {"id": 1, "amount": 5000}}'
```

---

## Project Structure

```
app/
├── Http/
│   └── Controllers/
│       └── WebhookController.php   # Validates & stores incoming events
├── Jobs/
│   └── ProcessWebhookJob.php       # Background job for processing
├── Events/
│   └── WebhookProcessed.php        # Fired after successful processing
├── Models/
│   └── WebhookEvent.php            # Stores raw event data
database/
└── migrations/
    └── create_webhook_events_table.php
```

---

## API Reference

### `POST /webhook`

**Request Body:**

```json
{
  "event": "order.created",
  "payload": {
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

## Concepts Demonstrated

| Concept | Implementation |
|---|---|
| **Jobs** | `ProcessWebhookJob` handles async processing logic |
| **Queues** | Jobs are pushed to the queue, not processed in the request cycle |
| **Events** | `WebhookProcessed` event fired after job completes |
| **Validation** | Laravel Form Request validates payload before storage |
| **Persistence** | Every webhook is stored — nothing is lost |

---

## License

MIT# hookpipe
