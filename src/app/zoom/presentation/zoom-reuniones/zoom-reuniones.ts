import { Component, inject, signal, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ZoomService } from '../../application/services/zoom.service';
import { ZoomCuenta, ZoomReunion } from '../../domain/models/zoom.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({ selector: 'app-zoom-reuniones', imports: [NgIcon, PageTitle, RouterLink, DatePipe, FormsModule], templateUrl: './zoom-reuniones.html' })
export class ZoomReuniones implements OnInit {
  private service = inject(ZoomService);
  private toast   = inject(ToastService);

  cuentas       = signal<ZoomCuenta[]>([]);
  cuentaSelId   = signal<number | null>(null);
  reuniones     = signal<ZoomReunion[]>([]);
  loading       = signal(false);
  loadingCuentas = signal(true);
  error         = signal('');

  ngOnInit(): void {
    this.service.getCuentas().subscribe({
      next: (res) => {
        this.cuentas.set(res.data);
        this.loadingCuentas.set(false);
        const pred = res.data.find(c => c.predeterminada) ?? res.data[0];
        if (pred) { this.cuentaSelId.set(pred.id); this.cargar(pred.id); }
      },
      error: () => this.loadingCuentas.set(false)
    });
  }

  onCuentaChange(idStr: string): void {
    const id = Number(idStr);
    this.cuentaSelId.set(id);
    this.cargar(id);
  }

  cargar(cuentaId: number): void {
    this.loading.set(true);
    this.error.set('');
    this.service.getReuniones(cuentaId).subscribe({
      next: (res) => { this.reuniones.set(res.meetings ?? []); this.loading.set(false); },
      error: (err) => { this.error.set(err?.error?.message ?? 'No se pudo conectar con Zoom'); this.loading.set(false); }
    });
  }

  get cuentaActual(): ZoomCuenta | undefined {
    return this.cuentas().find(c => c.id === this.cuentaSelId());
  }

  copiarLink(url: string): void {
    navigator.clipboard.writeText(url).then(() => this.toast.success('Copiado', 'Enlace copiado al portapapeles'));
  }
}
