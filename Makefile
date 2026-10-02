.DEFAULT_GOAL := help
.PHONY: help setup install update dev serve serve-public watch queue queue-work queue-listen \
        schedule logs tinker routes models dump tunnel mailserver \
        migrate migrate-fresh migrate-rollback migrate-status migrate-reset migrate-refresh \
        seed seed-migrados make-seed fresh db-reset db-refresh \
        cache-clear cache-warm clear optimize \
        test test-filter test-coverage lint format format-test format-dirty \
        key-generate storage-link clean \
        env-dev env-prod version info \
        sail-up sail-down sail-build swagger \
        make-controller make-model make-migration make-seeder make-middleware make-request \
        restart reset-hard kill-serve

help:
	@echo ""
	@echo " cenefco — Comandos disponibles"
	@echo ""
	@echo " Setup"
	@echo "  make setup            Instalacion completa desde cero"
	@echo "  make install          composer install + key:generate"
	@echo "  make update           Actualizar dependencias PHP y Node"
	@echo "  make key-generate     Generar APP_KEY"
	@echo "  make storage-link     Crear enlace simbolico storage"
	@echo "  make env-dev          Copiar .env de desarrollo"
	@echo "  make env-prod         Copiar .env de produccion"
	@echo "  make clean            Eliminar vendor, node_modules y caches"
	@echo "  make reset-hard       Limpiar, instalar, migrar y seedear todo"
	@echo ""
	@echo " Desarrollo"
	@echo "  make dev              Servidor + queue + logs en paralelo"
	@echo "  make serve            Servidor PHP local (upload hasta 20MB)"
	@echo "  make serve-public     Servidor accesible en red local (0.0.0.0:8000, upload hasta 20MB)"
	@echo "  make watch            npm run watch"
	@echo "  make queue            Worker de colas (queue:listen)"
	@echo "  make queue-work       Worker de colas (queue:work)"
	@echo "  make queue-listen     Worker de colas (queue:listen)"
	@echo "  make schedule         Ejecuta el scheduler (jobs programados en routes/console.php)"
	@echo "  make logs             Visor de logs en tiempo real (pail)"
	@echo "  make tinker           Consola interactiva REPL"
	@echo "  make routes           Listar rutas registradas"
	@echo "  make models           Mostrar informacion de modelos"
	@echo "  make dump             Servidor de dump"
	@echo "  make tunnel           Exponer puerto 8000 via Cloudflare Tunnel"
	@echo "  make mailserver       Iniciar MailHog"
	@echo "  make version          Version de Laravel"
	@echo "  make info             Info del entorno (PHP, Node, Composer)"
	@echo ""
	@echo " Base de datos"
	@echo "  make migrate          Ejecutar migraciones pendientes"
	@echo "  make migrate-fresh    Eliminar y recrear toda la BD"
	@echo "  make migrate-rollback Revertir ultimo batch"
	@echo "  make migrate-status   Estado de migraciones"
	@echo "  make migrate-reset    Revertir todas las migraciones"
	@echo "  make migrate-refresh  Revertir y volver a migrar"
	@echo "  make seed             Ejecutar todos los seeders (DatabaseSeeder)"
	@echo "  make make-seed        Ejecutar todos los seeders (alias de seed)"
	@echo "  make seed-migrados    Migrar cursos desde cursos_info_cenefco.json (idempotente)"
	@echo "  make fresh            migrate:fresh + seed completo"
	@echo "  make db-reset         migrate:fresh + RoleSeeder (dev)"
	@echo "  make db-refresh       migrate:refresh + seed"
	@echo ""
	@echo " Cache y optimizacion"
	@echo "  make cache-clear      Limpiar config, rutas, vistas y cache"
	@echo "  make clear            Limpiar todo incluyendo optimize:clear"
	@echo "  make cache-warm       Cachear config, rutas y vistas"
	@echo "  make optimize         Optimizar para produccion"
	@echo "  make restart          clear + cache-warm"
	@echo ""
	@echo " Calidad de codigo"
	@echo "  make test             Ejecutar suite de tests"
	@echo "  make test-filter f=X  Ejecutar test especifico"
	@echo "  make test-coverage    Tests con reporte de cobertura"
	@echo "  make lint             Revisar formato (pint --test)"
	@echo "  make format           Formatear codigo (pint)"
	@echo "  make format-test      Verificar formato sin cambios"
	@echo "  make format-dirty     Formatear solo archivos modificados"
	@echo ""
	@echo " Generadores (make make-X NAME=Nombre)"
	@echo "  make make-controller  Crear controller"
	@echo "  make make-model       Crear model + migration"
	@echo "  make make-migration   Crear migration"
	@echo "  make make-seeder      Crear seeder"
	@echo "  make make-middleware  Crear middleware"
	@echo "  make make-request     Crear form request"
	@echo ""
	@echo " Docker / Sail"
	@echo "  make sail-up          Levantar contenedores Docker"
	@echo "  make sail-down        Detener contenedores Docker"
	@echo "  make sail-build       Reconstruir imagen Docker"
	@echo ""
	@echo " Documentacion"
	@echo "  make swagger          Regenerar documentacion OpenAPI"
	@echo ""

setup:
	composer install
	npm install
	@[ -f .env ] || cp .env.example .env
	php artisan key:generate
	php artisan storage:link
	php artisan migrate
	php artisan db:seed --class=RoleSeeder

install:
	composer install
	@[ -f .env ] || cp .env.example .env
	php artisan key:generate

update:
	composer update
	npm update

key-generate:
	php artisan key:generate

storage-link:
	php artisan storage:link

env-dev:
	cp -p ./deploy/.env.dev .env

env-prod:
	cp -r ./deploy/.env.production .env

clean:
	rm -rf node_modules
	rm -rf vendor
	rm -rf public/hot
	rm -rf public/storage
	rm -rf public/build
	rm -rf bootstrap/cache/*.php

reset-hard: clean install setup migrate seed

dev:
	npx concurrently -c "#93c5fd,#c4b5fd,#fb7185,#facc15" \
		"PHP_INI_SCAN_DIR=/etc/php/8.3/cli/conf.d:$$(pwd) php artisan serve" \
		"php artisan queue:listen --tries=1 --timeout=0" \
		"php artisan pail --timeout=0" \
		"php artisan schedule:work" \
		--names=server,queue,logs,schedule --kill-others

kill-serve:
	-kill $$(ps aux | grep "artisan serve\|php.*-S 127" | grep -v grep | awk '{print $$2}') 2>/dev/null; true

serve: kill-serve
	PHP_INI_SCAN_DIR=/etc/php/8.3/cli/conf.d:$$(pwd) php artisan serve

serve-public: kill-serve
	PHP_INI_SCAN_DIR=/etc/php/8.3/cli/conf.d:$$(pwd) php artisan serve --host=0.0.0.0 --port=8000

watch:
	npm run watch

queue:
	php artisan queue:listen --tries=1 --timeout=0

queue-work:
	php artisan queue:work

queue-listen:
	php artisan queue:listen

schedule:
	php artisan schedule:work

logs:
	php artisan pail --timeout=0

tinker:
	php artisan tinker

routes:
	php artisan route:list --columns=method,uri,name,action

models:
	php artisan model:show

dump:
	php artisan dump-server

tunnel:
	cloudflared tunnel --url http://localhost:8000

mailserver:
	./tools/bin/mailhog &

version:
	php artisan --version

info:
	@echo "Laravel Version:"
	@php artisan --version
	@echo "PHP Version: $$(php -v | head -n 1)"
	@echo "Composer Version: $$(composer --version | head -n 1)"
	@echo "Node Version: $$(node -v)"
	@echo "NPM Version: $$(npm -v)"

migrate:
	php artisan migrate

migrate-fresh:
	php artisan migrate:fresh

migrate-rollback:
	php artisan migrate:rollback

migrate-status:
	php artisan migrate:status

migrate-reset:
	php artisan migrate:reset

migrate-refresh:
	php artisan migrate:refresh

seed:
	php artisan db:seed

seed-migrados:
	php artisan db:seed --class=CursoMigradoSeeder

fresh:
	php artisan migrate:fresh --seed

db-reset:
	php artisan migrate:fresh --seed --seeder=RoleSeeder

db-refresh: migrate-refresh seed

cache-clear:
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	php artisan cache:clear

clear:
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	php artisan optimize:clear

cache-warm:
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache

optimize:
	composer install --optimize-autoloader --no-dev
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	php artisan event:cache

restart: clear cache-warm

test:
	php artisan config:clear --ansi
	php artisan test

test-filter:
	php artisan test --filter=$(f)

test-coverage:
	php artisan test --coverage

lint:
	./vendor/bin/pint --test

format:
	./vendor/bin/pint

format-test:
	./vendor/bin/pint --test

format-dirty:
	./vendor/bin/pint --dirty

make-controller:
	php artisan make:controller $(NAME)

make-model:
	php artisan make:model $(NAME) -m

make-migration:
	php artisan make:migration $(NAME)

make-seeder:
	php artisan make:seeder $(NAME)

make-middleware:
	php artisan make:middleware $(NAME)

make-request:
	php artisan make:request $(NAME)

sail-up:
	./vendor/bin/sail up -d

sail-down:
	./vendor/bin/sail down

sail-build:
	./vendor/bin/sail build --no-cache

docker-build:
	docker build -t cenefco-api:latest .

docker-run:
	docker run -d --name cenefco_api --env-file ../.env.docker -p 8000:8000 cenefco-api:latest

docker-stop:
	docker stop cenefco_api && docker rm cenefco_api

docker-logs:
	docker logs -f cenefco_api

docker-bash:
	docker exec -it cenefco_api bash

swagger:
	php artisan l5-swagger:generate