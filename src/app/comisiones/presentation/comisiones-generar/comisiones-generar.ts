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
