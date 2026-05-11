.PHONY: up down build restart shell migrate seed fresh logs test

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose up -d --build

restart:
	docker compose restart

shell:
	docker compose exec app sh

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed

fresh:
	docker compose exec app php artisan migrate:fresh --seed

logs:
	docker compose logs -f

test:
	docker compose exec app php artisan test

routes:
	docker compose exec app php artisan route:list
