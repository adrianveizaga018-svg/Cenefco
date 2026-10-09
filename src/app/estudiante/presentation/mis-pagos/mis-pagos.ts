import { Component, inject, signal, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { EstudianteService, PlanPagoEstudiante, CuotaEstudiante } from '../../application/services/estudiante.service';
import { ComprobantePagoComponent } from '../comprobante-pago/comprobante-pago';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-mis-pagos-estudiante',
  standalone: true,
  imports: [CommonModule, NgIcon, PageTitle, ComprobantePagoComponent],
  templateUrl: './mis-pagos.html',
  styles: ``
})
export class MisPagosEstudianteComponent implements OnInit {
  private service = inject(EstudianteService);
  private route   = inject(ActivatedRoute);
  private router  = inject(Router);

  /** Preinscripción del portal (sin plan) cuyo diálogo "elegir plan y pagar" está abierto. */
  planElegir = signal<PlanPagoEstudiante | null>(null);
  /** ?pagar=<id_ins>: llega desde el portal después de llenar el formulario de inscripción. */
  private pagarAlCargar = Number(this.route.snapshot.queryParamMap.get('pagar')) || null;
  planes = signal<PlanPagoEstudiante[]>([]);
  cargando = signal(true);
  error = signal('');
  
  planActivo = signal<number | null>(null);

  // Estado para el modal de pago
  showComprobanteModal = signal(false);
  cuotaSeleccionada = signal<{ plan: PlanPagoEstudiante, cuota: CuotaEstudiante } | null>(null);

  ngOnInit(): void {
    this.cargarPagos();
  }

  cargarPagos() {
    this.cargando.set(true);
    this.service.getMisPagos().subscribe({
      next: res => {
        this.planes.set(res.data);
        if (res.data.length > 0) {
          this.planActivo.set(res.data[0].id_ins); // Expandir el primero por defecto
        }
        this.cargando.set(false);
        this.abrirPagoPendienteDeUrl();
      },
      error: () => {
        this.error.set('No se pudo cargar la información de pagos.');
        this.cargando.set(false);
      }
    });
  }

  private abrirPagoPendienteDeUrl(): void {
    const id = this.pagarAlCargar;
    if (!id) return;
    this.pagarAlCargar = null;
    this.router.navigate([], { queryParams: {}, replaceUrl: true });
    const plan = this.planes().find(p => p.id_ins === id);
    if (!plan) return;
    this.planActivo.set(id);
    if (plan.requiere_plan) { this.planElegir.set(plan); return; }
    const cuota = plan.cuotas.find(c => !c.pagado);
    if (cuota) this.abrirPago(plan, cuota);
  }

  togglePlan(id_ins: number) {
    if (this.planActivo() === id_ins) {
      this.planActivo.set(null);
    } else {
      this.planActivo.set(id_ins);
    }
  }

  getPorcentajePago(plan: PlanPagoEstudiante): number {
    if (plan.total_plan === 0) return 100;
    // Lo cubierto según el saldo: un anticipo o crédito transferido cuenta aunque no sea "pago de cuota"
    return Math.max(0, Math.min(100, Math.round(((plan.total_plan - plan.saldo) / plan.total_plan) * 100)));
  }

  getEstadoCuota(cuota: CuotaEstudiante): 'pagado' | 'vencido' | 'pendiente' {
    if (cuota.pagado) return 'pagado';
    if (!cuota.fecha_limite) return 'pendiente';
    
    const hoy = new Date();
    hoy.setHours(0,0,0,0);
    const limite = new Date(cuota.fecha_limite);
    
    if (limite < hoy) return 'vencido';
    return 'pendiente';
  }

  abrirPago(plan: PlanPagoEstudiante, cuota: CuotaEstudiante) {
    this.cuotaSeleccionada.set({ plan, cuota });
    this.showComprobanteModal.set(true);
  }

  cerrarPago() {
    this.showComprobanteModal.set(false);
    this.cuotaSeleccionada.set(null);
  }

  comprobanteEnviado(mensaje: string) {
    this.cerrarPago();
    this.planElegir.set(null);
    Swal.fire({ icon: 'success', title: 'Comprobante enviado', text: mensaje, confirmButtonColor: '#16a34a' });
    this.cargarPagos();
  }
}
