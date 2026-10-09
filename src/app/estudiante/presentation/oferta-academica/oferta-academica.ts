import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { DatePipe, DecimalPipe } from '@angular/common';
import { Router } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { extractErrorMessage } from '../../../utils/http-error';
import { EstudianteService, ProgramaCatalogo } from '../../application/services/estudiante.service';
import { ComprobantePagoComponent } from '../comprobante-pago/comprobante-pago';

/**
 * Catálogo del estudiante: programas abiertos en los que aún no está inscrito.
 * Los gratuitos se toman con un clic; en los de pago elige plan y sube el comprobante del primer pago.
 */
@Component({
  selector: 'app-oferta-academica',
  standalone: true,
  imports: [NgIcon, PageTitle, DatePipe, DecimalPipe, ComprobantePagoComponent],
  templateUrl: './oferta-academica.html',
})
export class OfertaAcademicaComponent implements OnInit {
  private service = inject(EstudianteService);
  private router  = inject(Router);

  programas = signal<ProgramaCatalogo[]>([]);
  loading   = signal(true);
  error     = signal('');
  soloGratis = signal(false);
  inscribiendo = signal<number | null>(null);
  /** Programa de pago cuyo diálogo de inscripción está abierto. */
  programaPago = signal<ProgramaCatalogo | null>(null);

  readonly visibles = computed(() => this.soloGratis() ? this.programas().filter(p => p.gratuito) : this.programas());
  readonly hayGratis = computed(() => this.programas().some(p => p.gratuito));

  ngOnInit(): void {
    this.cargar();
  }

  cargar(): void {
    this.loading.set(true);
    this.error.set('');
    this.service.getCatalogo().subscribe({
      next: (res) => { this.programas.set(res.data); this.loading.set(false); },
      error: () => { this.error.set('No se pudo cargar el catálogo de programas.'); this.loading.set(false); },
    });
  }

  async inscribirGratis(p: ProgramaCatalogo): Promise<void> {
    const plan = p.planes.find(pl => pl.gratuito);
    if (!plan) return;

    const r = await Swal.fire({
      icon: 'question',
      title: 'Inscribirme gratis',
      html: `<p class="text-sm text-gray-700">Te inscribirás en <strong>${this.escapar(p.nombre)}</strong>.<br>No tiene costo.</p>`,
      showCancelButton: true,
      confirmButtonText: 'Sí, inscribirme',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#16a34a',
    });
    if (!r.isConfirmed) return;

    this.inscribiendo.set(p.id_imp);
    this.service.inscribirseGratis(p.id_imp, plan.id_plan).subscribe({
      next: async (res) => {
        this.inscribiendo.set(null);
        this.programas.update(lista => lista.filter(x => x.id_imp !== p.id_imp));
        const fin = await Swal.fire({
          icon: 'success',
          title: '¡Listo, ya estás inscrito!',
          text: res.message,
          showCancelButton: true,
          confirmButtonText: 'Ir a Mis Cursos',
          cancelButtonText: 'Seguir viendo',
          confirmButtonColor: '#2563eb',
        });
        if (fin.isConfirmed) this.router.navigate(['/estudiante/mis-cursos']);
      },
      error: (err: HttpErrorResponse) => {
        this.inscribiendo.set(null);
        Swal.fire('No se pudo inscribir', extractErrorMessage(err, 'Inténtalo de nuevo.'), 'warning');
      },
    });
  }

  async inscritoConPago(p: ProgramaCatalogo, mensaje: string): Promise<void> {
    this.programaPago.set(null);
    this.programas.update(lista => lista.filter(x => x.id_imp !== p.id_imp));
    const fin = await Swal.fire({
      icon: 'success',
      title: '¡Inscripción enviada!',
      text: mensaje,
      showCancelButton: true,
      confirmButtonText: 'Ver Mis Pagos',
      cancelButtonText: 'Seguir viendo',
      confirmButtonColor: '#2563eb',
    });
    if (fin.isConfirmed) this.router.navigate(['/estudiante/mis-pagos']);
  }

  private escapar(texto: string): string {
    return texto.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]!));
  }
}
