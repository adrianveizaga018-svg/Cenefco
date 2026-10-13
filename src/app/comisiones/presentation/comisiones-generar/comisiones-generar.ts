import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe, SlicePipe } from '@angular/common';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select';
import { ToastService } from '../../../common/application/services/toast.service';
import { ComisionService } from '../../application/services/comision.service';
import { ComisionSugerida } from '../../domain/models/comision.model';
import { VendedorService } from '../../../vendedores/application/services/vendedor.service';
import { TablaComisionesService } from '../../application/services/tabla-comisiones.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { ComisionLiquidacionDetalle } from '../../domain/models/comision.model';
import Swal from 'sweetalert2';

/** Los nombres los escriben los propios estudiantes: se escapan antes de ponerlos en HTML. */
const esc = (texto: string | null | undefined): string =>
  (texto ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]!));

@Component({
  selector: 'app-comisiones-generar',
  imports: [FormsModule, RouterLink, NgIcon, DecimalPipe, SlicePipe, PageTitle, SearchableSelect],
  templateUrl: './comisiones-generar.html',
})
export class ComisionesGenerar implements OnInit {
  private comisionSvc = inject(ComisionService);
  private vendedorSvc = inject(VendedorService);
  private toast       = inject(ToastService);
  private router       = inject(Router);
  private tablaSvc     = inject(TablaComisionesService);

  /** Acordar montos (ventas negociadas) es de Administración / Contabilidad */
  readonly puedeAcordar = inject(AuthService).hasPermission('comisiones.configurar');

  vendedorOptions = signal<SelectOption[]>([]);
  vendedorId      = signal<number | null>(null);

  fechaDesde = signal(this.primerDiaDelMes());
  fechaHasta = signal(this.hoyIso());
  nota       = signal('');

  calculando = signal(false);
  generando  = signal(false);
  sugerida   = signal<ComisionSugerida | null>(null);
  buscado    = signal(false);

  ngOnInit(): void {
    this.vendedorSvc.getAll({ pageSize: 200 }).subscribe({
      next: r => this.vendedorOptions.set(r.data.map(v => ({
        value: v.id,
        label: `${v.nombre} ${v.apellido}`,
      }))),
      error: () => this.toast.error('Error', 'No se pudo cargar la lista de vendedores.'),
    });
  }

  private hoyIso(): string {
    return new Date().toISOString().split('T')[0];
  }

  private primerDiaDelMes(): string {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-01`;
  }

  calcular(): void {
    const vendedorId = this.vendedorId();
    if (!vendedorId) {
      this.toast.error('Falta el vendedor', 'Selecciona un vendedor antes de calcular.');
      return;
    }
    if (this.fechaHasta() < this.fechaDesde()) {
      this.toast.error('Rango inválido', 'La fecha hasta no puede ser anterior a la fecha desde.');
      return;
    }

    this.calculando.set(true);
    this.sugerida.set(null);
    this.buscado.set(false);

    this.comisionSvc.getSugerida(vendedorId, this.fechaDesde(), this.fechaHasta()).subscribe({
      next: res => {
        this.sugerida.set(res);
        this.calculando.set(false);
        this.buscado.set(true);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo calcular la comisión sugerida.');
        this.calculando.set(false);
        this.buscado.set(true);
      },
    });
  }

  /** Monto acordado para una venta negociada (o corregir el de la tabla), con su motivo. */
  async acordar(venta: ComisionLiquidacionDetalle): Promise<void> {
    const { value } = await Swal.fire({
      title: 'Comisión acordada',
      html: `<p class="text-sm text-start mb-3">${esc(venta.estudiante ?? 'Inscripción #' + venta.id_ins)}<br><span class="text-xs">${esc(venta.programa)} · ${esc(venta.regla)}</span></p>
        <label class="block text-start text-xs mb-1" for="acuerdo-monto">Monto de la comisión (Bs.)</label>
        <input id="acuerdo-monto" type="number" min="0" step="0.01" class="swal2-input !m-0 !w-full" value="${venta.comision_monto ?? ''}">
        <label class="block text-start text-xs mt-3 mb-1" for="acuerdo-motivo">Motivo (queda registrado)</label>
        <textarea id="acuerdo-motivo" class="swal2-textarea !m-0 !w-full" placeholder="Ej. Negociado con dirección: 10 % del contado">${esc(venta.motivo_acuerdo)}</textarea>`,
      showCancelButton: true, confirmButtonText: 'Guardar', cancelButtonText: 'Cancelar',
      preConfirm: () => {
        const monto = (document.getElementById('acuerdo-monto') as HTMLInputElement).value;
        const motivo = (document.getElementById('acuerdo-motivo') as HTMLTextAreaElement).value.trim();
        if (monto === '' || Number(monto) < 0) { Swal.showValidationMessage('Escribe el monto acordado.'); return false; }
        if (motivo.length < 3) { Swal.showValidationMessage('Escribe el motivo del acuerdo.'); return false; }
        return { monto: Number(monto), motivo };
      },
    });
    if (!value) return;

    this.tablaSvc.acordar({ id_ins: venta.id_ins, monto: value.monto, vendedor_id: null, motivo: value.motivo }).subscribe({
      next: () => { this.toast.success('Comisión acordada', `Bs. ${value.monto.toFixed(2)}`); this.calcular(); },
      error: (err) => this.toast.error('No se pudo guardar', err?.error?.message ?? 'Inténtalo de nuevo.'),
    });
  }

  generar(): void {
    const vendedorId = this.vendedorId();
    const sugerida = this.sugerida();
    if (!vendedorId || !sugerida || sugerida.inscritos.length === 0) return;

    this.generando.set(true);
    this.comisionSvc.crear({
      vendedor_id: vendedorId,
      fecha_desde: this.fechaDesde(),
      fecha_hasta: this.fechaHasta(),
      nota: this.nota() || null,
    }).subscribe({
      next: liquidacion => {
        this.generando.set(false);
        this.toast.success('Liquidación generada', `Comisión de Bs. ${liquidacion.monto_comision} generada para ${liquidacion.vendedor_nombre}.`);
        this.router.navigate(['/cenefco/comisiones']);
      },
      error: (err) => {
        this.generando.set(false);
        this.toast.error('Error', err?.error?.message ?? 'No se pudo generar la liquidación.');
      },
    });
  }
}
