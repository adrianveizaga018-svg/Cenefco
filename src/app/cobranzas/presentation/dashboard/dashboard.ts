import { Component, inject, signal, OnInit } from '@angular/core';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CobranzaService } from '../../application/services/cobranza.service';
import { Title } from '@angular/platform-browser';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [ReactiveFormsModule, NgIcon, PageTitle],
  templateUrl: './dashboard.html',
})
export class Dashboard implements OnInit {
  private service = inject(CobranzaService);
  private titleService = inject(Title);

  cuotas = signal<any[]>([]);
  metrics = signal<any>(null);
  cargando = signal(false);
  totalRegistros = signal(0);
  paginaActual = signal(1);

  filtroEstado = new FormControl('');
  filtroCi = new FormControl('');

  ngOnInit() {
    this.titleService.setTitle('Dashboard de Cobranzas - CENEFCO');
    this.cargarDashboard();
    this.cargarMetrics();
  }

  
  cargarMetrics() {
    this.service.getMetrics().subscribe({
      next: (res) => this.metrics.set(res),
      error: (err) => console.error(err)
    });
  }

  cargarDashboard(page = 1) {
    this.cargando.set(true);
    this.paginaActual.set(page);
    
    this.service.getDashboard({
      page,
      per_page: 20,
      estado: this.filtroEstado.value,
      ci: this.filtroCi.value?.trim()
    }).subscribe({
      next: (res) => {
        this.cuotas.set(res.data);
        this.totalRegistros.set(res.total);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false)
    });
  }

  aplicarFiltros() {
    this.cargarDashboard(1);
  }

  cambiarPagina(delta: number) {
    const newPage = this.paginaActual() + delta;
    if (newPage > 0) this.cargarDashboard(newPage);
  }

  
  getWhatsAppUrl(alerta: any, tipo: 'vence_pronto' | 'vencido'): string {
    if (!alerta.celular) return '';
    
    // Limpiar el número (quitar espacios o caracteres raros)
    let cel = alerta.celular.replace(/\D/g, '');
    if (!cel.startsWith('591')) cel = '591' + cel; // Asumimos prefijo Bolivia

    let mensaje = '';
    if (tipo === 'vence_pronto') {
      mensaje = `Hola ${alerta.estudiante_nombre}, te saludamos de CENEFCO. Te recordamos que tu cuota de Bs. ${alerta.monto} por "${alerta.descripcion}" vence el ${alerta.fecha_vencimiento}.`;
    } else {
      mensaje = `Hola ${alerta.estudiante_nombre}, te saludamos de CENEFCO. Tu cuota de Bs. ${alerta.monto} por "${alerta.descripcion}" venció hace ${alerta.dias_retraso} días. Por favor, regulariza tu pago lo antes posible.`;
    }

    return `https://wa.me/${cel}?text=${encodeURIComponent(mensaje)}`;
  }

  isVencida(fecha: string): boolean {
    if (!fecha) return false;
    const hoy = new Date();
    hoy.setHours(0,0,0,0);
    const f = new Date(fecha);
    f.setHours(0,0,0,0);
    return f < hoy;
  }
}
