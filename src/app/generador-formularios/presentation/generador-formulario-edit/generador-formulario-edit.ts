import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { PageTitle }   from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { GeneradorFormularioService } from '../../application/services/generador-formulario.service';

@Component({
  selector: 'app-generador-formulario-edit',
  standalone: true,
  imports: [NgIcon, PageTitle, RouterLink, FormsModule],
  templateUrl: './generador-formulario-edit.html',
})
export class GeneradorFormularioEdit implements OnInit {
  private svc    = inject(GeneradorFormularioService);
  private toast  = inject(ToastService);
  private router = inject(Router);
  private route  = inject(ActivatedRoute);

  loading    = signal(true);
  saving     = signal(false);

  nombre      = signal('');
  slug        = signal('');
  descripcion = signal('');
  activo      = signal(true);

  readonly id = computed(() => Number(this.route.snapshot.paramMap.get('id')));

  ngOnInit(): void {
    this.svc.getById(this.id()).subscribe({
      next: (f) => {
        this.nombre.set(f.nombre);
        this.slug.set(f.slug);
        this.descripcion.set(f.descripcion ?? '');
        this.activo.set(f.activo);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el formulario.');
        this.router.navigate(['/cenefco/generador-formularios']);
      },
    });
  }

  autoSlug(nombre: string): string {
    return nombre.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
  }

  onNombreChange(value: string): void {
    this.nombre.set(value);
    this.slug.set(this.autoSlug(value));
  }

  onSubmit(): void {
    if (!this.nombre().trim()) { this.toast.error('Error', 'El nombre es obligatorio.'); return; }
    this.saving.set(true);
    this.svc.update(this.id(), {
      nombre:      this.nombre().trim(),
      slug:        this.slug() || this.autoSlug(this.nombre()),
      descripcion: this.descripcion().trim() || null,
      activo:      this.activo(),
    }).subscribe({
      next: (f) => {
        this.toast.success('Guardado', `Formulario "${f.nombre}" actualizado.`);
        this.router.navigate(['/cenefco/generador-formularios']);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo guardar el formulario.');
        this.saving.set(false);
      },
    });
  }
}
