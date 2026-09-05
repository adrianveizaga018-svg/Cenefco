import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { CursoMigradoService } from '../../application/services/curso-migrado.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { generateSlug } from '../../../utils/slug';

const PORTAL_BASE = 'https://cenefco.com';

@Component({
  selector: 'app-curso-migrado-create',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './curso-migrado-create.html',
})
export class CursoMigradoCreate {
  private service = inject(CursoMigradoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);

  submitting        = signal(false);
  participantes     = signal<string[]>([]);
  nuevoParticipante = signal('');
  urlEditada        = signal(false);

  form: FormGroup = this.fb.group({
    nombre:        ['', [Validators.required, Validators.maxLength(300)]],
    url:           ['', [Validators.required, Validators.maxLength(500)]],
    periodo:       ['', [Validators.maxLength(50)]],
    gestion:       ['', [Validators.maxLength(10)]],
    fecha_inicio:  [''],
    carga_horaria: [null as number | null, [Validators.min(1), Validators.max(9999)]],
  });

  constructor() {

    this.form.get('nombre')!.valueChanges.subscribe((nombre: string) => {
      if (!this.urlEditada()) {
        const slug = generateSlug(nombre ?? '');
        const url  = slug ? `${PORTAL_BASE}/${slug}-participantes` : '';
        this.form.get('url')!.setValue(url, { emitEvent: false });
      }
    });

    this.form.get('url')!.valueChanges.subscribe(() => {
      this.urlEditada.set(true);
    });
  }

  resetUrl(): void {
    const slug = generateSlug(this.form.get('nombre')!.value ?? '');
    this.form.get('url')!.setValue(
      slug ? `${PORTAL_BASE}/${slug}-participantes` : '',
      { emitEvent: false }
    );
    this.urlEditada.set(false);
  }

  get urlActual(): string {
    return this.form.get('url')?.value ?? '';
  }

  get qrApiUrl(): string {
    const url = this.urlActual;
    if (!url) return '';
    return `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(url)}`;
  }

  agregarParticipante(): void {
    const nombre = this.nuevoParticipante().trim();
    if (!nombre) return;
    if (this.participantes().some(p => p.toLowerCase() === nombre.toLowerCase())) {
      this.toast.warning('Duplicado', 'Ese participante ya está en la lista.');
      return;
    }
    this.participantes.update(list => [...list, nombre]);
    this.nuevoParticipante.set('');
  }

  quitarParticipante(index: number): void {
    this.participantes.update(list => list.filter((_, i) => i !== index));
  }

  onParticipanteKeydown(event: KeyboardEvent): void {
    if (event.key === 'Enter') {
      event.preventDefault();
      this.agregarParticipante();
    }
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }

    this.submitting.set(true);

    const raw = this.form.value;
    const payload = {
      nombre:        raw.nombre,
      url:           raw.url,
      periodo:       raw.periodo      || null,
      gestion:       raw.gestion      || null,
      fecha_inicio:  raw.fecha_inicio || null,
      carga_horaria: raw.carga_horaria ?? null,
    };

    this.service.create(payload).subscribe({
      next: async (curso) => {
        const lista = this.participantes();
        if (lista.length > 0) {
          await Promise.all(
            lista.map(n => this.service.addParticipante(curso.id, n).toPromise().catch(() => null))
          );
        }
        this.toast.success('¡Registrado!', `"${curso.nombre}" fue guardado correctamente.`);
        this.router.navigate(['/cenefco/cursos-migrados']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo registrar el curso.'));
        this.submitting.set(false);
      },
    });
  }
}
