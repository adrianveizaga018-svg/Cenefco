import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { SueldoDocenteService } from '../../application/services/sueldo-docente.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { DocenteOption, ImparticionOption } from '../../domain/models/sueldo-docente.model';

@Component({
  selector: 'app-sueldo-docente-edit',
  imports: [NgIcon, RouterLink, FormsModule, PageTitle],
  templateUrl: './sueldo-docente-edit.html',
})
export class SueldoDocenteEdit implements OnInit {
  private service = inject(SueldoDocenteService);
  private route   = inject(ActivatedRoute);
  private router  = inject(Router);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  docentes      = signal<DocenteOption[]>([]);
  imparticiones = signal<ImparticionOption[]>([]);
  loading       = signal(true);
  saving        = signal(false);
  hasError      = signal(false);

  idSueldo!: number;

  form = {
    id_us:            null as number | null,
    id_imp:           null as number | null,
    concepto:         '',
    periodo:          '',
    gestion:          new Date().getFullYear(),
    monto_total:      null as number | null,
    observacion:      '',
    archivo_pdf_file: null as File | null,
  };

  archivoPdfActual = signal<string | null>(null);
  archivoNombre    = signal<string | null>(null);

  onArchivoChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.form.archivo_pdf_file = file;
    this.archivoNombre.set(file?.name ?? null);
  }

  limpiarArchivo(): void {
    this.form.archivo_pdf_file = null;
    this.archivoNombre.set(null);
  }

  ngOnInit(): void {
    this.idSueldo = Number(this.route.snapshot.paramMap.get('id'));
    let pending = 3;
    const done = () => { if (--pending === 0) { this.loading.set(false); this.cdr.detectChanges(); } };

    this.service.getById(this.idSueldo).subscribe({
      next: s => {
        this.archivoPdfActual.set(s.archivo_pdf ?? null);
        this.form = {
          id_us:            s.id_us,
          id_imp:           s.id_imp,
          concepto:         s.concepto,
          periodo:          s.periodo ?? '',
          gestion:          s.gestion ?? new Date().getFullYear(),
          monto_total:      s.monto_total,
          observacion:      s.observacion ?? '',
          archivo_pdf_file: null,
        };
        done();
      },
      error: () => { this.hasError.set(true); done(); },
    });

    this.service.getDocentes().subscribe({
      next: d => { this.docentes.set(d); done(); },
      error: () => done(),
    });

    this.service.getImparticiones().subscribe({
      next: i => { this.imparticiones.set(i); done(); },
      error: () => done(),
    });
  }

  guardar(): void {
    if (!this.form.id_us || !this.form.concepto.trim() || !this.form.monto_total) {
      this.toast.error('Validación', 'Docente, concepto y monto son obligatorios.');
      return;
    }
    if (this.form.monto_total <= 0) {
      this.toast.error('Validación', 'El monto debe ser mayor a 0.');
      return;
    }
    this.saving.set(true);
    this.service.update(this.idSueldo, this.form as any).subscribe({
      next: () => {
        this.saving.set(false);
        this.toast.success('Actualizado', 'Sueldo actualizado correctamente.');
        this.router.navigate(['/cenefco/sueldos-docentes', this.idSueldo]);
      },
      error: () => {
        this.saving.set(false);
        this.toast.error('Error', 'No se pudo actualizar el sueldo.');
      },
    });
  }

  cancelar(): void {
    this.router.navigate(['/cenefco/sueldos-docentes', this.idSueldo]);
  }
}
