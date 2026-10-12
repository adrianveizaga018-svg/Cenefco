import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { DatePipe, DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { InformeCampana, RegistroDiario, ResumenCampana, SeguimientoCampanaService } from '../../application/services/seguimiento-campana.service';

/**
 * Seguimiento de una campaña por marketing: lo que Facebook no sabe (crecimiento del grupo,
 * flyers, otros gastos) se registra a mano cada día, y el informe del periodo sale en un clic.
 */
@Component({
  selector: 'app-campana-seguimiento',
  standalone: true,
  imports: [PageTitle, NgIcon, FormsModule, DatePipe, DecimalPipe, RouterLink],
  templateUrl: './campana-seguimiento.html',
})
export class CampanaSeguimientoComponent implements OnInit {
  private svc   = inject(SeguimientoCampanaService);
  private toast = inject(ToastService);
  private auth  = inject(AuthService);
  private id    = Number(inject(ActivatedRoute).snapshot.paramMap.get('id'));

  readonly puedeEditar = this.auth.hasPermission('campanas.editar');
  readonly hoy = new Date().toISOString().slice(0, 10);

  cargando  = signal(true);
  guardando = signal(false);
  generando = signal(false);
  registros = signal<RegistroDiario[]>([]);
  resumen   = signal<ResumenCampana | null>(null);
  informes  = signal<InformeCampana[]>([]);

  // Formulario del día
  fecha  = signal(this.hoy);
  nuevos = signal<number | null>(null);
  total  = signal<number | null>(null);
  gasto  = signal<number | null>(null);
  nota   = signal('');
  flyer  = signal<File | null>(null);

  // Periodo del resumen e informe
  desde = signal('');
  hasta = signal(this.hoy);

  /** Barras del crecimiento relativas al día con más ingresos. */
  readonly maxNuevos = computed(() => Math.max(1, ...this.registros().map(r => r.nuevos_grupo)));
  readonly ultimoTotal = computed(() => this.registros()[0]?.total_grupo ?? 0);

  ngOnInit(): void { this.cargar(true); }

  cargar(primeraVez = false): void {
    this.cargando.set(true);
    this.svc.ver(this.id, primeraVez ? undefined : this.desde(), primeraVez ? undefined : this.hasta()).subscribe({
      next: (res) => {
        this.registros.set(res.registros);
        this.resumen.set(res.resumen);
        this.informes.set(res.informes);
        if (primeraVez) { this.desde.set(res.resumen.periodo.desde); this.hasta.set(res.resumen.periodo.hasta); }
        this.cargando.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.cargando.set(false);
        this.toast.error('No se pudo cargar la campaña', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  onFlyer(ev: Event): void {
    const f = (ev.target as HTMLInputElement).files?.[0] ?? null;
    if (f && f.size > 5 * 1024 * 1024) { this.toast.warning('Imagen muy pesada', 'El flyer debe pesar menos de 5 MB.'); return; }
    this.flyer.set(f);
  }

  /** Al cambiar "se unieron hoy" se sugiere el total sumando al último registrado. */
  sugerirTotal(): number | null {
    const n = this.nuevos();
    return n === null ? null : this.ultimoTotal() + n;
  }

  guardar(): void {
    if (this.nuevos() === null || this.nuevos()! < 0) { this.toast.warning('Falta un dato', 'Escribe cuántos se unieron al grupo (0 si nadie).'); return; }
    const fd = new FormData();
    fd.append('fecha', this.fecha());
    fd.append('nuevos_grupo', String(this.nuevos()));
    if (this.total() !== null) fd.append('total_grupo', String(this.total()));
    if (this.gasto()) fd.append('gasto_extra', String(this.gasto()));
    if (this.nota().trim()) fd.append('nota', this.nota().trim());
    if (this.flyer()) fd.append('flyer', this.flyer()!);

    this.guardando.set(true);
    this.svc.guardar(this.id, fd).subscribe({
      next: () => {
        this.guardando.set(false);
        this.toast.success('Guardado', 'Registro del ' + this.fecha().split('-').reverse().join('/'));
        this.nuevos.set(null); this.total.set(null); this.gasto.set(null); this.nota.set(''); this.flyer.set(null);
        this.cargar();
      },
      error: (err: HttpErrorResponse) => {
        this.guardando.set(false);
        this.toast.error('No se pudo guardar', extractErrorMessage(err, 'Revisa los datos.'));
      },
    });
  }

  editar(r: RegistroDiario): void {
    this.fecha.set(r.fecha.slice(0, 10)); this.nuevos.set(r.nuevos_grupo); this.total.set(r.total_grupo);
    this.gasto.set(r.gasto_extra || null); this.nota.set(r.nota ?? ''); this.flyer.set(null);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  async eliminar(r: RegistroDiario): Promise<void> {
    const ok = await Swal.fire({ icon: 'warning', title: '¿Eliminar el registro del ' + r.fecha.slice(0, 10).split('-').reverse().join('/') + '?',
      showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', confirmButtonColor: '#dc2626' });
    if (!ok.isConfirmed) return;
    this.svc.eliminar(r.id).subscribe({
      next: () => { this.toast.success('Eliminado', ''); this.cargar(); },
      error: (err: HttpErrorResponse) => this.toast.error('No se pudo eliminar', extractErrorMessage(err, 'Inténtalo de nuevo.')),
    });
  }

  generarInforme(): void {
    this.generando.set(true);
    this.svc.generarInforme(this.id, this.desde(), this.hasta()).subscribe({
      next: (res) => {
        this.generando.set(false);
        this.informes.update(l => [res.informe, ...l]);
        this.toast.success('Informe listo', 'Quedó guardado en el historial.');
        window.open(res.informe.archivo_url, '_blank');
      },
      error: (err: HttpErrorResponse) => {
        this.generando.set(false);
        this.toast.error('No se pudo generar', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }
}
