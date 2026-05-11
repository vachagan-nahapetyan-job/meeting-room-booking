# Meeting Room Booking — Microservice

Микросервис бронирования переговорных комнат. JSON API на Laravel 12, MySQL, Docker.

## Стек

- **PHP** 8.3
- **Laravel** 12
- **MySQL** 8.0
- **Nginx** 1.25
- **Docker** / Docker Compose

---

## Быстрый старт

​```bash
git clone https://github.com/vachagan-nahapetyan-job/meeting-room-booking.git
cd meeting-room-booking
docker compose up -d --build
​```

> Это единственная команда. Ничего больше не нужно.

Первый запуск занимает **~1-2 минуты** — composer устанавливает зависимости внутри контейнера.

Автоматически при старте:
1. `composer install` — установка зависимостей
2. `php artisan key:generate` — генерация APP_KEY
3. `php artisan migrate` — создание таблиц
4. `php artisan db:seed` — 4 тестовые переговорки (Alpha, Beta, Gamma, Delta)

Проверка:
​```bash
curl http://localhost:8080/api/rooms
​```
---

## API Reference

### Base URL

```
http://localhost:8080/api
```

---

### Переговорки (Rooms)

#### Список всех комнат

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
      "capacity": 6
    },
    {
      "id": 2,
      "name": "Beta",
      "location": "Floor 1, Room 102",
      "capacity": 10
    }
  ]
}
```

---

#### Детали комнаты

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
    "capacity": 6
  }
}
```

---

### Бронирования (Bookings)

#### Создать бронирование

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

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|---------|
| `user_id` | integer | ✅ | ID пользователя (без авторизации) |
| `room_id` | integer | ✅ | ID переговорки |
| `title` | string | ✅ | Название встречи (max 255) |
| `starts_at` | datetime | ✅ | Начало (`Y-m-d H:i:s`), должно быть в будущем |
| `ends_at` | datetime | ✅ | Конец (`Y-m-d H:i:s`), должно быть позже `starts_at` |

**Response 201 — успешно создано:**
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
      "capacity": 6
    },
    "starts_at": "2025-06-10 10:00:00",
    "ends_at": "2025-06-10 11:00:00",
    "duration_minutes": 60,
    "created_at": "2025-05-11 09:00:00"
  }
}
```

**Response 409 — конфликт времени:**
```json
{
  "message": "Переговорка уже занята на выбранное время."
}
```

**Response 422 — ошибка валидации:**
```json
{
  "message": "Ошибка валидации.",
  "errors": {
    "starts_at": ["Нельзя бронировать в прошлом."]
  }
}
```

---

#### Мои бронирования (по пользователю)

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
        "capacity": 6
      },
      "starts_at": "2025-06-10 10:00:00",
      "ends_at": "2025-06-10 11:00:00",
      "duration_minutes": 60,
      "created_at": "2025-05-11 09:00:00"
    }
  ],
  "links": {
    "first": "http://localhost:8080/api/bookings?user_id=42&page=1",
    "last": "http://localhost:8080/api/bookings?user_id=42&page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 1
  }
}
```

---

#### Бронирования по комнате

```
GET /api/rooms/{room_id}/bookings
```

**Response 200** — аналогичная пагинированная структура.

---

## Примеры curl

```bash
# Список комнат
curl http://localhost:8080/api/rooms

# Создать бронирование
curl -X POST http://localhost:8080/api/bookings \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 42,
    "room_id": 1,
    "title": "Sprint Planning",
    "starts_at": "2025-06-10 10:00:00",
    "ends_at": "2025-06-10 11:00:00"
  }'

# Мои бронирования
curl "http://localhost:8080/api/bookings?user_id=42"

# Бронирования конкретной комнаты
curl http://localhost:8080/api/rooms/1/bookings
```

---

## Make-команды

| Команда | Описание |
|---------|---------|
| `make build` | Сборка и запуск |
| `make up` | Запуск без пересборки |
| `make down` | Остановка |
| `make shell` | Войти в контейнер app |
| `make fresh` | Пересоздать БД + сиды |
| `make test` | Запустить тесты |
| `make logs` | Логи контейнеров |
| `make routes` | Список маршрутов |

---

## Логика проверки конфликтов

Два слота пересекаются, если:

```
existing.starts_at < new.ends_at  AND  existing.ends_at > new.starts_at
```

Это покрывает все сценарии: полное перекрытие, частичное, вложенное.

---

## Структура проекта

```
meeting-room-booking/
├── docker/
│   ├── app/
│   │   ├── Dockerfile
│   │   └── entrypoint.sh
│   └── nginx/
│       └── default.conf
├── src/                          # Laravel приложение
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
