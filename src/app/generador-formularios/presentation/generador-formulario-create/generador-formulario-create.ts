import { Component, inject, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { PageTitle }   from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { GeneradorFormularioService } from '../../application/services/generador-formulario.service';
import {
  CampoFormulario, TipoCampo,
  TIPOS_CAMPO, NOMBRES_SUGERIDOS,
} from '../../domain/models/generador-formulario.model';

@Component({
  selector: 'app-generador-formulario-create',
  standalone: true,
  imports: [NgIcon, PageTitle, RouterLink, FormsModule],
  templateUrl: './generador-formulario-create.html',
})
export class GeneradorFormularioCreate {
  private svc    = inject(GeneradorFormularioService);
  private toast  = inject(ToastService);
  private router = inject(Router);

  readonly tiposCampo     = TIPOS_CAMPO;
  readonly sugeridos      = NOMBRES_SUGERIDOS;

  nombre      = signal('');
  descripcion = signal('');
  activo      = signal(true);

  campos = signal<CampoFormulario[]>([]);

  nuevoCampo: CampoFormulario = this.campoVacio();
  nuevaOpcion = '';

  saving   = signal(false);
  showForm = signal(false);

  private campoVacio(): CampoFormulario {
    return { nombre_campo: '', etiqueta: '', tipo: 'text', requerido: false, opciones: [], placeholder: '', ayuda: '' };
  }

  usarSugerido(s: typeof NOMBRES_SUGERIDOS[0]): void {
    this.nuevoCampo = { ...this.campoVacio(), nombre_campo: s.nombre_campo, etiqueta: s.etiqueta, tipo: s.tipo };
    if (s.tipo === 'select') this.nuevoCampo.opciones = [];
    this.showForm.set(true);
  }

  agregarOpcion(): void {
    const op = this.nuevaOpcion.trim();
    if (!op) return;
    if (!this.nuevoCampo.opciones) this.nuevoCampo.opciones = [];
    this.nuevoCampo.opciones.push(op);
    this.nuevaOpcion = '';
  }

  quitarOpcion(i: number): void {
    this.nuevoCampo.opciones?.splice(i, 1);
  }

  agregarCampo(): void {
    if (!this.nuevoCampo.nombre_campo.trim() || !this.nuevoCampo.etiqueta.trim()) {
      this.toast.error('Error', 'El nombre de campo y la etiqueta son obligatorios.');
      return;
    }
    const yaExiste = this.campos().some(c => c.nombre_campo === this.nuevoCampo.nombre_campo);
    if (yaExiste) {
      this.toast.error('Error', `Ya existe un campo con el nombre "${this.nuevoCampo.nombre_campo}".`);
      return;
    }
    const camp: CampoFormulario = { ...this.nuevoCampo, opciones: [...(this.nuevoCampo.opciones ?? [])] };
    this.campos.update(list => [...list, camp]);
    this.nuevoCampo = this.campoVacio();
    this.nuevaOpcion = '';
    this.showForm.set(false);
  }

  eliminarCampo(i: number): void {
    this.campos.update(list => list.filter((_, idx) => idx !== i));
  }

  moverArriba(i: number): void {
    if (i === 0) return;
    this.campos.update(list => {
      const arr = [...list];
      [arr[i - 1], arr[i]] = [arr[i], arr[i - 1]];
      return arr;
    });
  }

  moverAbajo(i: number): void {
    this.campos.update(list => {
      if (i >= list.length - 1) return list;
      const arr = [...list];
      [arr[i], arr[i + 1]] = [arr[i + 1], arr[i]];
      return arr;
    });
  }

  autoSlug(nombre: string): string {
    return nombre.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
  }

  onSubmit(): void {
    if (!this.nombre().trim()) { this.toast.error('Error', 'El nombre del formulario es obligatorio.'); return; }
    this.saving.set(true);
    this.svc.create({
      nombre:      this.nombre().trim(),
      slug:        this.autoSlug(this.nombre()),
      descripcion: this.descripcion().trim() || null,
      campos:      this.campos(),
      activo:      this.activo(),
    }).subscribe({
      next: (f) => {
        this.toast.success('Creado', `Formulario "${f.nombre}" creado.`);
        this.router.navigate(['/cenefco/generador-formulario-campos', f.id]);
      },
      error: () => { this.toast.error('Error', 'No se pudo crear el formulario.'); this.saving.set(false); },
    });
  }

  labelTipo(tipo: TipoCampo): string {
    return this.tiposCampo.find(t => t.value === tipo)?.label ?? tipo;
  }
}
