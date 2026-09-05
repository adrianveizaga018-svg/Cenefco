import { Component, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { NotificacionesService } from '../../../../../notificaciones/application/services/notificacion.service';
import { Notificacion, NotificacionGrupo, prioridadConfig, tiempoRelativo } from '../../../../../notificaciones/domain/models/notificacion.model';

type Tab = 'todas' | 'sin-leer' | 'comunicados';

@Component({
  selector: 'app-notification-dropdown',
  imports: [NgIcon, RouterLink],
  templateUrl: './notification-dropdown.html',
})
export class NotificationDropdown {
  readonly notifSv = inject(NotificacionesService);

  tabActiva    = signal<Tab>('todas');
  grupoAbierto = signal<string | null>(null);

  get itemsFiltrados(): (Notificacion | NotificacionGrupo)[] {
    const tab = this.tabActiva();
    const all = this.notifSv.notificacionesAgrupadas();
    if (tab === 'sin-leer')    return all.filter(i =>
      this.esGrupo(i) ? i.items.some(n => !n.leida) : !(i as Notificacion).leida
    );
    if (tab === 'comunicados') return all.filter(i =>
      this.esGrupo(i) ? i.tipo === 'comunicado' : (i as Notificacion).tipo === 'comunicado'
    );
    return all;
  }

  esGrupo(item: Notificacion | NotificacionGrupo): item is NotificacionGrupo {
    return 'items' in item;
  }

  toggleGrupo(tipo: string): void {
    this.grupoAbierto.update(v => v === tipo ? null : tipo);
  }

  abrirDetalle(notif: Notificacion): void {
    this.notifSv.abrirDetalle(notif);
  }

  marcarTodas(): void {
    this.notifSv.marcarTodasLeidas().subscribe();
  }

  estadoLabel(): string {
    const e = this.notifSv.estadoConexion();
    if (e === 'polling') return 'Actualización cada 30s';
    return 'Sin conexión — reintentando...';
  }

  tieneCritica(): boolean {
    return this.notifSv.notificaciones().some(n => !n.leida && n.prioridad === 'critica');
  }

  prioridadConfig = prioridadConfig;
  tiempoRelativo  = tiempoRelativo;
}
