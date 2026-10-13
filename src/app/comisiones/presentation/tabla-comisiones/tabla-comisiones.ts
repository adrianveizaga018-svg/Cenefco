import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { ProductoComision, ProgramaComision, TablaComisionesService } from '../../application/services/tabla-comisiones.service';

/**
 * Tabla de comisiones: cuánto gana el vendedor por venta según el tipo de producto y el precio,
 * y de qué tipo es cada programa. La mantiene Administración / Contabilidad.
 */
@Component({
  selector: 'app-tabla-comisiones',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, DecimalPipe],
  templateUrl: './tabla-comisiones.html',
})
export class TablaComisionesComponent implements OnInit {
  private svc = inject(TablaComisionesService);
  private toast = inject(ToastService);
  readonly puedeEditar = inject(AuthService).hasPermission('comisiones.configurar');

  cargando = signal(true);
  productos = signal<ProductoComision[]>([]);
  programas = signal<ProgramaComision[]>([]);
  editando = signal<ProductoComision | null>(null);
  guardando = signal(false);

  filtro = signal('');
  soloSinTipo = signal(false);
  cambios = signal<Map<number, number | null>>(new Map());

  readonly programasVisibles = computed(() => {
    const f = this.filtro().trim().toLowerCase();
    return this.programas().filter(p =>
      (!f || p.nombre.toLowerCase().includes(f)) && (!this.soloSinTipo() || this.tipoDe(p) === null));
  });
  readonly sinTipo = computed(() => this.programas().filter(p => this.tipoDe(p) === null).length);

  ngOnInit(): void { this.cargar(); }

  cargar(): void {
    this.cargando.set(true);
    this.svc.tabla().subscribe({
      next: (t) => { this.productos.set(t.productos); this.programas.set(t.programas); this.cambios.set(new Map()); this.cargando.set(false); },
      error: () => { this.cargando.set(false); this.toast.error('No se pudo cargar', 'Inténtalo de nuevo.'); },
    });
  }

  nombreProducto(id: number | null): string {
    return this.productos().find(p => p.id === id)?.nombre ?? 'Sin tipo';
  }

  // ── Productos ───────────────────────────────────────────────────────────
  nuevo(): void {
    this.editando.set({ id: 0, nombre: '', modalidad: 'rangos', porcentaje_minimo_pago: 50, activo: true, rangos: [{ desde: 0, hasta: null, monto: 0 }] });
  }

  editar(p: ProductoComision): void {
    this.editando.set(structuredClone(p));
  }

  agregarRango(): void {
    this.editando.update(p => {
      if (!p) return p;
      const ultimo = p.rangos[p.rangos.length - 1];
      const desde = ultimo?.hasta != null ? ultimo.hasta + 1 : 0;
      return { ...p, rangos: [...p.rangos, { desde, hasta: null, monto: 0 }] };
    });
  }

  quitarRango(i: number): void {
    this.editando.update(p => p ? { ...p, rangos: p.rangos.filter((_, j) => j !== i) } : p);
  }

  guardarProducto(): void {
    const p = this.editando();
    if (!p) return;
    if (!p.nombre.trim()) { this.toast.warning('Falta el nombre', 'Ej. Cursos con aval'); return; }
    this.guardando.set(true);
    this.svc.guardarProducto({ ...p, id: p.id || undefined, rangos: p.modalidad === 'rangos' ? p.rangos : [] }).subscribe({
      next: () => { this.guardando.set(false); this.editando.set(null); this.toast.success('Tabla actualizada', p.nombre); this.cargar(); },
      error: (err) => { this.guardando.set(false); this.toast.error('No se pudo guardar', err?.error?.message ?? 'Revisa los rangos.'); },
    });
  }

  // ── Programas ───────────────────────────────────────────────────────────
  tipoDe(p: ProgramaComision): number | null {
    return this.cambios().has(p.id_programa) ? this.cambios().get(p.id_programa)! : p.producto_id;
  }

  cambiarTipo(p: ProgramaComision, valor: string): void {
    const nuevo = valor ? Number(valor) : null;
    this.cambios.update(m => {
      const n = new Map(m);
      if (nuevo === p.producto_id) n.delete(p.id_programa); else n.set(p.id_programa, nuevo);
      return n;
    });
  }

  guardarProgramas(): void {
    const asignaciones = [...this.cambios()].map(([id_programa, producto_id]) => ({ id_programa, producto_id }));
    if (!asignaciones.length) return;
    this.guardando.set(true);
    this.svc.asignarProgramas(asignaciones).subscribe({
      next: (r) => { this.guardando.set(false); this.toast.success('Programas actualizados', `${r.actualizados} programa(s)`); this.cargar(); },
      error: (err) => { this.guardando.set(false); this.toast.error('No se pudo guardar', err?.error?.message ?? 'Inténtalo de nuevo.'); },
    });
  }
}
