# Análisis: parametrizar comisiones de vendedores por categoría de programa

**Fecha:** 2026-07-29 (actualizado el mismo día tras revisión con el usuario)
**Alcance:** solo análisis — no se modificó código todavía. Objetivo: mover el cálculo de comisión de vendedores desde un `%` fijo por vendedor hacia un **monto fijo en Bs. por inscrito, parametrizable por categoría de programa** (`/cenefco/categoria-programa-edit/{id}`), reemplazando por completo el `%` y usando la relación curso→categoría para distinguir Curso con Aval, Curso con Resolución Ministerial y Diplomado.

> Este documento se revisó pregunta por pregunta con el usuario. Las decisiones de negocio quedaron cerradas en la sección 4; el diseño de la sección 3 ya refleja esas decisiones (no es el diseño original propuesto en la primera versión de este análisis).

---

## 1. Cómo funciona hoy (verificado en código)

### 1.1 Modelo actual: % plano por vendedor, sobre el monto pagado

- `vendedores.comision` — `decimal(5,2)` nullable, **un único porcentaje por vendedor** (migración `2026_05_29_000001_create_vendedores_table.php`). No existe por curso, ni por categoría, ni por inscrito.
- El cálculo real de liquidaciones vive en `app/Application/Comisiones/Services/ComisionCalculadorService.php`:
  ```php
  // pagosElegibles(): pagos verificados + activos, del vendedor, en el rango de fechas,
  // atribuidos por t_programa.vendedor_id (curso asignado por completo a un vendedor)
  'monto_comision' => round($totalBase * $porcentajeComision / 100, 2),
  ```
  Es decir: **suma de `monto_pagado` de pagos verificados × `%` del vendedor**. No hay noción de "por inscrito" ni de tipo de programa.
- Existe un segundo cálculo, **solo informativo** (no liquidado), en `VendedorComisionEstimadaService`: cuenta inscritos activos × `t_programa.costo_monto` × `%` del vendedor. Tampoco distingue aval/RM/diplomado.
- La atribución de la venta a un vendedor es **por curso completo** vía `t_programa.vendedor_id` (no por inscripción individual — el código documenta explícitamente que `t_inscripcion.id_vendedor` no se usa para esto porque en la práctica está vacío).

### 1.2 Categoría de programa: sin ningún campo de comisión hoy

`web_categoria_programa` (modelo `CategoriaPrograma`, editado en `/cenefco/categoria-programa-edit/{id}`) tiene: `nombre`, `slug`, `descripcion`, `imagen_url`, `icono`, `color`, `orden`, `activo`, `meta_titulo`, `meta_descripcion`, `tipo_programa_id`. **Ningún campo de monto o comisión.**

Dato relevante: **"Diplomado" ya existe como una categoría propia** (`database/seeders/CategoriaProgramaSeeder.php`), al mismo nivel que categorías temáticas (ej. Tecnología, Salud). Esto confirma que la categoría es el lugar correcto para colgar la tarifa de 300 Bs de diplomado — pero también que "categoría" y "modalidad legal del curso" son dos ejes distintos que hay que separar bien (ver sección 3).

### 1.3 Ya existe un precedente de diseño para aval/RM — pero es de otro módulo (Honorarios de Docentes)

```php
// app/Domain/Honorarios/Enums/TipoHonorario.php
enum TipoHonorario: string
{
    case DiplomadoFijo = 'diplomado_fijo';
    case RmPorDia       = 'rm_por_dia';   // Resolución Ministerial
    case AvalPorDia     = 'aval_por_dia';
}
```
Tabla `web_config_honorario_programa` (1:1 con `t_programa.id_programa`): guarda `tipo_honorario` + `monto_fijo` (diplomado) o `monto_por_dia` (RM/aval). Se llena desde `StoreCursoRequest`/`UpdateCursoRequest` al crear/editar un curso, y calcula lo que se le paga **al docente**, no al vendedor. Es **por día dictado**, no por inscrito.

**Esto es exactamente el precedente que el usuario está pidiendo replicar**, pero para vendedores y por inscrito en vez de por día. Confirma que el negocio ya piensa en estos tres casos (diplomado fijo / RM / aval) como una clasificación real del curso — solo que hoy vive exclusivamente ligada a honorarios docentes.

`t_programa` **no tiene** ningún campo propio de "modalidad legal" (aval/RM). Existió `avalado_sib` (boolean) pero fue eliminado en `2026_07_17_000002_drop_regalia_sib.php` junto con el concepto de regalía SIB — ya no existe en el esquema.

---

## 2. El problema: no es una simple "mudanza", es un cambio de eje de cálculo

Lo que hoy existe (% del vendedor sobre plata cobrada) y lo que se pide (monto fijo por inscrito, según categoría del curso) son dos modelos de negocio distintos:

| | Hoy | Nuevo (decidido) |
|---|---|---|
| Base de cálculo | % sobre `monto_pagado` (dinero) | monto fijo Bs. por **inscrito con pago verificado** |
| Dónde vive la tarifa | `vendedores.comision` (por vendedor) | `web_categoria_programa` (por categoría) |
| Variable que decide el monto | quién es el vendedor | a qué categoría pertenece el curso del inscrito |

Nota de la primera versión de este análisis (ya resuelta en la sección 4): en un primer momento se pensó que "categoría" (tema comercial: Tecnología, Salud...) y "modalidad legal del curso" (aval / Resolución Ministerial) eran dos ejes distintos que se cruzaban, lo que hubiera obligado a guardar **dos montos por categoría** (uno para aval, uno para RM) más un tercer campo en el curso para saber cuál aplica. El usuario aclaró que **no es así**: cada curso pertenece a una sola categoría (`t_programa.categoria_web_id`, relación singular ya existente), y esa categoría por sí sola ya determina la tarifa — no hace falta ningún campo adicional de "modalidad" en el curso. Esto simplifica el diseño a **un solo monto por categoría**.

---

## 3. Diseño (ya reflejando las decisiones tomadas)

### 3.1 Nuevo campo en `web_categoria_programa`

```php
Schema::table('web_categoria_programa', function (Blueprint $table) {
    $table->decimal('comision_monto', 8, 2)->after('activo'); // Bs. por inscrito con pago verificado
});
```

- **Un solo monto por categoría**, no tres — cada categoría (Diplomado, Curso con Aval, Curso con Resolución Ministerial, o cualquier otra que se cree) trae su propio monto en bolivianos.
- **Campo obligatorio** (`required`, sin default ni `nullable`) tanto en `StoreCategoriaProgramaRequest` como en `UpdateCategoriaProgramaRequest`: no se puede crear ni dejar guardada una categoría sin definir su comisión por inscrito.
- No existe el concepto de `%` en ningún punto de este nuevo modelo — el campo `vendedores.comision` deja de usarse para calcular nada (ver sección 3.4).
- Los rangos que dio el usuario (80–100 Bs RM, 35–40 Bs aval, 300 Bs diplomado) son guía de negocio para poblar este campo al crear cada categoría — no van hardcodeados en ningún lado, cada categoría define su propio número exacto.

### 3.2 Cómo se determina la tarifa de un curso

No hace falta ningún campo nuevo de "modalidad" en `t_programa`/`Curso`, ni reutilizar `web_config_honorario_programa.tipo_honorario` (que sigue existiendo, sin cambios, solo para honorarios de **docentes**). La relación ya existente `t_programa.categoria_web_id → web_categoria_programa.id` es toda la información necesaria: un curso pertenece a una sola categoría, y esa categoría ya trae su `comision_monto`. Por diseño esto es mutuamente excluyente (un curso nunca puede tener dos tarifas a la vez, porque solo puede estar en una categoría).

### 3.3 Nueva regla de cálculo de comisión

Reemplazar en `ComisionCalculadorService`:

```php
// Antes: por vendedor, sobre dinero, con %
monto_comision = total_pagado_periodo * porcentaje_vendedor / 100

// Nuevo: por curso, por inscrito con pago verificado, según la categoría del curso
foreach (curso in cursos_del_vendedor_en_periodo) {
    tarifa = curso.categoria.comision_monto;
    monto_comision += inscritos_con_pago_verificado(curso, periodo) * tarifa;
}
```

"Inscrito con pago verificado" es exactamente el mismo criterio de elegibilidad que ya usa hoy `pagosElegibles()` (pago `estado_verificacion = verificado`, `estado = activo`, dentro del rango de fechas del corte) — solo cambia qué se hace con cada pago elegible: antes se sumaba su `monto_pagado` para aplicarle el `%`; ahora se cuenta como **una unidad** (un inscrito) que dispara el monto fijo de su categoría. El "corte" (liquidación por rango `fecha_desde`/`fecha_hasta`) se mantiene exactamente igual que hoy — la variación en duración de los cursos (1 día, 3 semanas, 2 meses) no afecta la regla, porque lo que dispara la comisión es el evento de pago verificado dentro del corte, no la duración del curso.

### 3.4 Cambios en la pantalla `categoria-programa-edit`

Agregar al formulario (`categoria-programa-edit.ts`/`.html`) un input obligatorio:
- "Comisión por inscrito (Bs.)" — `comision_monto`, `Validators.required`, numérico, con texto de ayuda mencionando los rangos de referencia (RM: 80–100, Aval: 35–40, Diplomado: 300) para guiar al admin al definir el valor de cada categoría nueva.

### 3.5 Qué pasa con lo que ya existe (compatibilidad)

- Las liquidaciones ya generadas en `comisiones_liquidacion`/`comisiones_liquidacion_detalle` quedan intactas como registro histórico (`total_base`, `porcentaje_comision`) — no se recalculan con la regla nueva; el corte es hacia adelante a partir de que se despliegue el cambio.
- `vendedores.comision` (%) se elimina del flujo de cálculo. Queda pendiente decidir en la implementación si se retira la columna de la tabla `vendedores` o simplemente se deja de usar (impacto en el frontend `vendedor-edit`/`vendedor-create`, que hoy la muestra).
- `VendedorComisionEstimadaService` (la vista "estimada", no liquidada, usada en el listado de vendedores y su PDF) debe reescribirse con la misma regla nueva para no mostrar cifras inconsistentes con la liquidación real — es el mismo riesgo de "doble fuente de cálculo divergente" ya visto una vez en el módulo de Pagos (ver `PagoCalculadorService` en `CLAUDE.md`) y en la auditoría de Cursos.
- Los DTOs de frontend (`ComisionSugerida`, `VendedorComisionDetalle`, `VendedorComisionCurso`) necesitan cambiar sus campos: hoy muestran `costo_monto`/`subtotal`/`comision_porcentaje` en dinero — pasan a mostrar `cantidad_inscritos`, `comision_monto` (tarifa de la categoría) y `total_comision` (cantidad × tarifa), sin ningún `%`.

---

## 4. Decisiones de negocio (cerradas con el usuario, 2026-07-29)

1. **¿El monto fijo reemplaza el `%` o conviven?** → Se elimina el `%` por completo. Todo pasa a ser montos fijos en Bs.
2. **¿De dónde sale si un curso es "con aval" o "con RM"?** → No hace falta saberlo aparte: la categoría del curso (relación ya existente `categoria_web_id`) ya determina la tarifa directamente. No se reutiliza `tipo_honorario` ni se crea un campo de modalidad nuevo.
3. **¿Un curso puede ser aval y RM a la vez?** → No, mutuamente excluyente (un curso pertenece a una sola categoría).
4. **¿Inscrito elegible = matriculado o con pago verificado?** → Con pago verificado, igual que la regla actual.
5. **¿Categoría sin tarifa configurada = comisión 0?** → No aplica: la comisión es **obligatoria** al crear cualquier categoría, nunca puede quedar sin definir.
6. **¿Retrocompatibilidad con liquidaciones ya pagadas?** → Se trabaja con "cortes": el historial ya generado queda como está (auditoría), y el nuevo cálculo aplica hacia adelante para los próximos cortes/liquidaciones. El mecanismo de corte por rango de fechas no cambia, solo la fórmula interna.

---

## 5. Plan de implementación

1. Migración: agregar `comision_monto` (decimal, **not nullable**) a `web_categoria_programa`; actualizar `CategoriaProgramaDTO`, `StoreCategoriaProgramaRequest`/`UpdateCategoriaProgramaRequest` (validación `required|numeric|min:0.01`), repositorio y controller.
2. UI: input obligatorio "Comisión por inscrito (Bs.)" en `categoria-programa-edit.html`/`.ts` (y en el flujo de creación de categoría, si existe una pantalla separada de alta).
3. Reescribir `ComisionCalculadorService` (cálculo por inscrito con pago verificado × `comision_monto` de la categoría del curso), dentro de `DB::transaction()` igual que hoy, sin tocar liquidaciones históricas.
4. Reescribir `VendedorComisionEstimadaService` con la misma regla, para que "estimado" y "liquidado" no diverjan.
5. Actualizar DTOs y pantallas de frontend (`comisiones-generar`, `comisiones-historial`, `vendedor-edit` detalle de comisión, `VendedorComisionCurso`/`VendedorComisionDetalle`) para reflejar cantidad de inscritos × tarifa en vez de `%`.
6. Decidir y ejecutar el retiro de `vendedores.comision` del flujo (columna/UI en `vendedor-edit`, `vendedor-create`, listado de vendedores que hoy muestra el badge de `%`).
7. Escribir/actualizar tests de `ComisionCalculadorService` (unitario, mockeando el repositorio) y feature test del endpoint `POST /comisiones` con el nuevo cálculo.
