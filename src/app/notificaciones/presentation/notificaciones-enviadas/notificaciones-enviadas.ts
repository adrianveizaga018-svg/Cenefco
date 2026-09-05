import { Component, inject, signal, computed, ChangeDetectorRef, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NotificacionesService } from '../../application/services/notificacion.service';
import { prioridadConfig, ResumenEnviado, tiempoRelativo, tipoBadgeClass, tipoLabel } from '../../domain/models/notificacion.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-notificaciones-enviadas',
  standalone: true,
  imports: [NgIcon, RouterLink, DecimalPipe, FormsModule, PageTitle],
  templateUrl: './notificaciones-enviadas.html',
})
export class NotificacionesEnviadas implements OnInit {
  private notifSv = inject(NotificacionesService);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  enviadas = signal<ResumenEnviado[]>([]);
  loading  = signal(true);

  filtroTexto        = signal('');
  filtroTipo         = signal<'' | 'comunicado' | 'actividad'>('');
  filtroDestinatario = signal<'' | 'todos' | 'rol' | 'usuario'>('');

  prioridadConfig = prioridadConfig;
  tiempoRelativo  = tiempoRelativo;
  tipoBadgeClass  = tipoBadgeClass;
  tipoLabel       = tipoLabel;

  totalComunicados = computed(() => this.enviadas().length);
  totalAlcanzados  = computed(() => this.enviadas().reduce((sum, e) => sum + e.total_enviadas, 0));
  tasaLecturaPromedio = computed(() => {
    const items = this.enviadas();
    if (items.length === 0) return 0;
    const suma = items.reduce((acc, e) => acc + this.tasaLectura(e), 0);
    return Math.round(suma / items.length);
  });

  enviadasFiltradas = computed(() => {
    const texto = this.filtroTexto().trim().toLowerCase();
    const tipo  = this.filtroTipo();
    const dest  = this.filtroDestinatario();

    return this.enviadas().filter(e => {
      if (texto && !e.titulo.toLowerCase().includes(texto) && !e.mensaje.toLowerCase().includes(texto)) return false;
      if (tipo && e.tipo !== tipo) return false;
      if (dest && e.destinatario !== dest) return false;
      return true;
    });
  });

  hayFiltrosActivos = computed(() => !!(this.filtroTexto() || this.filtroTipo() || this.filtroDestinatario()));

  limpiarFiltros(): void {
    this.filtroTexto.set('');
    this.filtroTipo.set('');
    this.filtroDestinatario.set('');
  }

  ngOnInit() {
    this.notifSv.getEnviados().subscribe({
      next: (res) => {
        this.enviadas.set(res.data);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el historial de enviados.');
        this.loading.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  tasaLectura(item: ResumenEnviado): number {
    if (!item.total_enviadas) return 0;
    return Math.round((item.total_leidas / item.total_enviadas) * 100);
  }
}
