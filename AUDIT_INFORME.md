# Informe de Auditoría de Código — cenefco-admin

> **Fecha:** 2026-06-07
> **Alcance:** 620 archivos TypeScript · 100+ módulos de feature
> **Stack:** Angular 20 · Signals · Zoneless Change Detection · TailwindCSS

---

## Resumen ejecutivo

El proyecto tiene una **estructura de carpetas DDD correcta** (Domain → Application → Presentation)
y convenciones de código consistentes. Sin embargo, presenta **violaciones sistemáticas** de las
reglas de arquitectura en la capa de presentación: llamadas HTTP directas en componentes,
lógica de negocio duplicada, y 89+ componentes con el mismo boilerplate de manejo de estado.

| Categoría | Veredicto | Severidad |
| --- | --- | --- |
| Estructura de carpetas (DDD) | ✅ Correcta | — |
| TypeScript strict / tipos | ✅ Activado | — |
| Naming conventions | ✅ Consistente | — |
| HttpClient en componentes | ❌ 37 archivos | **Crítica** |
| Lógica de negocio en UI | ❌ 8+ archivos | **Alta** |
| Duplicación de código | ❌ Masiva | **Alta** |
| Organización de rutas | ❌ 761 líneas en 1 archivo | **Media** |
| Manejo de suscripciones | ⚠️ Sin cleanup | **Media** |
| Type safety en formularios | ⚠️ `as any` frecuente | **Media** |

---

## Hallazgo 1 — HttpClient inyectado en componentes de presentación (CRÍTICO)

**Archivos afectados: 37** (confirmado por grep en `**/presentation/**/*.ts`)

La capa de presentación **no debe llamar directamente a la API**. Esa responsabilidad pertenece
exclusivamente a la capa `application/services/`. Sin embargo, 37 componentes inyectan
`HttpClient` y realizan llamadas HTTP por su cuenta.

### Ejemplos concretos

**`cursos/presentation/curso-create/curso-create.ts` líneas 30, 83-84, 111, 140**
```typescript
private http = inject(HttpClient);  // ← línea 30: NO debe estar aquí

// línea 83-84: llamada directa a /api/v1/areas — debe ir en AreasService
this.http.get<{ data: ... }>('/api/v1/areas', { params: { pageSize: '100' } })
  .subscribe({ next: r => this.areas.set(r.data), error: () => {} });

// línea 111: subida de imagen — debe ir en FileUploadService
this.http.post<{ url: string }>('/api/v1/upload/image', formData).subscribe({...});

// línea 140: subida de PDF — debe ir en FileUploadService
this.http.post<{ url: string }>('/api/v1/upload/file', formData).subscribe({...});
```

**Lista completa de archivos afectados:**

```
cursos/presentation/curso-create/curso-create.ts
cursos/presentation/curso-edit/curso-edit.ts
areas/presentation/area-create/area-create.ts
areas/presentation/area-edit/area-edit.ts
pagos-academicos/presentation/pago-create/pago-create.ts
vendedores/presentation/vendedor-create/vendedor-create.ts
vendedores/presentation/vendedor-edit/vendedor-edit.ts
calendario-academico/presentation/calendario-create/calendario-create.ts
calendario-academico/presentation/calendario-edit/calendario-edit.ts
articulos/presentation/articulo-create/articulo-create.ts
articulos/presentation/articulo-edit/articulo-edit.ts
testimonios/presentation/testimonio-create/testimonio-create.ts
testimonios/presentation/testimonio-edit/testimonio-edit.ts
preinscripciones/presentation/preinscripcion-detail/preinscripcion-detail.ts
preinscripciones/presentation/preinscripcion-create/preinscripcion-create.ts
docentes-perfil/presentation/docente-perfil-create/docente-perfil-create.ts
docentes-perfil/presentation/docente-perfil-edit/docente-perfil-edit.ts
tesis/presentation/tesis-create/tesis-create.ts
tesis/presentation/tesis-edit/tesis-edit.ts
revistas/presentation/revista-create/revista-create.ts
revistas/presentation/revista-edit/revista-edit.ts
revistas-cientificas/presentation/revista-cientifica-create/revista-cientifica-create.ts
revistas-cientificas/presentation/revista-cientifica-edit/revista-cientifica-edit.ts
resenas/presentation/resena-create/resena-create.ts
resenas/presentation/resena-detail/resena-detail.ts
popups/presentation/popup-create/popup-create.ts
popups/presentation/popup-edit/popup-edit.ts
monografias/presentation/monografia-create/monografia-create.ts
monografias/presentation/monografia-edit/monografia-edit.ts
inscripciones/presentation/inscripciones/inscripciones.ts
inscripciones/presentation/inscripcion-create/inscripcion-create.ts
cuenta/presentation/mi-perfil/mi-perfil.ts
config-sitio/presentation/config-sitio/config-sitio.ts
cartas-generadas/presentation/carta-generada-create/carta-generada-create.ts
cartas-generadas/presentation/carta-generada-detail/carta-generada-detail.ts
banners/presentation/banner-create/banner-create.ts
banners/presentation/banner-edit/banner-edit.ts
```

### Solución requerida

Crear un `FileUploadService` en `common/application/services/`:

```typescript
// common/application/services/file-upload.service.ts
@Injectable({ providedIn: 'root' })
export class FileUploadService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/upload';

  uploadImage(file: File): Observable<{ url: string }> {
    const formData = new FormData();
    formData.append('file', file);
    return this.http.post<{ url: string }>(`${this.baseUrl}/image`, formData);
  }

  uploadFile(file: File): Observable<{ url: string }> {
    const formData = new FormData();
    formData.append('file', file);
    return this.http.post<{ url: string }>(`${this.baseUrl}/file`, formData);
  }
}
```

Eliminar `private http = inject(HttpClient)` de los 37 componentes y reemplazar por
`private fileUpload = inject(FileUploadService)`.

---

## Hallazgo 2 — Lógica de slug duplicada en componentes de presentación (ALTO)

**Archivos afectados: 8** (confirmado por grep `normalize('NFD')`)

La generación de slugs es **lógica de dominio** (reglas de negocio sobre cómo se forma una
URL a partir de un nombre). Está duplicada en 8 componentes de presentación, copiada
literalmente.

### Código duplicado (idéntico en los 8 archivos)

```typescript
// APARECE IGUAL en: cursos, areas, articulos, categorias-programa, etc.
this.form.get('nombre_programa')!.valueChanges.subscribe((nombre: string) => {
  const slug = (nombre ?? '')
    .toLowerCase()
    .normalize('NFD').replace(/[̀-ͯ]/g, '')    // diacríticos
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
  this.form.get('slug')!.setValue(slug, { emitEvent: false });
});
```

**Archivos afectados:**
- `cursos/presentation/curso-create/curso-create.ts`
- `cursos/presentation/curso-edit/curso-edit.ts`
- `areas/presentation/area-create/area-create.ts`
- `articulos/presentation/articulo-create/articulo-create.ts`
- `articulos/presentation/articulo-edit/articulo-edit.ts`
- `categorias-programa/presentation/categoria-programa-create/categoria-programa-create.ts`
- `categorias-programa/presentation/categoria-programa-edit/categoria-programa-edit.ts`
- `whatsapp/presentation/intent-form/intent-form.ts`

### Solución requerida

```typescript
// utils/slug.ts  (agregar a utils/)
export function generateSlug(text: string): string {
  return (text ?? '')
    .toLowerCase()
    .normalize('NFD').replace(/[̀-ͯ]/g, '')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
}
```

Luego en cada componente, reemplazar el bloque duplicado por:
```typescript
import { generateSlug } from '../../../utils/slug';

this.form.get('campo')!.valueChanges.subscribe(v => {
  this.form.get('slug')!.setValue(generateSlug(v ?? ''), { emitEvent: false });
});
```

**Nota:** La regex `[̀-ͯ]` que se usa actualmente está mal codificada (rango de caracteres
Unicode incorrecto). La corrección es `[̀-ͯ]`.

---

## Hallazgo 3 — Patrón de lista duplicado en 24 componentes (ALTO)

**Archivos afectados: 24** (confirmado por grep `toSignal(toObservable`)

Cada componente de listado reimplementa el mismo patrón de 25-30 líneas para manejo de
estado paginado con búsqueda. El código es **copia exacta** salvo el nombre del servicio.

### Código duplicado (patrón de `usuarios.ts` — idéntico en 24 archivos)

```typescript
// ESTE BLOQUE EXISTE 24 VECES, solo cambia el nombre del servicio
searchQuery = signal('');
pageIndex   = signal(1);
pageSize    = signal(10);
private refreshTrigger = signal(0);  // también existe en 89 archivos

private params = computed(() => ({
  query:     this.searchQuery(),
  pageIndex: this.pageIndex(),
  pageSize:  this.pageSize(),
  refresh:   this.refreshTrigger(),
}));

private state = toSignal(
  toObservable(this.params).pipe(
    switchMap(p =>
      this.XxxService.getAll(p).pipe(
        map(response => ({ type: 'success', response } as ApiState)),
        startWith(LOADING),
        catchError(() => of(ERROR)),
      )
    ),
    startWith(LOADING),
  ),
  { requireSync: true }
);

get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
get isLoading() { return this.state().type === 'loading'; }
```

**24 archivos con este patrón exacto:**
`fechas-pago`, `convenios`, `whatsapp-grupos`, `suscriptores`, `redirecciones`,
`programas-academicos`, `popups`, `planes-academicos`, `permisos`, `pagos-academicos`,
`notas-prensa`, `galeria-videos`, `galeria-categorias`, `fotos`, `fechas-doc`,
`docentes-perfil`, `descargables`, `cartas`, `cartas-modelo`, `cartas-generadas`,
`boletines`, `ayudas`, `articulos`, `acreditaciones`

### Solución requerida

Extraer a una función de composición reutilizable:

```typescript
// utils/list-state.ts
export interface ListResponse<T> { data: T[]; total: number; }

export type ListState<T> =
  | { type: 'loading' }
  | { type: 'success'; data: T[]; total: number }
  | { type: 'error' };

export function createListState<T>(
  fetchFn: (params: Record<string, unknown>) => Observable<ListResponse<T>>,
  options: { pageSize?: number } = {}
) {
  const searchQuery     = signal('');
  const pageIndex       = signal(1);
  const pageSize        = signal(options.pageSize ?? 10);
  const refreshTrigger  = signal(0);

  const params = computed(() => ({
    query: searchQuery(), pageIndex: pageIndex(),
    pageSize: pageSize(), refresh: refreshTrigger(),
  }));

  const state = toSignal(
    toObservable(params).pipe(
      switchMap(p => fetchFn(p).pipe(
        map(r => ({ type: 'success', data: r.data, total: r.total } as ListState<T>)),
        startWith({ type: 'loading' } as ListState<T>),
        catchError(() => of({ type: 'error' } as ListState<T>)),
      )),
      startWith({ type: 'loading' } as ListState<T>),
    ),
    { requireSync: true }
  );

  return {
    state,
    searchQuery, pageIndex, pageSize,
    refresh: () => refreshTrigger.update(n => n + 1),
    get items()     { const s = state(); return s.type === 'success' ? s.data  : []; },
    get total()     { const s = state(); return s.type === 'success' ? s.total : 0; },
    get isLoading() { return state().type === 'loading'; },
    get hasError()  { return state().type === 'error'; },
  };
}
```

Uso en componente:
```typescript
export class Usuarios {
  private list = createListState<Usuario>(p => inject(UsuarioService).getAll(p));

  get usuarios()  { return this.list.items; }
  get total()     { return this.list.total; }
  get isLoading() { return this.list.isLoading; }

  onSearch(e: Event) { this.list.searchQuery.set((e.target as HTMLInputElement).value); }
  onPageChange(p: number) { this.list.pageIndex.set(p); }
  afterDelete() { this.list.refresh(); }
}
```

---

## Hallazgo 4 — refreshTrigger signal en 89 componentes (ALTO)

**Archivos afectados: 89** (confirmado por grep `private refresh.*= signal(0)`)

Todos los componentes de listado tienen un `refreshTrigger = signal(0)` que se incrementa
para forzar refetch. Es redundante si se usa `createListState` del hallazgo anterior, pero
incluso sin esa abstracción, el patrón actual acumula 89 instancias del mismo boilerplate.

Este hallazgo se resuelve automáticamente al implementar `createListState` (Hallazgo 3).

---

## Hallazgo 5 — Lógica de upload duplicada en 19 componentes (ALTO)

**Archivos afectados: 19** (confirmado por grep `/api/v1/upload/image` en `presentation/`)

El bloque completo de `onImgSelected()` — incluyendo FileReader para preview, FormData,
llamada HTTP y manejo de errores — está copiado 19 veces:

```typescript
// ESTE BLOQUE SE REPITE 19 VECES (solo cambia el campo de form: foto, imagen_url, logo, etc.)
onImgSelected(event: Event): void {
  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => this.imgPreview.set(e.target?.result as string);
  reader.readAsDataURL(file);

  this.uploadingImg.set(true);
  const formData = new FormData();
  formData.append('file', file);

  this.http.post<{ url: string }>('/api/v1/upload/image', formData).subscribe({
    next: (res) => {
      this.form.patchValue({ foto: res.url });
      this.uploadingImg.set(false);
    },
    error: (err) => {
      this.toast.error('Error', extractErrorMessage(err, 'No se pudo subir la imagen'));
      this.imgPreview.set(null);
      this.uploadingImg.set(false);
      input.value = '';
    }
  });
}
```

**Archivos afectados:** `cursos`, `areas`, `vendedores`, `articulos`, `testimonios`,
`docentes-perfil`, `resenas`, `popups`, `monografias`, `banners`, `config-sitio` (create + edit
en la mayoría).

### Solución requerida

1. Crear `FileUploadService` (ver Hallazgo 1).
2. Centralizar la lógica de preview+upload en el componente `file-uploader` que ya existe
   en `common/components/file-uploader/` — extenderlo para que emita `(uploaded)` con la URL
   resultante.
3. En los formularios: usar `<app-file-uploader (uploaded)="form.patchValue({foto: $event})" />`

---

## Hallazgo 6 — Archivo de rutas monolítico (MEDIO)

**Archivo:** `src/app/views/ecommerce/ecommerce.routes.ts` — **761 líneas**

Un solo archivo define 300+ rutas e importa 200+ componentes en su cabecera.
Esto viola el principio de responsabilidad única y hace imposible el code splitting por dominio.

### Estructura actual (problema)
```typescript
// ecommerce.routes.ts — 761 líneas con 200+ imports al inicio
import { Ciudades }     from "../../ciudades/...";
import { CiudadCreate } from "../../ciudades/...";
import { CiudadEdit }   from "../../ciudades/...";
// ... 200+ imports más
```

### Solución requerida

Dividir en archivos de rutas por dominio:

```
views/
├── catalogos.routes.ts       → ciudades, profesiones, niveles, tipos-pago, etc.
├── academico.routes.ts       → cursos, programas, inscripciones, pagos, certificados
├── contenido.routes.ts       → noticias, eventos, articulos, boletines, etc.
├── institucional.routes.ts   → secretarias, autoridades, documentos, etc.
├── usuarios.routes.ts        → usuarios, roles, permisos, grupos
├── whatsapp.routes.ts        → conversaciones, plantillas, cuentas, etc.
└── configuracion.routes.ts   → configuraciones, config-sitio, zoom, moodle
```

Cada archivo tiene ~50-80 líneas y sus propios imports. El archivo principal `ecommerce.routes.ts`
los importa con `loadChildren` o los re-exporta directamente.

---

## Hallazgo 7 — `as any` en envíos de formularios (MEDIO)

**Archivos afectados: 50+** (estimado por patrón recurrente en create/edit)

Los datos del formulario se envían al servicio sin tipado:

```typescript
// area-create.ts
this.service.create({ ...this.form.value as any, galeria: this.galeriaUrls() });

// convenio-create.ts
this.service.create(this.form.value as any);

// curso-create.ts
this.cursoService.create(this.form.value).subscribe({...}); // FormGroup.value es any implícito
```

### Consecuencias
- TypeScript no detecta si se envía un campo renombrado, eliminado o con tipo incorrecto.
- Los errores solo aparecen en runtime (respuesta 422 del servidor).

### Solución requerida

Definir el tipo de payload de creación en el modelo de dominio y usarlo al construir el objeto:

```typescript
// domain/models/area.model.ts
export interface CreateAreaPayload {
  titulo:       string;
  descripcion?: string;
  imagen_url?:  string;
  orden:        number;
  activo:       boolean;
}

// En el componente
const payload: CreateAreaPayload = {
  titulo:       this.form.value.titulo!,
  descripcion:  this.form.value.descripcion ?? undefined,
  imagen_url:   this.form.value.imagen_url  ?? undefined,
  orden:        this.form.value.orden ?? 0,
  activo:       this.form.value.activo ?? true,
};
this.service.create(payload).subscribe({...});
```

---

## Hallazgo 8 — Suscripciones en constructores sin cleanup (MEDIO)

**Archivos afectados:** Todos los que hacen `.subscribe()` en constructor o `ngOnInit`
sin `takeUntilDestroyed()`.

```typescript
// curso-create.ts — constructor (líneas 79-95)
constructor() {
  this.cursoService.getCategorias().subscribe({ next: r => this.categorias.set(r.data) });
  this.cursoService.getPlanes().subscribe({ next: r => this.planes.set(r.data) });
  this.convenioService.getAll$().subscribe({ next: r => this.convenios.set(r) });
  this.form.get('nombre_programa')!.valueChanges.subscribe(...);  // nunca se desuscribe
}
```

Con Angular Zoneless, las suscripciones que nunca se completan (como `valueChanges`) son
memory leaks confirmados si el componente se destruye antes de que completen.

### Solución requerida

Usar `takeUntilDestroyed()` de `@angular/core/rxjs-interop`:

```typescript
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';

export class CursoCreate {
  private destroyRef = inject(DestroyRef);

  constructor() {
    this.form.get('nombre_programa')!.valueChanges
      .pipe(takeUntilDestroyed(this.destroyRef))
      .subscribe(nombre => {
        this.form.get('slug')!.setValue(generateSlug(nombre ?? ''), { emitEvent: false });
      });
  }
}
```

O mejor, reemplazar `valueChanges.subscribe()` con un `effect()`:

```typescript
// Angular 20: usar effect para sincronizar señales/formularios
// No requiere cleanup manual — se limpia solo al destruir el componente
```

---

## Hallazgo 9 — Non-null assertions sin guardia (BAJO)

```typescript
// curso-create.ts línea 86
this.form.get('nombre_programa')!.valueChanges.subscribe(...)
// Si 'nombre_programa' no existe en el FormGroup, this explota en runtime con TypeError
```

Dado que el control sí existe en el formulario, el riesgo es bajo, pero el patrón es frágil:
cualquier renombrado del control rompe silenciosamente en producción.

### Solución requerida

```typescript
const ctrl = this.form.get('nombre_programa');
if (ctrl) {
  ctrl.valueChanges.pipe(takeUntilDestroyed()).subscribe(...);
}
```

---

## Prioridad de corrección

| Prioridad | Hallazgo | Impacto | Esfuerzo estimado |
| --- | --- | --- | --- |
| 🔴 1 | **Hallazgo 1** — HttpClient en 37 componentes | Violación DDD fundamental | Alto (crear 1 servicio + refactorizar 37 archivos) |
| 🔴 2 | **Hallazgo 2** — Slug duplicado en 8 archivos | Lógica de dominio en UI | Bajo (crear util + search-replace) |
| 🟠 3 | **Hallazgo 3 + 4** — List pattern en 24+89 archivos | Deuda técnica masiva | Alto (extraer `createListState`) |
| 🟠 4 | **Hallazgo 5** — Upload duplicado en 19 archivos | Depende de Hallazgo 1 | Medio (extender `file-uploader` existente) |
| 🟡 5 | **Hallazgo 6** — Rutas monolíticas (761 líneas) | Mantenibilidad | Medio (dividir en 7 archivos) |
| 🟡 6 | **Hallazgo 7** — `as any` en 50+ formularios | Type safety | Alto (tedioso, por módulo) |
| 🟢 7 | **Hallazgo 8** — Sin `takeUntilDestroyed()` | Memory leaks potenciales | Medio |
| 🟢 8 | **Hallazgo 9** — Non-null assertions | Robustez | Bajo |

---

## Lo que está bien (no tocar)

- **Estructura de carpetas DDD:** Domain → Application → Presentation está correctamente separada.
- **Signals + Zoneless:** El uso de signals y zoneless change detection es correcto y moderno.
- **Standalone components:** Todos los componentes son standalone, correcto para Angular 20.
- **`inject()` sobre constructor injection:** Correcto, estilo moderno de Angular.
- **`ApiState` como tipo discriminado:** El patrón `{ type: 'loading' } | { type: 'success' }` es correcto — solo necesita abstraerse.
- **`extractErrorMessage()`:** La utilidad centralizada de error existe y se usa bien.
- **SweetAlert2 para destructivos:** Patrón correcto para confirmaciones.
- **`ToastService` signal-based:** Implementación correcta.
- **TypeScript strict:** `strict: true`, `strictTemplates`, `noPropertyAccessFromIndexSignature` — correcto.
- **Interceptor JWT funcional:** `authInterceptor` como función (no clase) — correcto.

---

## Resumen de números

| Métrica | Valor |
| --- | --- |
| Archivos `.ts` totales | 620 |
| Módulos de feature | ~100 |
| Componentes con `HttpClient` en presentación | **37** |
| Archivos con lógica de slug duplicada | **8** |
| Componentes con patrón de lista duplicado | **24** |
| Archivos con `refreshTrigger = signal(0)` | **89** |
| Componentes con upload directo hardcodeado | **19** |
| Líneas en archivo de rutas monolítico | **761** |
