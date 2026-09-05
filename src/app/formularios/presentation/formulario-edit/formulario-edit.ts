import { Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { CdkDragDrop, DragDropModule, moveItemInArray } from '@angular/cdk/drag-drop';
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
  selector: 'app-formulario-edit',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule, RouterLink, NgIcon, PageTitle, DragDropModule],
  templateUrl: './formulario-edit.html',
})
export class FormularioEdit implements OnInit {
  private svc    = inject(FormularioService);
  private toast  = inject(ToastService);
  private router = inject(Router);
  private route  = inject(ActivatedRoute);
  private fb     = inject(FormBuilder);

  readonly tiposCampo = TIPOS_CAMPO;

  loading    = signal(true);
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

  private formularioId!: number;

  ngOnInit(): void {
    this.formularioId = Number(this.route.snapshot.paramMap.get('id'));
    this.svc.getById(this.formularioId).subscribe({
      next: (f) => {
        this.form.patchValue({
          nombre:      f.nombre,
          slug:        f.slug,
          descripcion: f.descripcion ?? '',
          activo:      f.activo,
        });
        this.campos.set(f.campos ?? []);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el formulario.');
        this.router.navigate(['/cenefco/formularios']);
      },
    });
  }

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

  /**
   * Campo que se está editando actualmente (referencia exacta al objeto, no
   * su índice): así, si se reordena por drag-and-drop o se borra otro campo
   * mientras el formulario de edición está abierto, guardarCampo() sigue
   * ubicando el campo correcto en vez de sobreescribir uno equivocado.
   */
  editandoCampo = signal<CampoFormulario | null>(null);
  get modoEdicion(): boolean { return this.editandoCampo() !== null; }

  editarCampo(campo: CampoFormulario): void {
    this.editandoCampo.set(campo);
    const v = campo.validacion ?? {};
    this.campoForm.patchValue({
      etiqueta:      campo.etiqueta,
      nombre_campo:  campo.nombre_campo,
      tipo:          campo.tipo,
      requerido:     campo.requerido,
      placeholder:   campo.placeholder ?? '',
      ayuda:         campo.ayuda ?? '',
      min:           v.min ?? null,
      max:           v.max ?? null,
      min_length:    v.min_length ?? null,
      max_length:    v.max_length ?? null,
      patron:        v.patron ?? '',
      tamano_max_mb: v.tamano_max_mb ?? null,
    });
  }

  cancelarEdicionCampo(): void {
    this.editandoCampo.set(null);
    this.campoForm.reset({ tipo: 'text', requerido: false });
  }

  guardarCampo(): void {
    if (this.campoForm.invalid) { this.campoForm.markAllAsTouched(); return; }
    const v = this.campoForm.getRawValue();
    const datos = {
      nombre_campo: v.nombre_campo!,
      etiqueta:     v.etiqueta!,
      tipo:         v.tipo as TipoCampo,
      requerido:    !!v.requerido,
      placeholder:  v.placeholder || undefined,
      ayuda:        v.ayuda || undefined,
      validacion:   this.construirValidacion(v),
    };

    const original = this.editandoCampo();
    if (original) {
      this.campos.update(list => {
        const idx = list.indexOf(original);
        if (idx === -1) return list;
        const arr = [...list];
        arr[idx] = { ...datos, opciones: original.opciones ?? [] };
        return arr;
      });
      this.editandoCampo.set(null);
    } else {
      this.campos.update(list => [...list, { ...datos, opciones: [] }]);
    }
    this.campoForm.reset({ tipo: 'text', requerido: false });
  }

  eliminarCampo(i: number): void {
    this.campos.update(list => list.filter((_, idx) => idx !== i));
    delete this.opcionesTemp[i];
  }

  moverArriba(i: number): void {
    if (i === 0) return;
    this.campos.update(list => {
      const arr = [...list]; [arr[i - 1], arr[i]] = [arr[i], arr[i - 1]]; return arr;
    });
  }

  moverAbajo(i: number): void {
    this.campos.update(list => {
      if (i >= list.length - 1) return list;
      const arr = [...list]; [arr[i], arr[i + 1]] = [arr[i + 1], arr[i]]; return arr;
    });
  }

  onDropCampo(event: CdkDragDrop<CampoFormulario[]>): void {
    this.campos.update(list => {
      const arr = [...list];
      moveItemInArray(arr, event.previousIndex, event.currentIndex);
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

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    const raw = this.form.getRawValue();
    this.svc.update(this.formularioId, {
      nombre:      raw.nombre!,
      slug:        raw.slug || null,
      descripcion: raw.descripcion || null,
      campos:      this.campos(),
      activo:      !!raw.activo,
    }).subscribe({
      next: (f) => {
        this.toast.success('Guardado', `Formulario "${f.nombre}" actualizado.`);
        this.router.navigate(['/cenefco/formularios']);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo guardar el formulario.');
        this.submitting.set(false);
      },
    });
  }
}
