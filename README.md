# Meeting Room Booking — Microservice

> 🇷🇺 [Русская версия](#-быстрый-старт) | 🇬🇧 [English version](#-quick-start)

---

## 🇬🇧 Quick Start

```bash
git clone https://github.com/vachagan-nahapetyan-job/meeting-room-booking.git
cd meeting-room-booking
docker compose up  --build
```

> This is the only command needed. Everything else is automatic.

First run takes **~1-2 minutes** — composer installs dependencies inside the container.

What happens automatically on start:
1. `composer install` — install dependencies
2. `php artisan key:generate` — generate APP_KEY
3. `php artisan migrate` — create tables
4. `php artisan db:seed` — seed 4 meeting rooms (Alpha, Beta, Gamma, Delta)

---

Verify it works.The API will be available at:

```
http://localhost:8080

```
 
## 📖 Swagger UI 
```
http://localhost:8080/api/documentation

```

## Rooms List 

```
http://localhost:8080/api/rooms

```


## 🇷🇺 Быстрый старт

```bash
git clone https://github.com/vachagan-nahapetyan-job/meeting-room-booking.git
cd meeting-room-booking
docker compose up -d --build
```

> Это единственная команда. Ничего больше не нужно.

Первый запуск занимает **~1-2 минуты** — composer устанавливает зависимости внутри контейнера.

Автоматически при старте:
1. `composer install` — установка зависимостей
2. `php artisan key:generate` — генерация APP_KEY
3. `php artisan migrate` — создание таблиц
4. `php artisan db:seed` — 4 тестовые переговорки (Alpha, Beta, Gamma, Delta)

Проверка:

```
http://localhost:8080

```

## Stack

- **PHP** 8.3
- **Laravel** 12
- **MySQL** 8.0
- **Nginx** 1.25
- **Docker** / Docker Compose

---

## API Reference

### Base URL

```
http://localhost:8080/api
```

---

### Rooms

#### List all rooms

```
GET /api/rooms
```

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Alpha",
      "location": "Floor 1, Room 101",
    },
    {
      "id": 2,
      "name": "Beta",
      "location": "Floor 1, Room 102",
    }
  ]
}
```

---

#### Room details

```
GET /api/rooms/{id}
```

**Response 200:**
```json
{
  "data": {
    "id": 1,
    "name": "Alpha",
    "location": "Floor 1, Room 101",
  }
}
```

---

### Bookings

#### Create a booking

```
POST /api/bookings
Content-Type: application/json
```

**Body:**
```json
{
  "user_id": 42,
  "room_id": 1,
  "title": "Sprint Planning",
  "starts_at": "2025-06-10 10:00:00",
  "ends_at": "2025-06-10 11:00:00"
}
```

| Field | Type | Required | Description |
|-------|------|:--------:|-------------|
| `user_id` | integer | ✅ | User ID (no auth — just pass UID) |
| `room_id` | integer | ✅ | Room ID (must exist) |
| `title` | string | ✅ | Meeting title (max 255) |
| `starts_at` | datetime | ✅ | Start time (`Y-m-d H:i:s`), must be in the future |
| `ends_at` | datetime | ✅ | End time (`Y-m-d H:i:s`), must be after `starts_at` |

**Response 201 — created:**
```json
{
  "data": {
    "id": 7,
    "user_id": 42,
    "title": "Sprint Planning",
    "room": {
      "id": 1,
      "name": "Alpha",
      "location": "Floor 1, Room 101",
    },
    "starts_at": "2025-06-10 10:00:00",
    "ends_at": "2025-06-10 11:00:00",
    "duration_minutes": 60,
    "created_at": "2025-05-11 09:00:00"
  }
}
```

**Response 409 — time slot conflict:**
```json
{
  "message": "This room is already booked for the selected time slot."
}
```

**Response 422 — validation error:**
```json
{
  "message": "Validation error.",
  "errors": {
    "starts_at": ["Booking in the past is not allowed."]
  }
}
```

---

#### My bookings (by user)

```
GET /api/bookings?user_id={user_id}
```

**Response 200:**
```json
{
  "data": [
    {
      "id": 7,
      "user_id": 42,
      "title": "Sprint Planning",
      "room": {
        "id": 1,
        "name": "Alpha",
        "location": "Floor 1, Room 101",
      },
      "starts_at": "2025-06-10 10:00:00",
      "ends_at": "2025-06-10 11:00:00",
      "duration_minutes": 60,
      "created_at": "2025-05-11 09:00:00"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "per_page": 20, "total": 1 }
}
```

---

#### Bookings by room

```
GET /api/rooms/{room_id}/bookings
```

**Response 200** — same paginated structure as above.

---

## curl Examples

```bash
# List rooms
curl http://localhost:8080/api/rooms

# Create a booking
curl -X POST http://localhost:8080/api/bookings \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 42,
    "room_id": 1,
    "title": "Sprint Planning",
    "starts_at": "2025-06-10 10:00:00",
    "ends_at": "2025-06-10 11:00:00"
  }'

# My bookings
curl "http://localhost:8080/api/bookings?user_id=42"

# Bookings for a specific room
curl http://localhost:8080/api/rooms/1/bookings
```

---

## Make Commands

| Command | Description |
|---------|-------------|
| `make build` | Build and start |
| `make up` | Start without rebuild |
| `make down` | Stop |
| `make shell` | Enter app container |
| `make fresh` | Recreate DB + seeds |
| `make test` | Run tests |
| `make logs` | Container logs |
| `make routes` | List routes |

---

## Conflict Detection Logic

Two slots overlap if:

```
existing.starts_at < new.ends_at  AND  existing.ends_at > new.starts_at
```

This covers all cases: full overlap, partial overlap, and nested slots.

---

## Project Structure

```
meeting-room-booking/
├── docker/
│   ├── app/
│   │   ├── Dockerfile
│   │   └── entrypoint.sh
│   └── nginx/
│       └── default.conf
├── src/                          # Laravel application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── BookingController.php
│   │   │   │   └── RoomController.php
│   │   │   ├── Requests/
│   │   │   │   └── StoreBookingRequest.php
│   │   │   └── Resources/
│   │   │       ├── BookingResource.php
│   │   │       └── RoomResource.php
│   │   ├── Models/
│   │   │   ├── Booking.php
│   │   │   └── Room.php
│   │   └── Services/
│   │       └── BookingService.php
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
│       └── api.php
├── docker-compose.yml
├── Makefile
└── README.md
```