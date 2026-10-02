# cenefco API

API REST para la gestión de cursos, diplomados y programas de formación continua. Centraliza la administración de estudiantes, inscripciones, notas, pagos, docentes y la estructura académica de los programas.

---

## Módulos

| Módulo | Descripción |
| --- | --- |
| **Programas** | Gestión de programas académicos (diplomados, cursos, especializaciones) |
| **Planes** | Planes de estudio asociados a cada programa |
| **Materias** | Catálogo de materias con asignación a planes |
| **Docentes** | Registro de docentes y asignación a materias (impartición) |
| **Estudiantes** | Gestión de estudiantes con datos personales y académicos |
| **Inscripciones** | Inscripción de estudiantes a materias y programas |
| **Notas** | Registro y consulta de calificaciones por materia e inscripción |
| **Horarios** | Gestión de horarios de clases por materia y docente |
| **Pagos** | Registro de pagos, fechas de pago y tipos de pago |
| **Documentos** | Documentos requeridos y presentados por los estudiantes |
| **Usuarios y Roles** | Control de acceso con niveles, grupos de permisos y permisos granulares |
| **Contenido web** | Páginas, módulos, menús y bloques de plantilla del sistema |
| **Configuración** | Ajustes globales del sistema vía `spatie/laravel-settings` |
| **Web Institucional** | Banners, testimonios, FAQ, aliados, suscriptores, mensajes de contacto, docente-perfil, eventos, etiquetas, categorías de programas, popups y galerías de video |
| **Certificados** | Plantillas JPG, lista de aprobados, generación masiva con QR y verificación pública por código único |
| **Pre-inscripción** | Captación de leads por programa con rastreo UTM y notificación automática al abrir convocatoria |
| **WhatsApp** | Grupos de WhatsApp por apertura de curso con control de vigencia del enlace |

---

## Arquitectura

Implementa **DDD (Domain-Driven Design) + CQRS**:

```text
app/
├── Domain/          # Interfaces, excepciones — sin dependencias externas
├── Application/     # Commands, Queries, Handlers, DTOs
├── Infrastructure/  # Modelos Eloquent, Repositorios
└── Http/            # Controllers, Requests, Middleware
```

- **Commands / Handlers** — escrituras (crear noticia, registrar trámite)
- **Queries / QueryHandlers** — lecturas (listar, filtrar, paginar)
- **Repositorios** — abstracción sobre Eloquent, inyectados vía `DomainServiceProvider`
- **DTOs inmutables** — toda respuesta sale como `readonly class` con `fromModel()`

---

## Stack tecnológico

| Tecnología | Versión | Descripción |
| --- | --- | --- |
| **PHP** | `^8.2` | Lenguaje principal |
| **Laravel** | `^12.0` | Framework base |
| **MySQL** | `8+` | Base de datos relacional |
| **Laravel Sanctum** | `^4.3` | Autenticación por Bearer tokens |
| **Spatie Laravel Settings** | `^3.7` | Configuraciones persistentes en BD |
| **DomPDF** | `^3.1` | Generación de documentos PDF |
| **L5 Swagger** | `^10.1` | Documentación OpenAPI auto-generada |
| **Intervention Image** | `^1.5` | Procesamiento de imágenes |
| **Simple QrCode** | `^4.x` | Generación de códigos QR para certificados |

---

## Inicio rápido

### Prerrequisitos

- PHP `^8.2` + Composer
- MySQL `8+`
- Make

### Instalación

```bash
# 1. Clonar e instalar dependencias
git clone <repo>
cd cenefco_api
composer install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=localhost
# DB_PORT=3306
# DB_DATABASE=cenefco
# DB_USERNAME=<usuario>
# DB_PASSWORD=<contraseña>

# 4. Migrar y seedear
php artisan migrate
php artisan db:seed

# 5. Iniciar servidor
php artisan serve
```

O con un solo comando:

```bash
make setup   # composer install + .env + migrate + seed
make dev     # servidor + queue + logs en paralelo
```

### URLs de acceso

| Servicio | URL |
| --- | --- |
| **API REST** | `http://localhost:8000/api/v1` |
| **Swagger UI** | `http://localhost:8000/api/documentation` |
| **Autenticación** | `POST http://localhost:8000/api/login` |

---

## Desarrollo

### Comandos esenciales (Makefile)

```bash
make help          # Ver todos los comandos disponibles
```

#### Setup

```bash
make setup         # Instalación completa desde cero
make install       # Solo composer install + key:generate
make fresh         # migrate:fresh + seed completo (desarrollo)
```

#### Servidor y colas

```bash
make dev           # Servidor + queue + logs en paralelo
make serve         # Solo servidor PHP
make queue         # Worker de colas
make logs          # Visor de logs en tiempo real
make tinker        # Consola REPL interactiva
make routes        # Listar rutas registradas
```

#### Base de datos

```bash
make migrate       # Ejecutar migraciones pendientes
make fresh         # Eliminar y recrear toda la BD con seeders
make seed          # Ejecutar todos los seeders
make rollback      # Revertir último batch de migraciones
```

#### Caché y optimización

```bash
make cache-clear   # Limpiar config, rutas, vistas y caché
make cache-warm    # Regenerar config, rutas y vistas en caché
make optimize      # Optimizar para producción
```

#### Calidad de código

```bash
make test                  # Ejecutar suite de tests completa
make test-filter f=Nombre  # Ejecutar un test específico
make lint                  # Revisar estilo de código (pint --test)
make format                # Formatear código automáticamente (pint)
```

---

## Autenticación y permisos

### Endpoints de autenticación

```text
POST   /api/login
POST   /api/logout
GET    /api/me
```

Todas las rutas bajo `/api/v1/` requieren `Authorization: Bearer {token}`.

### Roles y permisos

Los permisos se verifican con el middleware `permiso:recurso.accion`:

```php
Route::get('/usuarios', [UserController::class, 'index'])
    ->middleware('permiso:usuarios.ver');
```

| Módulo | Permisos disponibles |
| --- | --- |
| usuarios | `usuarios.ver`, `usuarios.crear`, `usuarios.editar`, `usuarios.eliminar` |
| noticias | `noticias.ver`, `noticias.crear`, `noticias.editar`, `noticias.eliminar` |
| normas | `normas.ver`, `normas.crear`, `normas.editar` |
| trámites | `tramites.ver`, `tramites.crear`, `tramites.editar` |
| transparencia | `transparencia.ver`, `transparencia.crear` |
| reportes | `reportes.ver` |

---

## Base de datos cenefco (legado SIASEC)

El proyecto incluye **145 migraciones Laravel** generadas a partir del backup `disereco_siasec_backup.sql` del sistema legado SIASEC, más **~18 migraciones nuevas** para la capa web institucional y el módulo de certificados. Estas migraciones permiten recrear el esquema completo en cualquier entorno.

### Convención de nombres

```text
2026_04_14_NNNNNN_create_cenefco_{tabla}_table.php
```

Todas están en `database/migrations/` con el prefijo `cenefco_` en el nombre del archivo (no en el nombre de la tabla — la tabla conserva el nombre original, ej. `t_usuario`).

### Grupos de tablas

| Prefijo | Descripción |
| --- | --- |
| `t_` | Tablas de datos principales del sistema SIASEC |
| `t_reg` | Registros de componentes y formularios del sistema de permisos |
| `t_grupo*`, `t_usuariogrupopermiso` | Grupos de permisos y asignaciones |
| `t_nivel` | Niveles de acceso del sistema |
| `t_bitacora*`, `t_log*` | Auditoría y trazabilidad |
| `t_config*` | Configuraciones globales del sistema |
| `web_*` | Tablas nuevas para la web institucional (banners, testimonios, FAQ, etc.) |
| `t_cert_*` | Módulo de certificados (plantillas, campos, generación, verificación) |

### Convenciones del esquema legado

**Tablas legado (`t_*`, `mdl_*`):**
- **Sin `timestamps()`** — usan `fecha_reg` propio; no hay `created_at` / `updated_at`
- **Sin `softDeletes()`** — baja lógica mediante columna `estado` (`tinyint`, `1` = activo, `0` = inactivo)
- **`id_us_reg`** — columna de auditoría que registra el usuario que creó/modificó el registro
- **Claves primarias compuestas** — varias tablas usan `$table->primary([...])` con múltiples columnas
- **Sin `bigIncrements`** — los IDs son `integer` simples

**Tablas nuevas (`web_*`, `t_cert_*`):**
- Usan `bigIncrements('id')` como PK simple
- `timestampTz` para `created_at`, `updated_at` y `deleted_at`
- Estado como `string` semántico (`borrador` / `publicado` / `archivado`), no `tinyint`
- FK constraints declarados formalmente con `->foreign()`
- Slugs únicos en tablas de contenido público

### Ejecutar las migraciones cenefco

```bash
# Aplicar solo las migraciones cenefco (si están en un estado pendiente)
php artisan migrate --path=database/migrations

# Ver estado de todas las migraciones
php artisan migrate:status | grep cenefco

# Recrear BD completa desde cero
php artisan migrate:fresh
```

---

## Esquema de base de datos

### Esquema destacado

- **Slugs únicos** auto-generados en: noticias, comunicados, eventos, normas, trámites, secretarías, autoridades, documentos de transparencia
- **Soft deletes** en tablas críticas (`deleted_at`)
- **Búsqueda FULLTEXT** (MySQL) en noticias, comunicados, normas, trámites, eventos, secretarías, autoridades y documentos
- **Configuraciones globales** persistidas en tabla `settings` vía `spatie/laravel-settings`
- **Vista `v_busqueda_global`** — unifica noticias, comunicados, normas, trámites, eventos y documentos para búsqueda global

### Seeders iniciales

| Seeder | Datos que carga |
| --- | --- |
| `RolesPermisosSeeder` | Roles base y permisos del sistema |
| `TiposEventoSeeder` | Tipos de evento (cultural, cívico, deportivo, etc.) |
| `TiposNormaSeeder` | Tipos de norma (ley, decreto, resolución, etc.) |
| `TiposTramiteSeeder` | Tipos de trámite |
| `TiposAuditoriaSeeder` | Tipos de auditoría |
| `TiposDocumentoTransparenciaSeeder` | Categorías de documentos de transparencia |
| `CategoriaNoticiaSeeder` | Categorías de noticias |
| `CategoriaIndicadorSeeder` | Categorías de indicadores de gestión |
| `ConfiguracionSitioSeeder` | Configuración inicial del portal |
| `MenusSeeder` | Estructura de menús de navegación |
| `AdminSeeder` | Usuario administrador inicial |

### Comandos de migración

```bash
# Aplicar migraciones pendientes
make migrate

# Recrear BD desde cero con seeders (solo desarrollo)
make fresh

# Ver estado de migraciones
php artisan migrate:status
```

---

## Despliegue a producción

### Variables de entorno requeridas

```env
APP_NAME="cenefco API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.cenefco.gob.bo

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=cenefco
DB_USERNAME=<usuario>
DB_PASSWORD=<password-seguro>

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.proveedor.com
MAIL_FROM_ADDRESS=noreply@cenefco.gob.bo
MAIL_FROM_NAME="cenefco"
```

### Checklist de producción

```bash
# 1. Instalar dependencias sin dev
composer install --no-dev --optimize-autoloader

# 2. Generar key y configurar .env
php artisan key:generate

# 3. Aplicar migraciones
php artisan migrate --force

# 4. Crear tabla de sesiones (si SESSION_DRIVER=database)
php artisan session:table
php artisan migrate --force

# 5. Cargar datos iniciales
php artisan db:seed --force

# 6. Optimizar para producción
make optimize
```

---

## Estándares de calidad

- **Capas estrictas**: Domain no puede importar Infrastructure ni Http
- **DTOs inmutables**: toda respuesta sale como `readonly class`
- **Handlers**: toda operación de escritura múltiple dentro de `DB::transaction()`
- **Repositorios**: toda interacción con BD pasa por la interfaz del dominio
- **PSR-12**: formateado con Laravel Pint

```bash
make format    # Formatear código
make lint      # Verificar linting
make test      # Correr tests
```

---

## Solución de problemas

### `Table 'sessions' doesn't exist`

```bash
php artisan session:table
php artisan migrate
```

### `Table 'settings' doesn't exist`

```bash
php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="migrations"
php artisan migrate
```

### Error 500 en producción

```bash
tail -f storage/logs/laravel.log
```

### Las rutas no se actualizan

```bash
make cache-clear
make routes
```

---

## Documentación

| Documento | Descripción |
| --- | --- |
| [CLAUDE.md](CLAUDE.md) | Guía técnica para agentes AI y onboarding de desarrolladores |
