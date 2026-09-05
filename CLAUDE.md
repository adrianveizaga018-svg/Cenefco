# CLAUDE.md — Guía para Agentes AI y Onboarding Técnico: admin_ventas

> Referencia principal para Claude Code y cualquier desarrollador nuevo en el frontend.
> Describe la arquitectura, patrones, convenciones y ejemplos reales del código.

---

## Qué es este proyecto

**admin_ventas** es el panel de administración del sistema comercial **cenefco**.
Construido con Angular 18+ usando **standalone components**, **Signals**, y **Zoneless Change Detection**.

Stack: Angular 20 · TypeScript · TailwindCSS · ApexCharts · Angular Signals · Zoneless (`provideZonelessChangeDetection`)

API base: `/api/v1/` — servida por `api_ventas` (Laravel 12 DDD)

---

## Estructura de carpetas

```
src/app/
│
├── auth/                          ← Autenticación
│   ├── application/services/
│   │   └── auth.service.ts        ← Login, logout, currentUser (signal)
│   ├── guards/
│   │   ├── auth.guard.ts          ← Protege rutas autenticadas
│   │   └── guest.guard.ts         ← Protege rutas públicas
│   ├── infrastructure/interceptors/
│   │   └── auth.interceptor.ts    ← Añade Bearer token a cada request
│   └── presentation/modern-auth/ ← Páginas de login, register, reset-password
│
├── common/                        ← Código compartido entre todos los features
│   ├── domain/models/
│   │   └── settings.model.ts      ← Tipos de configuración del negocio
│   ├── application/services/
│   │   ├── toast.service.ts       ← Notificaciones (signal-based)
│   │   ├── title.service.ts       ← Título del navegador dinámico
│   │   ├── layout-store.service.ts ← Estado del sidebar/layout
│   │   ├── settings.service.ts    ← Configuraciones generales del negocio
│   │   ├── notificacion.service.ts← Notificaciones del sistema
│   │   └── departamento.service.ts← Departamentos/ciudades de Bolivia
│   └── components/               ← Componentes UI reutilizables
│       ├── apexchart/             ← Wrapper de ApexCharts
│       ├── page-title/            ← Encabezado de página con breadcrumb
│       ├── pagination/            ← Componente de paginación
│       ├── route-loader/          ← Barra de progreso entre rutas
│       ├── searchable-select/     ← Select con búsqueda
│       ├── toast-container/       ← Contenedor de toasts
│       └── file-uploader/         ← Subida de archivos
│
├── dashboard/                     ← Dashboard principal
│   ├── domain/models/
│   │   └── dashboard.model.ts     ← DashboardStats, TopProducto, VentaMes, etc.
│   ├── application/services/
│   │   └── dashboard.service.ts   ← Consultas de métricas al API
│   └── presentation/ecommerce/
│       ├── ecommerce.ts           ← Página principal del dashboard
│       └── components/            ← overview, sales-chart, order-chart, etc.
│
├── ventas/                        ← Módulo de ventas
│   ├── domain/models/venta.model.ts
│   ├── application/services/venta.service.ts
│   └── presentation/
│       ├── ventas/                ← Listado de ventas
│       ├── venta-create/          ← Crear nueva venta
│       └── venta-detail/          ← Detalle de venta
│
├── productos/                     ← Módulo de productos
├── clientes/                      ← Módulo de clientes
├── proveedores/                   ← Módulo de proveedores
├── compras/                       ← Módulo de compras
├── caja/                          ← Módulo de caja
├── inventario/                    ← Inventario y ajustes
├── categorias/                    ← Categorías de productos
├── tipos-pago/                    ← Tipos de pago
├── roles/                         ← Roles y permisos
├── usuarios/                      ← Usuarios del sistema
├── reportes/                      ← Reportes y exportaciones
├── whatsapp/                      ← Bot de WhatsApp (conversaciones y plantillas)
├── configuraciones/               ← Configuraciones del negocio
├── cuenta/                        ← Perfil del usuario logueado
│   └── presentation/mi-perfil/
│
├── layouts/                       ← Layout principal (sin cambios)
│   ├── main-layout/               ← Layout raíz con sidebar + topbar
│   └── components/
│       ├── sidenav/               ← Sidebar con menú de navegación
│       ├── topbar/                ← Barra superior con usuario y notificaciones
│       ├── footer/
│       └── customizer/            ← Panel de personalización del tema
│
├── views/                         ← Archivos de rutas (orquestadores)
│   ├── views.routes.ts            ← Raíz de rutas autenticadas
│   ├── dashboards/dashboards.routes.ts
│   ├── ecommerce/ecommerce.routes.ts ← Rutas de todos los features de negocio
│   └── extra/                     ← Páginas auxiliares (404, maintenance, etc.)
│
├── utils/                         ← Utilidades puras (sin Angular)
│   ├── calculate-time.ts
│   ├── file-utils.ts
│   ├── input-restrict.directive.ts
│   └── zod-validators.ts
│
├── constants/index.ts             ← Constantes globales
├── app.routes.ts                  ← Rutas raíz de la aplicación
└── app.config.ts                  ← Configuración Angular (providers, interceptors)
```

---

## Arquitectura de cada feature

Cada módulo de negocio sigue el mismo patrón de 3 capas:

```
<feature>/
├── domain/
│   └── models/
│       └── <feature>.model.ts     ← Interfaces/tipos puros (sin Angular)
├── application/
│   └── services/
│       └── <feature>.service.ts   ← Lógica de negocio, llamadas HTTP
└── presentation/
    └── <vista>/
        ├── <vista>.ts             ← Componente Angular (standalone)
        └── <vista>.html           ← Template
```

### Reglas de la arquitectura

```typescript
// ✅ La capa domain solo tiene interfaces/tipos TypeScript puros
// domain/models/venta.model.ts
export interface Venta {
  id: number;
  numero: string;
  total: number;
  // ...
}

// ✅ La capa application tiene el servicio que llama al API
// application/services/venta.service.ts
@Injectable({ providedIn: 'root' })
export class VentaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/ventas';

  getAll(params = {}): Observable<VentaListResponse> {
    return this.http.get<VentaListResponse>(this.baseUrl, { params });
  }
}

// ✅ La capa presentation consume el servicio con inject()
// presentation/ventas/ventas.ts
@Component({ selector: 'app-ventas', standalone: true, ... })
export class Ventas {
  private ventaService = inject(VentaService);
  ventas = signal<Venta[]>([]);
}
```

### Lo que NO se debe hacer

```typescript
// ❌ No hacer llamadas HTTP directamente en componentes
export class Ventas {
  constructor(private http: HttpClient) {
    this.http.get('/api/v1/ventas').subscribe(...); // ← PROHIBIDO
  }
}

// ❌ No importar desde rutas de otro feature en la capa de presentación
import { VentaCreate } from '../../ventas/presentation/venta-create/venta-create'; // ← PROHIBIDO

// ❌ No usar constructor injection (usar inject() en su lugar)
constructor(private service: VentaService) {} // ← evitar, usar inject()
```

---

## Configuración Angular

### app.config.ts

```typescript
export const appConfig: ApplicationConfig = {
  providers: [
    provideBrowserGlobalErrorListeners(),
    provideZonelessChangeDetection(),    // ← Zoneless: sin Zone.js
    provideRouter(routes),
    provideHttpClient(
      withFetch(),
      withInterceptors([authInterceptor]) // ← JWT automático en cada request
    ),
  ]
};
```

### Implicación del Zoneless

Con `provideZonelessChangeDetection()`, Angular **no detecta cambios automáticamente** en suscripciones RxJS. Regla:

```typescript
// ✅ Usar Signals para estado reactivo
ventas = signal<Venta[]>([]);

// ✅ Si usas subscribe(), marcar cambios manualmente
private cdr = inject(ChangeDetectorRef);

this.service.getAll().subscribe(data => {
  this.ventas.set(data.data);
  this.cdr.detectChanges(); // ← obligatorio con zoneless + subscribe
});

// ✅ Preferir toSignal() para evitar subscribe() manual
ventas = toSignal(this.service.getAll(), { initialValue: [] });
```

---

## Rutas

### Estructura de rutas

```
app.routes.ts
├── /pages/maintenance, /pages/404, /pages/coming-soon, /pages/offline
├── /auth-modern/login, /auth-modern/register, ...   (sin guard)
└── MainLayout (canActivate: authGuard)
    └── views.routes.ts
        ├── dashboards.routes.ts  → /dashboards/e-commerce
        ├── ecommerce.routes.ts   → todos los features de negocio
        └── extra.routes.ts       → /pages/starter, /pages/faqs, etc.
```

### URLs disponibles

| Módulo | Rutas |
| --- | --- |
| Dashboard | `/dashboards/e-commerce` |
| Ventas | `/e-commerce/listado-ventas`, `/e-commerce/venta-create`, `/e-commerce/venta-detail/:id` |
| Productos | `/e-commerce/listado-productos`, `/e-commerce/producto-create`, `/e-commerce/producto-edit/:slug` |
| Clientes | `/e-commerce/listado-clientes`, `/e-commerce/client-create`, `/e-commerce/client-edit/:id` |
| Proveedores | `/e-commerce/proveedores`, `/e-commerce/proveedor-create`, `/e-commerce/proveedor-edit/:slug` |
| Compras | `/e-commerce/compras`, `/e-commerce/compra-create` |
| Caja | `/e-commerce/caja` |
| Inventario | `/e-commerce/inventario`, `/e-commerce/inventario-ajuste` |
| Categorías | `/e-commerce/categorias` |
| Tipos de Pago | `/e-commerce/tipos-pago` |
| Roles | `/e-commerce/roles`, `/e-commerce/rol-create`, `/e-commerce/rol-edit/:id` |
| Usuarios | `/e-commerce/usuarios`, `/e-commerce/usuario-create`, `/e-commerce/usuario-edit/:id` |
| WhatsApp | `/e-commerce/whatsapp-conversaciones`, `/e-commerce/whatsapp-plantillas` |
| Configuraciones | `/e-commerce/configuraciones` |
| Reportes | `/e-commerce/reportes` |
| Perfil | `/e-commerce/mi-perfil` |

---

## Autenticación

### Flujo

1. Usuario ingresa a cualquier ruta → `authGuard` verifica `AuthService.isLoggedIn()`
2. Si no está autenticado → redirige a `/auth-modern/login`
3. Login exitoso → guarda `token` y `user` en `localStorage` via `AuthService`
4. `authInterceptor` intercepta cada request HTTP y añade `Authorization: Bearer <token>`
5. Si el API devuelve `401` → el interceptor llama `auth.logout()` y redirige al login

### Usar AuthService en componentes

```typescript
export class MiComponente {
  auth = inject(AuthService);

  // Acceder al usuario logueado (Signal)
  usuario = this.auth.currentUser(); // AuthUser | null

  // Verificar si está logueado
  logueado = this.auth.isLoggedIn(); // boolean

  // Logout
  cerrarSesion() {
    this.auth.logout();
  }
}
```

---

## Componentes comunes

### PageTitle

```html
<app-page-title title="Listado de Ventas" subtitle="Ventas" />
```

### Pagination

```html
<app-pagination
  [currentPage]="currentPage()"
  [pageSize]="pageSize"
  [total]="total()"
  (pageChange)="onPageChange($event)"
/>
```

### ToastService

```typescript
private toast = inject(ToastService);

// En cualquier acción
this.toast.success('Venta creada', 'La venta fue registrada correctamente.');
this.toast.error('Error', 'No se pudo guardar.');
this.toast.warning('Atención', 'Stock bajo.');
```

### SearchableSelect

```html
<app-searchable-select
  [options]="clientes()"
  [labelKey]="'nombre'"
  [valueKey]="'id'"
  placeholder="Buscar cliente..."
  (selected)="onClienteSelected($event)"
/>
```

### ApexChart

```typescript
// En el componente:
chartOptions: (() => ApexOptions) | null = null;

ngOnInit() {
  this.chartOptions = () => ({
    chart: { type: 'area', height: 300 },
    series: [{ name: 'Ventas', data: [10, 25, 30, 18] }],
    // ...
  });
}
```

```html
@if (chartOptions) {
  <app-apexchart [getOptions]="chartOptions" />
}
```

---

## Cómo crear un nuevo feature

Ejemplo: módulo `descuentos`

### 1. Crear la estructura de carpetas

```bash
mkdir -p src/app/descuentos/domain/models
mkdir -p src/app/descuentos/application/services
mkdir -p src/app/descuentos/presentation/descuentos
```

### 2. Crear el modelo (`domain/models/descuento.model.ts`)

```typescript
export interface Descuento {
  id: number;
  nombre: string;
  porcentaje: number;
  activo: boolean;
}

export interface DescuentoListResponse {
  data: Descuento[];
  total: number;
}
```

### 3. Crear el servicio (`application/services/descuento.service.ts`)

```typescript
import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Descuento, DescuentoListResponse } from '../../domain/models/descuento.model';

@Injectable({ providedIn: 'root' })
export class DescuentoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/descuentos';

  getAll(): Observable<DescuentoListResponse> {
    return this.http.get<DescuentoListResponse>(this.baseUrl);
  }

  create(data: Partial<Descuento>): Observable<Descuento> {
    return this.http.post<Descuento>(this.baseUrl, data);
  }

  update(id: number, data: Partial<Descuento>): Observable<Descuento> {
    return this.http.put<Descuento>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
```

### 4. Crear el componente (`presentation/descuentos/descuentos.ts`)

```typescript
import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { DescuentoService } from '../../application/services/descuento.service';
import { Descuento } from '../../domain/models/descuento.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-descuentos',
  standalone: true,
  imports: [NgIcon, DecimalPipe, RouterLink, PageTitle],
  templateUrl: './descuentos.html',
})
export class Descuentos implements OnInit {
  private service  = inject(DescuentoService);
  private toast    = inject(ToastService);
  private cdr      = inject(ChangeDetectorRef);

  descuentos = signal<Descuento[]>([]);
  loading    = signal(true);

  ngOnInit() {
    this.service.getAll().subscribe({
      next: (res) => {
        this.descuentos.set(res.data);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron cargar los descuentos.');
        this.loading.set(false);
        this.cdr.detectChanges();
      }
    });
  }
}
```

### 5. Registrar la ruta en `views/ecommerce/ecommerce.routes.ts`

```typescript
import { Descuentos } from "../../descuentos/presentation/descuentos/descuentos";

// Agregar a ECOMMERCE_ROUTES:
{ path: 'e-commerce/descuentos', component: Descuentos, data: { title: 'Descuentos' } },
```

### 6. Agregar al menú del sidebar

Editar `layouts/components/sidenav/components/sidebar-menu/sidebar-menu.ts` y agregar la entrada al arreglo del menú.

---

## Convenciones de código

### Componentes (Standalone)

```typescript
@Component({
  selector: 'app-nombre',
  standalone: true,           // ← siempre standalone
  imports: [NgIcon, RouterLink, DecimalPipe, PageTitle, ...],
  templateUrl: './nombre.html',
})
export class NombreComponente {
  // inject() en lugar de constructor
  private service = inject(NombreService);

  // Estado con signals
  items   = signal<Item[]>([]);
  loading = signal(false);
  error   = signal<string | null>(null);
}
```

### Templates Angular

```html
<!-- Condicionales -->
@if (loading()) {
  <div>Cargando...</div>
} @else if (items().length === 0) {
  <p>Sin resultados</p>
}

<!-- Bucles -->
@for (item of items(); track item.id) {
  <div>{{ item.nombre }}</div>
}

<!-- NO usar arrow functions en templates -->
<!-- ❌ [class]="items.filter(i => i.activo)" -->
<!-- ✅ usar método del componente: [class]="getActivos()" -->
```

### Nombrado de archivos

| Elemento | Convención | Ejemplo |
| --- | --- | --- |
| Componente | `kebab-case.ts` | `venta-create.ts` |
| Servicio | `<feature>.service.ts` | `venta.service.ts` |
| Modelo | `<feature>.model.ts` | `venta.model.ts` |
| Guard | `<nombre>.guard.ts` | `auth.guard.ts` |
| Interceptor | `<nombre>.interceptor.ts` | `auth.interceptor.ts` |
| Rutas | `<feature>.routes.ts` | `ecommerce.routes.ts` |

---

## Reglas de importación

```typescript
// ✅ Un feature importando su propio dominio
import { VentaModel } from '../domain/models/venta.model';

// ✅ Un feature importando su propio servicio
import { VentaService } from '../application/services/venta.service';

// ✅ Un feature importando common
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';

// ✅ Un feature importando auth
import { AuthService } from '../../auth/application/services/auth.service';

// ✅ Un feature usando tipos de otro feature (cuando hay dependencia real)
import { ProductoModel } from '../../productos/domain/models/producto.model';

// ❌ Importar componentes de presentación de otro feature
import { VentaCreate } from '../../ventas/presentation/venta-create/venta-create'; // PROHIBIDO

// ❌ No importar desde rutas core/ (ya no existe)
import { VentaService } from '../../core/services/venta.service'; // PROHIBIDO
```

---

## Comandos frecuentes

```bash
# Servidor de desarrollo
npm start
# o
npx ng serve

# Build de desarrollo
npx ng build --configuration development

# Build de producción
npx ng build --configuration production

# Verificar errores de compilación sin build completo
npx ng build --configuration development 2>&1 | grep -E "ERROR|error TS"
```

> **Nota sobre Node.js:** Este proyecto requiere Node.js v20+ o v22+.
> Si tienes nvm: `nvm use --lts` antes de cualquier comando ng.

---

## Estado del proyecto

- Rama principal: `main`
- Rama de arquitectura refactorizada: `refactor/feature-based-architecture`
- Build de producción: ✅ sin errores
- Módulos implementados: ventas, productos, clientes, proveedores, compras, caja, inventario, categorías, tipos-pago, roles, usuarios, reportes, whatsapp, configuraciones, cuenta/perfil, dashboard

---

## Módulo de Pagos — Reglas críticas del frontend

> Este módulo maneja dinero real. Las reglas siguientes no son opcionales.

### Archivos del módulo

```text
src/app/
├── pagos-academicos/
│   ├── domain/models/pago-academico.model.ts
│   ├── application/services/pago-academico.service.ts
│   └── presentation/
│       ├── pago-create/pago-create.ts
│       ├── pago-edit/pago-edit.ts
│       └── pagos-academicos/pagos-academicos.ts
├── fechas-pago/
│   ├── domain/models/fecha-pago.model.ts
│   ├── application/services/fecha-pago.service.ts
│   └── presentation/
│       ├── fecha-pago-create/fecha-pago-create.ts
│       └── fecha-pago-edit/fecha-pago-edit.ts
└── inscripciones/
    ├── application/services/inscripcion.service.ts   ← registrarAnticipo, devoluciones
    └── presentation/inscripcion-detail/              ← resumen de pagos por inscripción
```

### Tipos de datos — reglas irrompibles

```typescript
// ❌ NUNCA: enviar id_pago generado por el frontend
const payload = {
  id_pago: Math.floor(Date.now() / 1000),  // ← PROHIBIDO
  monto_pagado: String(monto),              // ← PROHIBIDO
};

// ✅ SIEMPRE: el ID lo genera MySQL (AUTO_INCREMENT), el monto viaja como number
const payload: CreatePagoAcademicoPayload = {
  id_us:         this.form.value.id_us,
  id_fechapago:  this.form.value.id_fechapago,
  monto_pagado:  this.form.value.monto_pagado,  // number, nunca string
  fecha_deposito: this.form.value.fecha_deposito,
};

// ❌ NUNCA: enviar id_fechapago generado por el frontend
const payload = {
  id_fechapago:  Math.floor(Date.now() / 1000),  // ← PROHIBIDO
  monto_a_pagar: String(monto),                   // ← PROHIBIDO
};

// ✅ SIEMPRE: solo los campos que pide el backend
const payload: CreateFechaPagoPayload = {
  id_plan:       this.form.value.id_plan,
  monto_a_pagar: this.form.value.monto_a_pagar,  // number
  nro_pago:      this.form.value.nro_pago,
};
```

### Interfaces actualizadas

`CreatePagoAcademicoPayload` — **sin** `id_pago`, `monto_pagado` es `number`:

```typescript
export interface CreatePagoAcademicoPayload {
  id_us:                number;
  id_fechapago?:        number | null;
  monto_pagado:         number;          // ← number, nunca string
  nro_boleta_bancaria?: string | null;
  fecha_deposito?:      string | null;
  nro_nit?:             string | null;
  nombre_nit?:          string | null;
  observacion_pago?:    string | null;
  estado?:              number;
}
```

`CreateFechaPagoPayload` — **sin** `id_fechapago`, `monto_a_pagar` es `number`:

```typescript
export interface CreateFechaPagoPayload {
  id_plan:        number;
  nro_pago?:      string | null;
  tipo_tramite?:  string | null;
  monto_a_pagar:  number;                // ← number, nunca string
  fecha_inicio?:  string | null;
  fecha_fin?:     string | null;
  obligatorio?:   number;
  estado?:        number;
}
```

### Formularios de pago — configuración correcta

```typescript
// pago-create.ts — form correcto
form = this.fb.group({
  // ← sin id_pago
  id_us:              [null as number | null, [Validators.required]],
  id_fechapago:       [null as number | null],
  monto_pagado:       [null as number | null, [Validators.required, Validators.min(0.01)]],
  nro_boleta_bancaria:[''],
  fecha_deposito:     [new Date().toISOString().split('T')[0]],
  observacion_pago:   [''],
});

// fecha-pago-create.ts — form correcto
form = this.fb.group({
  // ← sin id_fechapago
  id_plan:       [null as number | null, [Validators.required]],
  nro_pago:      [''],
  monto_a_pagar: [null as number | null, [Validators.required, Validators.min(0.01)]],
  fecha_inicio:  [''],
  fecha_fin:     [''],
  obligatorio:   [1],
});
```

### Llamadas HTTP del módulo

| Acción             | Método | URL                                     | Payload clave                                    |
|--------------------|--------|-----------------------------------------|--------------------------------------------------|
| Crear pago         | POST   | `/api/v1/pagos-academicos`              | `monto_pagado: number` — sin `id_pago`           |
| Editar pago        | PUT    | `/api/v1/pagos-academicos/{id}`         | `monto_pagado: number` — sin `id_pago`           |
| Crear cuota        | POST   | `/api/v1/fechas-pago`                   | `monto_a_pagar: number` — sin `id_fechapago`     |
| Registrar anticipo | POST   | `/api/v1/inscripciones/{id}/anticipo`   | `monto_pagado: number`                           |
| Resolver devolución| PATCH  | `/api/v1/devoluciones/{id}/resolver`    | `estado`, `nota_respuesta: null\|string`         |
| Cancelar devolución| PATCH  | `/api/v1/devoluciones/{id}/cancelar`    | `estado: 'cancelada'`, `nota_respuesta: null`    |

### Resumen de pagos por inscripción (`ResumenPagoDTO`)

El endpoint `GET /api/v1/inscripciones/{id}` devuelve un objeto `resumen` con este shape:

```typescript
interface ResumenPago {
  plan_no_asignado: boolean;
  total_pagado:     number;
  total_anticipos:  number;
  cuotas_pagadas:   number;
  cuotas_totales:   number;
  total_plan:       number;
  pendiente:        number | null;  // null = sin plan asignado (NO equivale a pagado)
}
```

> `pendiente === null` significa que la inscripción no tiene plan asignado, **no que esté pagada**. El frontend debe mostrar una advertencia en ese caso, nunca mostrar "Bs. 0 pendiente".

---

Proyecto cenefco — Sistema Comercial para negocios bolivianos
