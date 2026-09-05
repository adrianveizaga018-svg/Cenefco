# CENEFCO Admin

Panel administrativo (Angular) del Sistema de Gestión CENEFCO — de uso interno para el personal del Centro de Formación Continua (administración, cajeros, coordinación académica, docentes). Gestiona cursos y programas, inscripciones, pagos, certificados con QR, docentes, contenido del sitio web institucional, WhatsApp y la configuración general del sistema.

Consume la API REST de [`cenefco-api`](../cenefco-api) (Laravel 12). No accede a la base de datos directamente. El sitio público de cara al estudiante es un proyecto aparte: [`cenefco-portal`](../cenefco-portal).

---

## Módulos principales

| Área | Incluye |
| --- | --- |
| **Académico** | Cursos/Programas, Áreas, Categorías de Programa, Planes Académicos, Convenios, Grupo Académico (Imparte), Calendario Académico, Cursos Migrados, Docentes, Sueldos Docentes, Documentos Académicos, Citas de Asesoría, Formularios de Inscripción |
| **Inscripciones y Pagos** | Inscripciones, Inscripciones a Diplomado, Pagos Académicos, Fechas de Pago, Reporte de Cobros, Ingresos, Ventas, Reporte Financiero, Correos Enviados |
| **Certificados** | Certificados, Plantillas de Certificado, Lista de Aprobados, Verificaciones, Certificados Post-Inscripción |
| **Catálogos** | Ciudades, Profesiones, Niveles, Tipos de Pago/Universidad/Postgrado, Configuración Académica, Universidades, Grados Académicos, Expedido |
| **Configuración del Sistema** | Configuraciones generales, Config. del Sitio, Moodle, Zoom, Cartas Modelo/Generadas |
| **Contenido del Sitio Web** | Banners, Eventos, Artículos, Noticias, Comunicados, Boletines, Popups, Galería, Descargables, Redes Sociales, FAQs, Testimonios, Suscriptores, Mensajes de Contacto, Analytics |
| **Institucional** | Autoridades, Secretarías, Organigramas, Historia y Cifras Institucionales, Aliados, Acreditaciones, Notas de Prensa, Normas, Transparencia, Tesis/Monografías/Revistas, Menús del Portal |
| **Usuarios y Seguridad** | Usuarios, Roles, Permisos, Notificaciones del Sistema, Vendedores, Mi Perfil |
| **WhatsApp** | Bot (estado, NLU, Intents), Asesores, Conversaciones, Plantillas, Grupos por curso, Speech de Ventas |
| **Finanzas / RRHH** | Gastos, Gastos Recurrentes, Regalía Sociedad de Ingenieros, Empleados, Planillas, Ajustes de Sueldo, Honorarios |

Ver el detalle de cada módulo y su flujo de uso en el `MANUAL_USUARIO.md` de `cenefco-api`.

---

## Arquitectura del frontend

Cada módulo de negocio sigue el mismo patrón de tres capas, consistente en ~117 módulos (`src/app/{modulo}/`):

```text
src/app/{modulo}/
├── domain/
│   └── models/        # Interfaces TypeScript puras — sin lógica, sin HttpClient
├── application/
│   └── services/       # Servicios @Injectable, un método por endpoint de la API
└── presentation/
    └── {vistas}/         # Componentes standalone (lista, create, edit, detail)
```

Solo `constants/`, `layouts/`, `utils/` y `views/` (contenedor de rutas) quedan fuera de este patrón por ser código transversal. Ver `CLAUDE.md` en la raíz de este repo para el ejemplo paso a paso de cómo crear un módulo nuevo.

### Ruteo

- `app.routes.ts` — carga pública de `auth/presentation/modern-auth/` (login) y protege el resto (`MainLayout`) con `canActivate: [authGuard]`.
- `views/views.routes.ts` agrupa `dashboards`, `ecommerce` y `extra`. `views/ecommerce/routes/*.routes.ts` reparte los módulos de negocio en 7 grupos: `academico`, `catalogos`, `configuracion`, `contenido`, `institucional`, `usuarios`, `whatsapp`.

### Autenticación

- `POST /api/auth/login` devuelve `{ token, user, expires_at }`. El token (Sanctum, Bearer) se guarda en `localStorage` (`cenefco_token`); `AuthService` expone al usuario actual como **signal** y `hasPermission(codigo)`.
- El interceptor (`auth/infrastructure/interceptors/auth.interceptor.ts`) agrega `Authorization: Bearer <token>` a cada request y cierra sesión ante un `401`.
- **La sesión expira sola** cuando se cumple `expires_at` (por defecto 8h desde el login, configurable en `cenefco-api`): `AuthService` programa el cierre automático y redirige a `/auth-modern/login?expired=1` sin esperar a que el usuario haga alguna petición.

### Variables de entorno

**No existe** carpeta `src/environments/` — todas las llamadas a la API usan **rutas relativas** (`/api/...`). En desarrollo, `proxy.conf.json` redirige `/api` y `/storage` hacia `cenefco-api` local (`localhost:8000`) y `/whatsapp-bot` hacia el bot (`localhost:3001`). En producción, es el propio Nginx del build estático el que hace `proxy_pass` de esas rutas hacia `cenefco-api` — no hace falta ni existe un archivo de configuración de entorno que editar.

---

## Stack tecnológico

| Tecnología | Versión | Uso |
| --- | --- | --- |
| **Angular** | `^20.1.0` | Framework (standalone components, builder `@angular/build:application`) |
| **TypeScript** | `~5.8.2` | Tipado estático |
| **Tailwind CSS** | `^4` | Utilidades de estilo, + **Preline** (kit de componentes UI) |
| **Bun** | — | Gestor de paquetes (el repo trae `bun.lock`, **no** `package-lock.json`) |
| **ApexCharts** | `ng-apexcharts` | Gráficas y reportes |
| **FullCalendar** | `@fullcalendar/*` | Calendario Académico |
| **SweetAlert2** | — | Alertas y confirmaciones |
| **CKEditor 5** | — | Editor de texto enriquecido (contenido web) |
| **@ng-select, flatpickr, simplebar, swiper** | — | Selects, datepicker, scroll, carruseles |
| **xlsx, jsPDF, qrcode** | — | Exportaciones y generación de QR/PDF en cliente |

---

## Inicio rápido

### Prerrequisitos

- Node.js `20+`
- **Bun** (`curl -fsSL https://bun.sh/install | bash`) — este proyecto usa Bun, no npm
- [`cenefco-api`](../cenefco-api) corriendo en `http://localhost:8000` (el panel no funciona sin la API)

### Instalación

```bash
cd cenefco-admin
bun install
```

### Servidor de desarrollo

```bash
bun run start   # ng serve, puerto 4200
```

Abre `http://localhost:4200/`. Las peticiones a `/api`, `/storage` y `/whatsapp-bot` se redirigen automáticamente a los servicios locales vía `proxy.conf.json`.

### Build de producción

```bash
bun run build -- --configuration production
```

El resultado queda en `dist/tailwick/browser/` (nombre interno del proyecto en `angular.json`; nota: `package.json` lo llama `"tailwink"` — es solo una inconsistencia de nombres, no afecta el build).

### Pruebas unitarias

```bash
bun run test
```

---

## Comandos útiles

| Script | Descripción |
| --- | --- |
| `bun run start` | Servidor de desarrollo (`ng serve`, puerto 4200) |
| `bun run build` | Build de producción |
| `bun run watch` | Build en modo observación (development) |
| `bun run test` | Pruebas unitarias (Karma) |

---

## Notas importantes

- El código de cada módulo vive en `src/app/{modulo}/`, siguiendo siempre el patrón `domain/application/presentation` — ver `CLAUDE.md` antes de crear uno nuevo.
- No hay `src/environments/` que ajustar: la URL de la API se resuelve por proxy (dev) o por Nginx (producción), siempre con rutas relativas `/api/...`.
- Para desplegar en el VPS de producción, ver `MANUAL_TECNICO.md` en `cenefco-api` (sección de despliegue) — este proyecto se compila con Bun y se sirve como sitio estático detrás de Nginx.
- Para crear un componente nuevo dentro de un módulo: `ng generate component nombre` (o `bunx ng generate ...`).

---

## Recursos

- [Angular CLI](https://angular.io/cli)
- [Documentación Angular](https://angular.io/)
- [Bun](https://bun.sh/)
- `CLAUDE.md` (este repo) — arquitectura completa y guía para crear módulos nuevos
- `MANUAL_USUARIO.md` / `MANUAL_TECNICO.md` (repo `cenefco-api`) — manuales funcional y técnico de todo el sistema
