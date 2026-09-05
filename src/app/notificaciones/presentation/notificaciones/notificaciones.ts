import { Component, inject, signal, computed, ChangeDetectorRef, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { NotificacionesService } from '../../application/services/notificacion.service';
import { Notificacion, ListResponse, prioridadConfig, tiempoRelativo, tipoBadgeClass, tipoLabel, TIPOS_NOTIFICACION } from '../../domain/models/notificacion.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';

interface GrupoFecha {
  etiqueta: string;
  items: Notificacion[];
}

@Component({
  selector: 'app-notificaciones',
  standalone: true,
  imports: [NgIcon, RouterLink, PageTitle],
  templateUrl: './notificaciones.html',
})
export class NotificacionesPage implements OnInit {
  private notifSv = inject(NotificacionesService);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  notificaciones = signal<Notificacion[]>([]);
  total          = signal(0);
  loading        = signal(true);
  detalle        = signal<Notificacion | null>(null);
  lightbox       = signal<string | null>(null);

  filtroLeida  = signal<'' | 'true' | 'false'>('');
  filtroPrioridad = signal('');
  filtroTipo   = signal('');
  page         = signal(1);
  readonly pageSize = 20;

  tipos = TIPOS_NOTIFICACION;

  sinLeer = this.notifSv.sinLeer;

  prioridadConfig = prioridadConfig;
  tiempoRelativo  = tiempoRelativo;
  tipoBadgeClass  = tipoBadgeClass;
  tipoLabel       = tipoLabel;

  grupos = computed<GrupoFecha[]>(() => this.agruparPorFecha(this.notificaciones()));

  private agruparPorFecha(items: Notificacion[]): GrupoFecha[] {
    const hoy = new Date(); hoy.setHours(0, 0, 0, 0);
    const ayer = new Date(hoy); ayer.setDate(ayer.getDate() - 1);
    const semana = new Date(hoy); semana.setDate(semana.getDate() - 7);

    const baldes = new Map<string, Notificacion[]>();
    for (const n of items) {
      const fecha = new Date(n.created_at);
      const soloFecha = new Date(fecha); soloFecha.setHours(0, 0, 0, 0);

      let etiqueta: string;
      if (soloFecha.getTime() === hoy.getTime())        etiqueta = 'Hoy';
      else if (soloFecha.getTime() === ayer.getTime())  etiqueta = 'Ayer';
      else if (soloFecha.getTime() > semana.getTime())  etiqueta = 'Esta semana';
      else                                                etiqueta = 'Anteriores';

      if (!baldes.has(etiqueta)) baldes.set(etiqueta, []);
      baldes.get(etiqueta)!.push(n);
    }

    const orden = ['Hoy', 'Ayer', 'Esta semana', 'Anteriores'];
    return orden
      .filter(etiqueta => baldes.has(etiqueta))
      .map(etiqueta => ({ etiqueta, items: baldes.get(etiqueta)! }));
  }

  ngOnInit() {
    this.cargar();
  }

  cargar() {
    this.loading.set(true);
    const params: Record<string, unknown> = { pageIndex: this.page(), pageSize: this.pageSize };
    if (this.filtroLeida())     params['leida']     = this.filtroLeida();
    if (this.filtroPrioridad()) params['prioridad'] = this.filtroPrioridad();
    if (this.filtroTipo())      params['tipo']      = this.filtroTipo();

    this.notifSv.getAll(params).subscribe({
      next: (res: ListResponse) => {
        this.notificaciones.set(res.data);
        this.total.set(res.total);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron cargar las notificaciones.');
        this.loading.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  aplicarFiltros() {
    this.page.set(1);
    this.cargar();
  }

  limpiarFiltros() {
    this.filtroLeida.set('');
    this.filtroPrioridad.set('');
    this.filtroTipo.set('');
    this.page.set(1);
    this.cargar();
  }

  abrirDetalle(n: Notificacion) {
    this.detalle.set(n);
    if (!n.leida) {
      this.notifSv.marcarLeida(n.id).subscribe(() => {
        this.notificaciones.update(list => list.map(x => x.id === n.id ? { ...x, leida: true } : x));
        this.cdr.detectChanges();
      });
    }
  }

  cerrarDetalle() {
    this.detalle.set(null);
  }

  eliminar(n: Notificacion) {
    if (!confirm('¿Eliminar esta notificación?')) return;
    this.notifSv.eliminar(n.id).subscribe(() => {
      this.notificaciones.update(list => list.filter(x => x.id !== n.id));
      this.total.update(t => t - 1);
      if (this.detalle()?.id === n.id) this.detalle.set(null);
      this.cdr.detectChanges();
    });
  }

  get totalPages() {
    return Math.ceil(this.total() / this.pageSize);
  }

  irPagina(p: number) {
    if (p < 1 || p > this.totalPages) return;
    this.page.set(p);
    this.cargar();
  }

  abrirLightbox(url: string) {
    this.lightbox.set(url);
  }

  cerrarLightbox() {
    this.lightbox.set(null);
  }
}
