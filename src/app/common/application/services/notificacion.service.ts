import { inject, Injectable, OnDestroy, signal } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { ToastService } from './toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

export interface VentaNotificacion {
  id: number;
  numero_venta: string;
  cliente: string;
  total: number;
  estado: string;
  tipo_venta: string;
  fecha_venta: string;
}

@Injectable({ providedIn: 'root' })
export class NotificacionService implements OnDestroy {
  notificaciones = signal<VentaNotificacion[]>([]);
  unreadCount = signal(0);

  private http = inject(HttpClient);
  private toast = inject(ToastService);

  private lastId = 0;
  private intervalId: ReturnType<typeof setInterval> | null = null;
  private connecting = false;
  private readonly pollUrl = '/api/v1/notificaciones/poll';
  private readonly intervalMs = 8000;

  connect(): void {
    if (this.intervalId || this.connecting) return;
    this.connecting = true;

    this.http.get<{ ventas: VentaNotificacion[] }>(`${this.pollUrl}?last_id=0`).subscribe({
      next: ({ ventas }) => {
        if (ventas?.length > 0) {
          this.lastId = ventas[ventas.length - 1].id;
        }
        this.connecting = false;
        this.intervalId = setInterval(() => this.poll(), this.intervalMs);
      },
      error: (err: HttpErrorResponse) => {
        this.connecting = false;
        this.intervalId = setInterval(() => this.poll(), this.intervalMs);
      },
    });
  }

  private poll(): void {
    this.http.get<{ ventas: VentaNotificacion[] }>(`${this.pollUrl}?last_id=${this.lastId}`).subscribe({
      next: ({ ventas }) => {
        for (const venta of ventas) {
          this.lastId = venta.id;
          this.notificaciones.update(list => [venta, ...list].slice(0, 30));
          this.unreadCount.update(n => n + 1);

          this.toast.success(
            `Nueva venta: ${venta.numero_venta}`,
            `${venta.cliente} — Bs. ${venta.total.toLocaleString('es-BO', { minimumFractionDigits: 2 })}`
          );
        }
      },
    });
  }

  markAllRead(): void {
    this.unreadCount.set(0);
  }

  ngOnDestroy(): void {
    if (this.intervalId) clearInterval(this.intervalId);
  }
}
