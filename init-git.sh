#!/bin/bash
# Run this script once to initialize the git repo with meaningful commits
# Usage: bash init-git.sh

set -e

cd "$(dirname "$0")"

git init
git config user.email "vachagan.nahapetyan.job@gmail.com"
git config user.name "Vachagan Nahapetyan"

# ── Commit 1: Docker infrastructure ──────────────────────────────────────────
git add docker-compose.yml docker/ Makefile .gitignore
git commit -m "chore: add Docker infrastructure (nginx, php-fpm, mysql)"

# ── Commit 2: Laravel base & config ──────────────────────────────────────────
git add src/composer.json src/composer.lock src/artisan src/public/ src/bootstrap/ \
        src/config/ src/routes/ src/.env.example src/phpunit.xml
git commit -m "chore: scaffold Laravel 12 project with base config and routes"

# ── Commit 3: Database layer (migrations + seeders) ──────────────────────────
git add src/database/
git commit -m "feat: add rooms and bookings migrations, seed initial rooms"

# ── Commit 4: Domain models ───────────────────────────────────────────────────
git add src/app/Models/
git commit -m "feat: add Room and Booking Eloquent models with overlapping scope"

# ── Commit 5: BookingService (business logic) ─────────────────────────────────
git add src/app/Services/ src/app/Providers/
git commit -m "feat: add BookingService with conflict detection logic"

# ── Commit 6: HTTP layer (requests, resources, controllers) ──────────────────
git add src/app/Http/
git commit -m "feat: add controllers, form requests, and JSON API resources"

# ── Commit 7: Feature tests ───────────────────────────────────────────────────
git add src/tests/
git commit -m "test: add feature tests for booking creation and conflict detection"

# ── Commit 8: Documentation ───────────────────────────────────────────────────
git add README.md init-git.sh
git commit -m "docs: add README with API reference and curl examples"
