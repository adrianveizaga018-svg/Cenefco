import { computed, inject, Injectable, OnDestroy, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import {
  EnviarComunicadoPayload,
  ListResponse,
  Notificacion,
  NotificacionGrupo,
  NotificacionPreferencia,
  NoLeidasResponse,
  ResumenEnviado,
  prioridadConfig,
  tiempoRelativo,
} from '../../domain/models/notificacion.model';
import { ToastService } from '../../../common/application/services/toast.service';

@Injectable({ providedIn: 'root' })
export class NotificacionesService implements OnDestroy {
  private http   = inject(HttpClient);
  private toast  = inject(ToastService);
  private router = inject(Router);

  readonly notificaciones   = signal<Notificacion[]>([]);
  readonly sinLeer          = signal(0);
  readonly pollingActivo    = signal(false);
  readonly detalleActivo    = signal<Notificacion | null>(null);

  readonly estadoConexion = computed<'polling' | 'desconectado'>(() =>
    this.pollingActivo() ? 'polling' : 'desconectado'
  );

  readonly notificacionesAgrupadas = computed<(Notificacion | NotificacionGrupo)[]>(() => {
    const map = new Map<string, Notificacion[]>();
    for (const n of this.notificaciones()) {
      if (!map.has(n.tipo)) map.set(n.tipo, []);
      map.get(n.tipo)!.push(n);
    }
    const result: (Notificacion | NotificacionGrupo)[] = [];
    for (const [tipo, items] of map) {
      if (items.length === 1) result.push(items[0]);
      else result.push({ tipo, items, count: items.length });
    }
    return result.sort((a, b) => {
      const da = 'created_at' in a ? a.created_at : (a as NotificacionGrupo).items[0].created_at;
      const db = 'created_at' in b ? b.created_at : (b as NotificacionGrupo).items[0].created_at;
      return new Date(db).getTime() - new Date(da).getTime();
    });
  });

  private pollTimer: ReturnType<typeof setInterval> | null = null;
  private idsAvisados = new Set<number>();

  conectar(): void {
    this.cargarNoLeidas();
    this.iniciarPolling();
  }

  private iniciarPolling(): void {
    if (this.pollTimer) return;
    this.pollingActivo.set(true);
    this.pollTimer = setInterval(() => this.cargarNoLeidas(), 30000);
  }

  private detenerPolling(): void {
    if (this.pollTimer) {
      clearInterval(this.pollTimer);
      this.pollTimer = null;
      this.pollingActivo.set(false);
    }
  }

  cargarNoLeidas(): void {
    this.http.get<NoLeidasResponse>('/api/v1/notificaciones/no-leidas').subscribe({
      next: ({ count, items }) => {
        this.sinLeer.set(count);
        this.notificaciones.set(items);

        for (const n of items) {
          if (!this.idsAvisados.has(n.id) && (n.prioridad === 'alta' || n.prioridad === 'critica')) {
            this.idsAvisados.add(n.id);
            this.toast.warning(n.titulo, n.mensaje ?? undefined);

            if (
              (n.tipo === 'compromiso_cobro' || n.tipo === 'compromiso_cobro_vencido') &&
              typeof Notification !== 'undefined' &&
              Notification.permission === 'granted'
            ) {
              const desktopNotif = new Notification(n.titulo, {
                body: n.mensaje ?? undefined,
                tag: `compromiso-cobro-${n.id}`,
                icon: '/assets/images/logo-sm.png',
                badge: '/assets/images/logo-sm.png',
              });
              desktopNotif.onclick = () => {
                window.focus();
                if (n.url_accion) this.router.navigateByUrl(n.url_accion);
              };
            }
          }
        }
      },
    });
  }

  abrirDetalle(notif: Notificacion): void {
    if (!notif.leida) {

      this.notificaciones.update(list =>
        list.map(n => n.id === notif.id ? { ...n, leida: true } : n)
      );
      this.sinLeer.update(n => Math.max(0, n - 1));
      this.http.get(`/api/v1/notificaciones/${notif.id}`).subscribe();
    }
    this.detalleActivo.set(notif);
  }

  marcarLeida(id: number): Observable<{ data: Notificacion }> {
    const obs = this.http.put<{ data: Notificacion }>(`/api/v1/notificaciones/${id}/leer`, {});
    obs.subscribe(() => {
      this.notificaciones.update(list => list.map(n => n.id === id ? { ...n, leida: true } : n));
      this.sinLeer.update(n => Math.max(0, n - 1));
    });
    return obs;
  }

  marcarTodasLeidas(): Observable<{ marcadas: number }> {
    const obs = this.http.put<{ marcadas: number }>('/api/v1/notificaciones/leer-todas', {});
    obs.subscribe(() => {
      this.notificaciones.update(list => list.map(n => ({ ...n, leida: true })));
      this.sinLeer.set(0);
    });
    return obs;
  }

  eliminar(id: number): Observable<void> {
    const obs = this.http.delete<void>(`/api/v1/notificaciones/${id}`);
    obs.subscribe(() => {
      this.notificaciones.update(list => list.filter(n => n.id !== id));
      this.sinLeer.update(n => Math.max(0, n - 1));
    });
    return obs;
  }

  getAll(params: Record<string, unknown>): Observable<ListResponse> {
    return this.http.get<ListResponse>('/api/v1/notificaciones', { params: params as any });
  }

  enviarComunicado(payload: EnviarComunicadoPayload): Observable<{ ok: boolean }> {
    const form = new FormData();
    form.append('titulo',       payload.titulo);
    form.append('mensaje',      payload.mensaje);
    form.append('prioridad',    payload.prioridad);
    form.append('destinatario', payload.destinatario);
    if (payload.tipo)         form.append('tipo',         payload.tipo);
    if (payload.rol_destino)  form.append('rol_destino',  payload.rol_destino);
    if (payload.expires_at)   form.append('expires_at',   payload.expires_at);
    if (payload.usuario_ids)  payload.usuario_ids.forEach(id => form.append('usuario_ids[]', String(id)));
    if (payload.imagen)       form.append('imagen', payload.imagen, payload.imagen.name);
    return this.http.post<{ ok: boolean }>('/api/v1/notificaciones/comunicado', form);
  }

  getEnviados(): Observable<ListResponse<ResumenEnviado>> {
    return this.http.get<ListResponse<ResumenEnviado>>('/api/v1/notificaciones/enviados');
  }

  getPreferencias(): Observable<{ data: NotificacionPreferencia[] }> {
    return this.http.get<{ data: NotificacionPreferencia[] }>('/api/v1/notificaciones/preferencias');
  }

  guardarPreferencias(preferencias: { tipo: string; activa: boolean }[]): Observable<{ ok: boolean }> {
    return this.http.put<{ ok: boolean }>('/api/v1/notificaciones/preferencias', { preferencias });
  }

  tiempoRelativo = tiempoRelativo;
  prioridadConfig = prioridadConfig;

  desconectar(): void {
    this.detenerPolling();
    this.idsAvisados.clear();
  }

  ngOnDestroy(): void {
    this.desconectar();
  }
}
