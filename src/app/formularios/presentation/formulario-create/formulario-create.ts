import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { FormularioService } from '../../application/services/formulario.service';
import { CampoFormulario, TipoCampo, ValidacionCampo } from '../../domain/models/formulario.model';

const TIPOS_CAMPO: { value: TipoCampo; label: string }[] = [
  { value: 'text',     label: 'Texto corto' },
  { value: 'email',    label: 'Email' },
  { value: 'tel',      label: 'Teléfono' },
  { value: 'number',   label: 'Número' },
  { value: 'date',     label: 'Fecha' },
  { value: 'textarea', label: 'Texto largo' },
  { value: 'select',   label: 'Selección (lista)' },
  { value: 'file',     label: 'Archivo / Documento' },
  { value: 'checkbox', label: 'Casilla (sí/no)' },
];

@Component({
  selector: 'app-formulario-create',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './formulario-create.html',
})
export class FormularioCreate {
  private svc    = inject(FormularioService);
  private toast  = inject(ToastService);
  private router = inject(Router);
  private fb     = inject(FormBuilder);

  readonly tiposCampo = TIPOS_CAMPO;

  submitting = signal(false);
  campos     = signal<CampoFormulario[]>([]);
  opcionesTemp: Record<number, string> = {};

  form = this.fb.group({
    nombre:      ['', [Validators.required, Validators.maxLength(200)]],
    slug:        ['', Validators.maxLength(250)],
    descripcion: [''],
    activo:      [true],
  });

  campoForm = this.fb.group({
    etiqueta:    ['', [Validators.required, Validators.maxLength(200)]],
    nombre_campo:['', [Validators.required, Validators.maxLength(100)]],
    tipo:        ['text' as TipoCampo, Validators.required],
    requerido:   [false],
    placeholder: [''],
    ayuda:       [''],
    min:           [null as number | null],
    max:           [null as number | null],
    min_length:    [null as number | null],
    max_length:    [null as number | null],
    patron:        [''],
    tamano_max_mb: [null as number | null],
  });

  get tipoActual(): TipoCampo {
    return (this.campoForm.get('tipo')?.value ?? 'text') as TipoCampo;
  }
  get muestraRangoNumerico(): boolean { return this.tipoActual === 'number' || this.tipoActual === 'date'; }
  get muestraLongitud(): boolean { return ['text', 'textarea', 'tel'].includes(this.tipoActual); }
  get muestraPatron(): boolean { return ['text', 'tel'].includes(this.tipoActual); }
  get muestraArchivo(): boolean { return this.tipoActual === 'file'; }

  private construirValidacion(v: ReturnType<typeof this.campoForm.getRawValue>): ValidacionCampo | null {
    const validacion: ValidacionCampo = {};
    if (v.min != null) validacion.min = v.min;
    if (v.max != null) validacion.max = v.max;
    if (v.min_length != null) validacion.min_length = v.min_length;
    if (v.max_length != null) validacion.max_length = v.max_length;
    if (v.patron) validacion.patron = v.patron;
    if (v.tamano_max_mb != null) validacion.tamano_max_mb = v.tamano_max_mb;
    return Object.keys(validacion).length ? validacion : null;
  }

  agregarCampo(): void {
    if (this.campoForm.invalid) { this.campoForm.markAllAsTouched(); return; }
    const v = this.campoForm.getRawValue();
    this.campos.update(list => [...list, {
      nombre_campo: v.nombre_campo!,
      etiqueta:     v.etiqueta!,
      tipo:         v.tipo as TipoCampo,
      requerido:    !!v.requerido,
      placeholder:  v.placeholder || undefined,
      ayuda:        v.ayuda || undefined,
      opciones:     [],
      validacion:   this.construirValidacion(v),
    }]);
    this.campoForm.reset({ tipo: 'text', requerido: false });
  }

  eliminarCampo(i: number): void {
    this.campos.update(list => list.filter((_, idx) => idx !== i));
    delete this.opcionesTemp[i];
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

  agregarOpcion(i: number): void {
    const val = (this.opcionesTemp[i] ?? '').trim();
    if (!val) return;
    this.campos.update(list => {
      const arr = [...list];
      arr[i] = { ...arr[i], opciones: [...(arr[i].opciones ?? []), val] };
      return arr;
    });
    this.opcionesTemp[i] = '';
  }

  eliminarOpcion(campoIdx: number, opcionIdx: number): void {
    this.campos.update(list => {
      const arr = [...list];
      const opciones = [...(arr[campoIdx].opciones ?? [])];
      opciones.splice(opcionIdx, 1);
      arr[campoIdx] = { ...arr[campoIdx], opciones };
      return arr;
    });
  }

  autoSlug(): void {
    const nombre = this.form.get('nombre')?.value ?? '';
    const slug = nombre.toLowerCase()
      .normalize('NFD').replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-');
    this.form.get('slug')?.setValue(slug);
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    const raw = this.form.getRawValue();
    this.svc.create({
      nombre:      raw.nombre!,
      slug:        raw.slug || null,
      descripcion: raw.descripcion || null,
      campos:      this.campos(),
      activo:      !!raw.activo,
    }).subscribe({
      next: (f) => {
        this.toast.success('Creado', `Formulario "${f.nombre}" creado correctamente.`);
        this.router.navigate(['/cenefco/formularios']);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo crear el formulario.');
        this.submitting.set(false);
      },
    });
  }
}
