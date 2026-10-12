import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { CrmService, ETAPAS, ORIGENES, Origen, TableroCrm } from '../../application/services/crm.service';

/** Tablero de ventas: cómo va cada vendedor en el periodo (las ventas cuentan lo cobrado). */
@Component({
  selector: 'app-tablero-crm',
  standalone: true,
  imports: [PageTitle, NgIcon, FormsModule, DecimalPipe, RouterLink],
  templateUrl: './tablero.html',
})
export class TableroCrmComponent implements OnInit {
  private crm   = inject(CrmService);
  private toast = inject(ToastService);
  private auth  = inject(AuthService);

  readonly supervisa = this.auth.hasPermission('crm.supervisar');
  readonly etapas = ETAPAS;

  desde    = signal(this.inicioDeMes());
  hasta    = signal(new Date().toISOString().slice(0, 10));
  datos    = signal<TableroCrm | null>(null);
  cargando = signal(true);

  /** Barras del embudo relativas a la etapa con más interesados. */
  readonly maxEmbudo = computed(() => Math.max(1, ...Object.values(this.datos()?.embudo ?? { x: 0 })));

  ngOnInit(): void { this.cargar(); }

  cargar(): void {
    this.cargando.set(true);
    this.crm.tablero(this.desde(), this.hasta()).subscribe({
      next: (d) => { this.datos.set(d); this.cargando.set(false); },
      error: (err: HttpErrorResponse) => {
        this.cargando.set(false);
        this.toast.error('No se pudo cargar el tablero', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  periodo(tipo: 'mes' | 'mes_anterior' | 'semana'): void {
    const hoy = new Date();
    if (tipo === 'mes') {
      this.desde.set(this.inicioDeMes()); this.hasta.set(this.iso(hoy));
    } else if (tipo === 'mes_anterior') {
      const ini = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
      const fin = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
      this.desde.set(this.iso(ini)); this.hasta.set(this.iso(fin));
    } else {
      const ini = new Date(hoy); ini.setDate(hoy.getDate() - 6);
      this.desde.set(this.iso(ini)); this.hasta.set(this.iso(hoy));
    }
    this.cargar();
  }

  nombreOrigen(o: Origen): string { return ORIGENES[o] ?? o; }

  claseMeta(avance: number | null): string {
    if (avance === null) return 'bg-default-300';
    if (avance >= 90) return 'bg-success';
    if (avance >= 60) return 'bg-primary';
    return 'bg-amber-500';
  }

  horas(h: number | null): string {
    if (h === null) return '—';
    if (h < 1) return `${Math.round(h * 60)} min`;
    return h < 48 ? `${h.toFixed(1)} h` : `${(h / 24).toFixed(1)} días`;
  }

  private inicioDeMes(): string {
    const h = new Date();
    return this.iso(new Date(h.getFullYear(), h.getMonth(), 1));
  }

  private iso(d: Date): string {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  }
}
