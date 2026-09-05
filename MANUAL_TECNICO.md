# Manual Técnico — Sistema de Gestión CENEFCO

> Documentación técnica de arquitectura, tecnologías, estructura de código e instalación/despliegue de los tres proyectos que componen el sistema. Dirigido a desarrolladores y al equipo de infraestructura. Complementa al `MANUAL_USUARIO.md` (funcional) — este documento es de implementación.

## Índice

1. [Visión General de la Arquitectura](#1-visión-general-de-la-arquitectura)
2. [`cenefco-api` — Backend (Laravel)](#2-cenefco-api--backend-laravel)
3. [`cenefco-admin` — Panel Administrativo (Angular)](#3-cenefco-admin--panel-administrativo-angular)
4. [`cenefco-portal` — Sitio Público (Angular)](#4-cenefco-portal--sitio-público-angular)
5. [Comunicación Entre los Tres Proyectos](#5-comunicación-entre-los-tres-proyectos)
6. [Instalación en Entorno Local](#6-instalación-en-entorno-local)
7. [Despliegue en Producción (VPS)](#7-despliegue-en-producción-vps)
8. [Seguridad](#8-seguridad)
9. [Inconsistencias Conocidas de Documentación](#9-inconsistencias-conocidas-de-documentación)
10. [Referencias](#10-referencias)

---

## 1. Visión General de la Arquitectura

El sistema está compuesto por **tres repositorios independientes** que se despliegan por separado pero se comunican en tiempo de ejecución:

```
                         ┌─────────────────────┐
                         │   cenefco-api        │  Laravel 12 + PostgreSQL
                         │   (Backend / API)    │  DDD + CQRS
                         └──────────┬───────────┘
                                    │  REST JSON (/api/v1/...)
                    ┌───────────────┼────────────────┐
                    │                                │
         ┌──────────▼──────────┐         ┌───────────▼─────────┐
         │   cenefco-admin      │         │   cenefco-portal     │
         │   Panel interno       │         │   Sitio público       │
         │   Angular 20          │         │   Angular 19.2.8      │
         │   Auth: Bearer token   │         │   Sin login (público)  │
         │   (Sanctum)             │         │   + "Mis Pagos" por CI   │
         └───────────────────────┘         └─────────────────────────┘
```

- Los tres proyectos viven en repositorios Git **separados** (`cenefco-api`, `cenefco-admin`, `cenefco-portal`), cada uno con su propio ciclo de build y despliegue.
- Comparten **una sola base de datos** (a través de `cenefco-api`, nunca acceso directo): ambos frontends consumen exclusivamente la API REST, nunca la base de datos.
- `cenefco-admin` (uso interno) se autentica con **Laravel Sanctum** (token Bearer). `cenefco-portal` (público) usa una capa adicional de **clave de portal + cifrado AES** para las rutas `/api/v1/portal/*` y `/api/v1/public/*` (ver sección 5).

---

## 2. `cenefco-api` — Backend (Laravel)

### 2.1 Stack tecnológico

| Componente | Versión / detalle |
|---|---|
| Lenguaje | PHP `^8.2` (en VPS real corre PHP 8.3, compatible con el `^8.2` de `composer.json`) |
| Framework | Laravel `^12.0` |
| Base de datos | PostgreSQL (migrado desde MySQL — `config/database.php` usa conexión `pgsql`; `.env.example` aún trae residuos de MySQL, ver sección 9) |
| Autenticación | Laravel Sanctum `^4.3` (SPA stateful para `cenefco-admin`) |
| Build de assets | Vite `^7` + Tailwind CSS `^4` (solo para vistas Blade propias, mínimas — la mayor parte de la UI vive en los dos Angular) |
| PDF | `barryvdh/laravel-dompdf` (certificados, cartas, reportes) |
| QR | `endroid/qr-code` + `simplesoftwareio/simple-qrcode` (certificados verificables) |
| Hojas de cálculo | `phpoffice/phpspreadsheet` (exportaciones) |
| Pagos | `stripe/stripe-php` `^19.4` |
| Settings persistentes | `spatie/laravel-settings` |
| IA / Bot | `prism-php/prism` (integración LLM para el bot de WhatsApp, con Ollama local como proveedor — ver `.env` `OLLAMA_*`) |
| Docs de API | `darkaonline/l5-swagger` |
| Testing | PHPUnit `^11.5`, Mockery, Faker |
| Estilo de código | Laravel Pint |

### 2.2 Arquitectura: DDD + CQRS estricto

Cada módulo de negocio se reparte en 4 capas, sin excepción (ver `CLAUDE.md` del repo para el detalle completo con ejemplos de código):

```
app/
├── Domain/{Modulo}/
│   ├── Contracts/      → Interfaces de repositorio (sin Eloquent, sin dependencias externas)
│   └── Exceptions/      → Excepciones de negocio (p. ej. NotFoundException, CursoConInscritosException)
├── Application/{Modulo}/
│   ├── Commands/         → Input de escritura (DTOs de comando, readonly)
│   ├── Handlers/          → Un handler por Command, orquesta el dominio
│   ├── Queries/            → Input de lectura
│   ├── QueryHandlers/       → Un handler por Query
│   └── DTOs/                 → Objetos de salida (nunca se expone un modelo Eloquent crudo)
├── Infrastructure/{Modulo}/
│   ├── Models/                 → Eloquent
│   └── Repositories/            → Implementación concreta de los Contracts del Domain
└── Http/
    ├── Controllers/Api/          → Solo orquestan Commands/Queries, cero SQL, cero lógica de negocio
    └── Requests/{Modulo}/         → FormRequests de validación
```

Reglas irrompibles (ver `CLAUDE.md` para ejemplos "❌ prohibido" / "✅ correcto"):
- Nunca Eloquent en `Domain/`.
- Nunca lógica de negocio en Controllers.
- Toda escritura múltiple envuelta en `DB::transaction()`.
- Toda respuesta HTTP sale como DTO, nunca como modelo Eloquent crudo.
- IDs nunca se generan a mano (`MAX(id)+1`) — siempre `insertGetId()`/`Model::create()`.

Los bindings de interfaz → implementación se registran centralizadamente en `app/Providers/DomainServiceProvider.php`.

### 2.3 Base de datos: dos generaciones de tablas conviviendo

- **Tablas legado** (`t_*`, `mdl_*`, ~145 migraciones): heredadas del sistema SIASEC anterior. PKs compuestas (`id_x, id_us_reg`), sin `timestamps()` (usan `fecha_reg`), sin soft deletes (usan `estado tinyint`), cada tabla tiene su espejo `_log` de auditoría.
- **Tablas nuevas** (`web_*`, `t_cert_*`, ~18 migraciones): convenciones modernas — `bigIncrements`, `timestampTz`, `estado_web` string semántico (`borrador`/`publicado`/`archivado`), FKs explícitas, slugs únicos.

Ambas conviven en la misma base `cenefco_api`. Ver `CLAUDE.md` para el mapa completo de relaciones entre tablas legado.

### 2.4 Autenticación y capas de seguridad HTTP

Middlewares registrados en `bootstrap/app.php`:

| Alias | Clase | Uso |
|---|---|---|
| `permiso` | `CheckPermiso` | Autorización granular `recurso.accion` sobre rutas del admin |
| `portal.key` | `ValidarPortalKey` | Exige header `X-Portal-Key` en rutas `/api/v1/portal/*` (consumidas por `cenefco-portal`) |
| `solo.activos` | `SoloActivosPortal` | Filtra registros inactivos en respuestas públicas |
| `rate.portal` | `RateLimitPortal` | Rate limiting específico para el portal público |
| `encrypt.portal` | `EncryptApiResponse` | Cifra el body de la respuesta (AES) en rutas del portal |

- **`cenefco-admin`** se autentica contra `cenefco-api` con **tokens personales de Sanctum (Bearer)**, no con cookies de sesión: `POST /api/auth/login` devuelve `{ token, user, expires_at }`; el token se guarda en `localStorage` y el interceptor del admin lo agrega como `Authorization: Bearer <token>` en cada request. *(Nota: `SANCTUM_STATEFUL_DOMAINS`/`SESSION_DOMAIN` en el `.env` de producción quedaron configurados pensando en el modo "SPA stateful" de Sanctum, pero el código real no lo usa — ver sección 9).*
  - **Expiración de tokens**: `config('sanctum.expiration')` (env `SANCTUM_TOKEN_EXPIRATION_MINUTES`, default `480` = 8h) hace que Sanctum rechace con `401` cualquier token más viejo que ese límite. `EloquentUserRepository::login()` fija explícitamente `expires_at` al crear el token y lo devuelve en la respuesta de login, para que el frontend pueda programar el cierre de sesión exacto (ver sección 3.4) en vez de depender solo de que el próximo request falle.
- **`cenefco-portal`** no tiene login tradicional: usa `PORTAL_API_KEY` (header `X-Portal-Key`) + `API_ENCRYPT_KEY` (AES-CBC) para las rutas públicas, y el estudiante se identifica en "Mis Pagos" solo con CI + email (sin contraseña).

### 2.5 Excepciones de dominio → HTTP

Cada excepción de dominio se registra explícitamente en `bootstrap/app.php` (`$exceptions->render(...)`) mapeándola a un código HTTP (`404` para NotFound, `422` para violaciones de regla de negocio como `CursoConInscritosException` o `PagoDuplicadoException`). Ver tabla completa de códigos en `CLAUDE.md`.

---

## 3. `cenefco-admin` — Panel Administrativo (Angular)

### 3.1 Stack tecnológico

| Componente | Versión / detalle |
|---|---|
| Framework | Angular `^20.1.0` (standalone components, builder `@angular/build:application`) |
| Lenguaje | TypeScript `~5.8.2` |
| Gestor de paquetes | **Bun** (el repo trae `bun.lock`, no `package-lock.json` — usar `bun install`, no `npm ci`) |
| CSS | Tailwind CSS v4 + **Preline** (kit de componentes UI sobre Tailwind) |
| Iconos | `@ng-icons/*` (lucide, tabler-icons), `@iconify/tailwind4` |
| Gráficas | ApexCharts / `ng-apexcharts` |
| Otros | `@fullcalendar/*` (calendario), `sweetalert2`, `flatpickr`, `@ng-select/ng-select`, `simplebar`, `swiper`, `@ckeditor/ckeditor5-*`, `xlsx`, `jspdf`, `qrcode` |
| Nombre interno del proyecto | `tailwick` en `angular.json` (build sale en `dist/tailwick/browser/`) |
| Puerto dev por defecto | `4200` |

### 3.2 Arquitectura de carpetas

Patrón **domain / application / presentation** repetido de forma consistente en ~117 módulos de negocio (`cursos`, `usuarios`, `roles`, `noticias`, `whatsapp`, etc.):

```
src/app/{modulo}/
├── domain/
│   └── models/            → Interfaces TypeScript puras (sin lógica)
├── application/
│   └── services/            → Servicios HttpClient, un método por endpoint
└── presentation/
    └── {vistas}/              → Componentes standalone (lista, create, edit, detail)
        └── {modulo}.ts, .html, .scss
```

Solo `constants/`, `layouts/`, `utils/` y `views/` (contenedor de rutas) quedan fuera de este patrón por ser código transversal/infraestructura.

### 3.3 Ruteo

- `app.routes.ts` → carga pública de `auth/presentation/modern-auth/modern-auth.route.ts` (login) + `MainLayout` protegido con `canActivate: [authGuard]` que carga `views/views.routes.ts`.
- `views.routes.ts` agrupa `dashboards.routes.ts`, `ecommerce.routes.ts` y `extra.routes.ts`. `ecommerce.routes.ts` a su vez agrupa los 7 grupos de módulos: `academico`, `catalogos`, `configuracion`, `contenido`, `institucional`, `usuarios`, `whatsapp` (cada uno en `views/ecommerce/routes/*.routes.ts`).
- Guard de autenticación: `auth/guards/auth.guard.ts` (`CanActivateFn`), redirige a `/auth-modern/login` si no hay sesión. Existe también `guest.guard.ts` (inverso, para rutas públicas de auth).

### 3.4 Autenticación y expiración de sesión

`auth/application/services/auth.service.ts`: `login()` → `POST /api/auth/login`, guarda `token`/`user`/`expires_at` en `localStorage` (`cenefco_token`, `cenefco_user`, `cenefco_token_expires_at`), expone el usuario actual como **signal** y `hasPermission(codigo)`. El interceptor `auth/infrastructure/interceptors/auth.interceptor.ts` inyecta `Authorization: Bearer <token>` en cada petición y hace `logout()` automático ante `401` (caso reactivo: el backend ya rechazó el token).

Además del caso reactivo, `AuthService` programa un `setTimeout` (`scheduleExpiry`) con el `expires_at` que devuelve el login — al cumplirse el tiempo de vida del token (backend, sección 2.4), la sesión se cierra **proactivamente** sin esperar a que el usuario haga alguna petición, y lo redirige a `/auth-modern/login?expired=1`. El componente de login lee ese query param y muestra "Tu sesión expiró. Vuelve a iniciar sesión." Al recargar la página, el constructor de `AuthService` vuelve a leer `expires_at` de `localStorage` y reprograma el timer con el tiempo restante (o cierra la sesión de inmediato si ya venció mientras la pestaña estaba cerrada).

### 3.5 Variables de entorno

**No existe** carpeta `src/environments/` (a diferencia del portal). Todas las llamadas usan **rutas relativas** (`/api/...`). En desarrollo, `proxy.conf.json` redirige `/api` y `/storage` → `localhost:8000` (el `cenefco-api` local) y `/whatsapp-bot` → `localhost:3001`. En producción, es el **Nginx** del propio `cenefco-admin` el que hace `proxy_pass` de `/api` y `/storage` hacia `cenefco-api` (ver sección 7).

---

## 4. `cenefco-portal` — Sitio Público (Angular)

### 4.1 Stack tecnológico

| Componente | Versión / detalle |
|---|---|
| Framework | Angular **19.2.8** (nombre interno del proyecto: `current-ng`) |
| Lenguaje | TypeScript `~5.7.2` |
| Gestor de paquetes | **npm** (repo trae ambos `package-lock.json` y `bun.lock` — el `package-lock.json` es el vigente por fecha de commit; usar `npm ci`) |
| CSS / UI | **Bootstrap 5.3.5** + `@ng-bootstrap/ng-bootstrap` (no usa Tailwind, a diferencia del admin) |
| Iconos | Font Awesome Free |
| Animación / carruseles | `aos`, `gsap`, `ngx-owl-carousel-o`, `ngx-slick-carousel` + `slick-carousel` + `jquery` (dependencia legacy global), `ngx-countup` |
| SSR | Angular SSR + Express (`server.ts`), aunque `prerender: false` |
| Puerto dev por defecto | **4201** (no 4200 — ese es el admin) |
| Output de build | `dist/current-ng/browser/` |

### 4.2 Arquitectura de carpetas

Mismo patrón DDD ligero que el admin, aplicado de forma algo menos uniforme:

```
src/app/{modulo}/
├── domain/models/           → Interfaces TS
└── application/services/     → Servicios HttpClient
```

- `src/app/views/` — componentes de página (`home-1`, `cenefco`, `cursos`, `mis-pagos`, `checkout`, `carrito`, `noticias`, `blogs`, etc.).
- `src/app/core/` — `constants`, `interceptors`, `directives`, `pipes`, `services`, `utils` transversales.
- `src/app/layouts/` — shell de navbar + footer.

### 4.3 Ruteo

`app.routes.ts` monta un único `LayoutComponent` con `loadChildren` lazy hacia `views/views.route.ts`, que a su vez sub-carga por lazy loading los módulos `noticias`, `comunicados`, `eventos`, `other-pages`, `blogs`. **No hay guards de ruta** — todo el portal es público, incluida `/mis-pagos` (se protege con lógica de negocio — pedir CI + email — no con `CanActivate`).

### 4.4 Autenticación, checkout y pagos

- **Sin login tradicional.** "Mis Pagos" identifica al estudiante con **CI + email** contra `GET /api/v1/public/mis-pagos`.
- El pago se inicia con `POST /api/public/pago/iniciar` (o `iniciarPagoMultiple`), que devuelve una `checkout_url` de Stripe Checkout **hosted** — el navegador redirige directo (`window.location.href`), sin SDK de Stripe en el frontend.
- `checkout.component.ts` hace polling cada 3s a `GET /api/public/pago/estado/:sessionId` hasta resolver, y redirige a `/pago-exitoso` o `/pago-fallido`.
- Interceptor `core/interceptors/decrypt-response.interceptor.ts`: agrega el header `X-Portal-Key` en rutas `/portal/` o `/public/`, y si la respuesta trae `X-Encrypted: 1`, descifra el body con AES-CBC (Web Crypto API) usando `environment.apiEncryptKey`.

### 4.5 Variables de entorno — generación especial

`src/environments/environment.ts` **no se edita a mano**: lo genera `scripts/set-env.js`.

- **En desarrollo** (`node scripts/set-env.js`, sin flags): lee `../cenefco-api/.env` — **ruta relativa fuera del propio repo**, asume que `cenefco-api` y `cenefco-portal` están clonados como hermanos en el mismo directorio padre — y escribe `apiBaseUrl`, `portalApiKey`, `apiEncryptKey` a `environment.ts`.
- **En producción** (`--prod`) escribe a `environment.production.ts`, pero **`angular.json` no tiene `fileReplacements` configurado** — el build de producción real sigue importando `environment.ts` a secas (ver hallazgo detallado en sección 7.4). Por eso en el VPS también se corre el modo **sin** `--prod`.
- `environment.example.ts` es la plantilla versionada en git; `environment.ts`/`environment.production.ts` están en `.gitignore`.
- **Gotcha:** si `../cenefco-api/.env` no existe al correr el script, el build no falla, pero las claves quedan vacías → sin cifrado ni autenticación de portal (401 o respuestas ilegibles en producción).

---

## 5. Comunicación Entre los Tres Proyectos

```
cenefco-admin (Bearer token, Sanctum) ──► /api/v1/{recurso}              [autenticado, con permiso]
cenefco-portal (X-Portal-Key + AES)   ──► /api/v1/portal/{recurso}       [portal.key + encrypt.portal]
cenefco-portal (sin auth)              ──► /api/v1/public/{recurso}       [rutas públicas simples]
```

- Ninguno de los dos frontends accede a la base de datos directamente — todo pasa por `cenefco-api`.
- En **desarrollo**, cada Angular corre en su propio puerto (`4200` admin, `4201` portal) y usa un `proxy.conf.json` para redirigir `/api` y `/storage` al backend local (`localhost:8000`).
- En **producción**, cada frontend es un build estático servido por su propio Nginx, que hace `proxy_pass` interno de `/api` y `/storage` hacia el Nginx de `cenefco-api` — evita problemas de CORS entre subdominios y permite que las rutas relativas (`/api/...`) funcionen igual en ambos entornos sin cambiar código.

---

## 6. Instalación en Entorno Local

### 6.1 `cenefco-api`

Requisitos: PHP `8.2+`, Composer, Node `20+`, PostgreSQL (o MySQL en entornos antiguos — ver sección 9).

```bash
cd cenefco-api
make setup          # composer install + .env + key:generate + migrate + npm install + build
# o paso a paso:
composer install
cp .env.example .env   # editar credenciales de BD antes de continuar
php artisan key:generate
php artisan migrate
npm install && npm run build

make dev             # servidor + queue worker + logs (pail) + vite, todo en paralelo
# equivalente a: php artisan serve + queue:listen + pail + npm run dev
```

Otros comandos útiles (`make help` lista todos): `make test`, `make test-filter f=NombreTest`, `make seed`, `make migrate-fresh`, `make fresh` (fresh + seed completo), `make tinker`, `make routes`.

> **No correr `make seed` / `db:seed` completo en un entorno que ya tiene datos reales** — el `DatabaseSeeder` mezcla catálogos necesarios con datos de demostración.

### 6.2 `cenefco-admin`

Requisitos: Node `20+`, **Bun**.

```bash
cd cenefco-admin
bun install
bun run start        # ng serve, puerto 4200
```

Necesita `cenefco-api` corriendo en `localhost:8000` (usa `proxy.conf.json` para `/api`, `/storage`, `/whatsapp-bot`).

### 6.3 `cenefco-portal`

Requisitos: Node `18.19+`/`20.11+`, npm.

```bash
cd cenefco-portal
npm install
node scripts/set-env.js     # genera environment.ts leyendo ../cenefco-api/.env — correr SIEMPRE antes del primer build/serve
npm start                   # ng serve --proxy-config proxy.conf.json --port 4201
```

> Requiere que `cenefco-api` esté clonado como carpeta **hermana** de `cenefco-portal` (mismo directorio padre) para que `scripts/set-env.js` encuentre `../cenefco-api/.env`.

---

## 7. Despliegue en Producción (VPS)

> Esta sección resume `DESPLIEGUE_VPS.md` (guía completa y verificada paso a paso, con todos los *gotchas* reales encontrados durante el despliegue). **Ese documento vive fuera de cualquier repositorio git** (`C:\Users\maxcell\projects\cenefco\DESPLIEGUE_VPS.md`) porque contiene credenciales reales de producción — este manual, al vivir dentro del repo `cenefco-api`, **nunca debe incluir esos valores reales**, solo placeholders.

### 7.1 Arquitectura del VPS

Un solo VPS Ubuntu 24.04 sirve los tres proyectos **sin Docker** (servicios nativos: PostgreSQL, PHP-FPM, Nginx, systemd), detrás de **Nginx Proxy Manager** (Docker) que ya gestiona otros servicios (Moodle, un proyecto de certificados):

```
Internet → :80/:443 → Nginx Proxy Manager (Docker, SSL Let's Encrypt)
                          ├── www.cenefco.com   → 127.0.0.1:8010 → cenefco-portal (estático)
                          ├── admin.cenefco.com → 127.0.0.1:8011 → cenefco-admin (estático)
                          ├── api.cenefco.com   → 127.0.0.1:8012 → cenefco-api (Nginx + PHP-FPM 9001)
                          ├── campus.cenefco.com       → Moodle (Docker, sin cambios)
                          └── certificados.cenefco.com → proyecto de certificados (sin cambios)
```

Los tres proyectos nuevos viven en `/srv/` (nunca en `/root/` — `www-data` no puede leer ahí). Cada uno corre en un puerto interno propio, nunca expuesto directo a internet (solo NPM les llega, vía la IP del bridge de Docker `172.17.0.1`, no `127.0.0.1`).

### 7.2 Resumen por proyecto

| Proyecto | Runtime en el VPS | Comando de build | Gestor de paquetes | Gotcha principal |
|---|---|---|---|---|
| `cenefco-api` | PHP-FPM 8.3 (pool dedicado, puerto `127.0.0.1:9001`) | `composer install --no-dev` + `npm run build` | Composer + npm | Ubuntu 24.04 solo trae `php8.3-*` en sus repos (no `php8.2-*`) — cumple igual el `^8.2` de Composer |
| `cenefco-admin` | Estático (solo Nginx) | `bun run build -- --configuration production` | **Bun** (`bun.lock`) | `npm ci` falla — no hay `package-lock.json` |
| `cenefco-portal` | Estático (solo Nginx) | `node scripts/set-env.js` + `npm run build` (nunca `ng build` directo) | npm (`package-lock.json` vigente) | SSR habilitado genera `index.csr.html`, no `index.html` — el script `build` de `package.json` ya copia el archivo correcto; invocar `ng build` a mano rompe el sitio (403/500) |

Ambos frontends Angular necesitan que su Nginx haga `proxy_pass` de `/api` y `/storage` hacia el puerto interno de `cenefco-api` (`127.0.0.1:8012`) — de lo contrario las rutas relativas del código (`/api/v1/...`) resolverían contra su propio dominio en vez de `api.cenefco.com`.

### 7.3 Variables de entorno de producción (`cenefco-api/.env`)

Plantilla sin valores reales — generar cada secreto en el momento del despliegue, nunca reutilizar los de desarrollo:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.cenefco.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cenefco_api
DB_USERNAME=cenefco_api
DB_PASSWORD=<generar con: openssl rand -base64 24>

SANCTUM_STATEFUL_DOMAINS=www.cenefco.com,admin.cenefco.com
SESSION_DOMAIN=.cenefco.com
SESSION_SECURE_COOKIE=true

API_ENCRYPT_KEY=<generar con: php -r "echo bin2hex(random_bytes(32));">
PORTAL_API_KEY=<generar con: php -r "echo bin2hex(random_bytes(16));">
```

`SANCTUM_STATEFUL_DOMAINS` + `SESSION_DOMAIN=.cenefco.com` (con el punto) + `SESSION_SECURE_COOKIE=true` son críticos porque `admin.cenefco.com` autentica contra `api.cenefco.com` por cookie de sesión entre subdominios distintos del mismo dominio raíz. `API_ENCRYPT_KEY`/`PORTAL_API_KEY` deben generarse **antes** de compilar `cenefco-portal`, porque su `scripts/set-env.js` las lee directamente de este `.env`.

### 7.4 Migraciones y seeders en producción

```bash
php artisan migrate --force
php artisan storage:link
```

**Nunca correr `php artisan db:seed` completo en producción** — el `DatabaseSeeder` mezcla seeders de catálogo necesarios con seeders de demo/prueba. Sembrar uno por uno, excluyendo cualquier seeder con "Demo" o "Prueba" en el nombre (`AdminSeeder`, `RolesPermisosSeeder`, `WebConfiguracionSitioSeeder`, `WebRedesSocialesSeeder`, `WebMenuSeeder`, y el resto de catálogos que el sitio necesite mostrar desde el día uno).

### 7.5 Mantenimiento continuo (actualizar una versión ya desplegada)

```bash
# cenefco-api
cd /srv/cenefco-api && git pull origin main && composer install --no-dev --optimize-autoloader \
  && npm ci && npm run build && php artisan migrate --force \
  && php artisan config:clear && php artisan cache:clear && systemctl restart php8.3-fpm

# cenefco-admin
cd /srv/cenefco-admin && git pull origin main && bun install \
  && bun run build -- --configuration production && systemctl reload nginx

# cenefco-portal — SIEMPRE regenerar environment.ts antes de cada build
cd /srv/cenefco-portal && git pull origin main && npm ci \
  && node scripts/set-env.js && npm run build && systemctl reload nginx
```

> El `Makefile` en la raíz del workspace (`C:\Users\maxcell\projects\cenefco\Makefile`) define objetivos `make deploy-api/deploy-admin/deploy-portal` — pero usa `npm ci` para el admin (en vez de `bun install`) y `ng build` directo para el portal (en vez de `npm run build`), lo que **contradice** los gotchas verificados en `DESPLIEGUE_VPS.md`. Preferir siempre los comandos de esta sección o del `.md` de despliegue sobre ese `Makefile` hasta que se corrija.

Guía completa, con cada paso de Nginx, PHP-FPM, Proxy Hosts de NPM, DNS y checklist de verificación final: **`DESPLIEGUE_VPS.md`** (fuera de este repo).

---

## 8. Seguridad

> Resumen de `SEGURIDAD_VPS.md` (fuera de este repo, contiene credenciales reales). Checklist de referencia, sin valores sensibles.

- **Puertos internos** (`8010`/`8011`/`8012` de los tres proyectos, `5432` PostgreSQL) deben quedar cerrados a internet vía `ufw` una vez confirmado que todo funciona por dominio — solo Nginx Proxy Manager (red interna de Docker) necesita alcanzarlos.
- **Panel de administración de Nginx Proxy Manager** (`:81`) no debe estar expuesto públicamente — acceder solo vía túnel SSH (`ssh -N -L 8181:127.0.0.1:81 root@<ip>`).
- **`fail2ban`** debe estar activo para SSH y accesos HTTP fallidos.
- **SSH**: preferir puerto no estándar, `PasswordAuthentication no` (solo clave), `PermitRootLogin prohibit-password`. Nunca cerrar el puerto activo sin haber confirmado el nuevo desde una segunda sesión.
- **`APP_DEBUG=false`** siempre en producción — con `true`, cualquier error de PHP expone stack traces completos.
- **Ningún `.env` real se sube a git** — ver hallazgo importante en la sección 9: esto **no se está cumpliendo hoy** en `.env.example`.
- **Backups automáticos de PostgreSQL** (`pg_dump` diario vía cron, retención ~14 días).
- **Rotación de credenciales**: cualquier clave que haya quedado expuesta en el historial de git (contraseña de PostgreSQL de desarrollo, tokens de WhatsApp/Zoom) debe rotarse y usarse solo en el `.env` del VPS, nunca reutilizarse.

---

## 9. Inconsistencias Conocidas de Documentación

Hallazgos a tener en cuenta al trabajar en el código — evitan perder tiempo asumiendo que la documentación interna de cada repo está al día:

- **⚠️ `cenefco-api/.env.example` contiene secretos reales committeados en git** (`WHATSAPP_ACCESS_TOKEN`, `ZOOM_CLIENT_ID`/`SECRET`, `DB_PASSWORD` de desarrollo, `WHATSAPP_VERIFY_TOKEN`). Un `.env.example` debe llevar solo placeholders — esto ya está señalado como pendiente de rotar en `SEGURIDAD_VPS.md`, pero además debería **reemplazarse el contenido de `.env.example` por placeholders** cuanto antes, ya que cualquiera con acceso de lectura al repo (incluido el historial) puede ver estas claves hoy.
- **`cenefco-api/.env.example` sigue configurado para MySQL** (`DB_CONNECTION=mysql`, puerto `3306`) aunque el código y la base de datos real ya usan PostgreSQL (`config/database.php`, confirmado contra datos reales). Actualizar la plantilla evita que alguien nuevo configure el motor equivocado.
- **`cenefco-portal/README.md` y `CLAUDE.md` afirman "Angular 20 standalone + zoneless"** — el `package.json` real fija Angular **19.2.8**. Confiar en `package.json`, no en la prosa del README.
- **`cenefco-portal/CLAUDE.md` conserva secciones heredadas del proyecto original** (portal municipal de Achocalla): menciona rutas/componentes (`/institucional`, `AchocallaComponent`, módulos `tramites`/`normativa`) que no existen en el `views.route.ts` actual. Usar ese documento con cautela, priorizando lo que se confirme leyendo el código.
- **`cenefco-portal/CLAUDE.md` menciona scripts `npm run set-env` / `set-env:prod`** que no existen en el `package.json` real (solo `ng`, `start`, `build`, `watch`, `test`) — el script se invoca directo: `node scripts/set-env.js`.
- **`cenefco-admin`: `angular.json` nombra el proyecto `"tailwick"`, `package.json` lo nombra `"tailwink"`** — inconsistencia cosmética sin impacto funcional, pero puede confundir al buscar el `outputPath` del build (`dist/tailwick/browser/`, con "c").
- **`cenefco-admin/README.md` menciona una carpeta `src/environments/`** que no existe en el proyecto real — el admin no usa ese mecanismo, todas las URLs son relativas (ver sección 3.5).
- **El `Makefile` del workspace raíz** (`deploy-admin`/`deploy-portal`) usa comandos distintos a los verificados en `DESPLIEGUE_VPS.md` (ver nota al final de la sección 7.5) — puede fallar si se usa tal cual contra un VPS real.
- **`DESPLIEGUE_VPS.md` describe la autenticación de `cenefco-admin` como "Sanctum SPA stateful (cookies de sesión)"**, y por eso configura `SANCTUM_STATEFUL_DOMAINS`/`SESSION_DOMAIN`/`SESSION_SECURE_COOKIE` en el `.env` de producción. El código real (`auth.service.ts`, `EloquentUserRepository::login()`) usa **tokens Bearer de Sanctum guardados en `localStorage`**, no cookies — esas variables de sesión no tienen efecto sobre este flujo de login. No es necesario quitarlas (no rompen nada), pero no resuelven CORS/CSRF del admin como el despliegue asume; si en el futuro se depuran problemas de sesión del admin, confirmar primero cuál mecanismo está realmente en juego.

---

## 10. Referencias

| Documento | Ubicación | Contiene |
|---|---|---|
| `CLAUDE.md` (cenefco-api) | raíz de `cenefco-api` | Arquitectura DDD+CQRS completa, convenciones, ejemplos de código, módulo de Pagos en detalle |
| `CLAUDE.md` (cenefco-admin) | raíz de `cenefco-admin` | Arquitectura del panel, convenciones por capa, cómo crear un módulo nuevo |
| `CLAUDE.md` (cenefco-portal) | raíz de `cenefco-portal` | Arquitectura del portal (con secciones desactualizadas, ver sección 9) |
| `DESPLIEGUE_VPS.md` | `C:\Users\maxcell\projects\cenefco\` (fuera de git) | Guía paso a paso completa de despliegue, con credenciales reales y gotchas encontrados en producción |
| `SEGURIDAD_VPS.md` | `C:\Users\maxcell\projects\cenefco\` (fuera de git) | Checklist de seguridad del VPS por fases, con estado real de cada riesgo |
| `MANUAL_USUARIO.md` | raíz de `cenefco-api` | Manual funcional para el personal que opera el sistema (no técnico) |

---

*Manual técnico en construcción — se irá ampliando con diagramas de secuencia de los flujos críticos (inscripción, pago, emisión de certificado) y con el detalle de los módulos que aún no se han auditado a fondo.*
