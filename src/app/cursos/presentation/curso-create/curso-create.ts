import { Component, inject, signal, computed } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators, ValidatorFn, AbstractControl, ValidationErrors } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { CursoService } from '../../application/services/curso.service';
import { CategoriaCurso } from '../../domain/models/curso.model';
import { FormularioService } from '../../../formularios/application/services/formulario.service';
import { Formulario } from '../../../formularios/domain/models/formulario.model';
import { ConvenioService } from '../../../convenios/application/services/convenio.service';
import { ConvenioOption } from '../../../convenios/domain/models/convenio.model';
import { VendedorService } from '../../../vendedores/application/services/vendedor.service';
import { Vendedor } from '../../../vendedores/domain/models/vendedor.model';
import { AreaService } from '../../../areas/application/services/area.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { generateSlug } from '../../../utils/slug';
import { CursoImagenes } from '../../../common/components/curso-imagenes/curso-imagenes';
import { CKEditorModule } from '@ckeditor/ckeditor5-angular';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

interface Imparticion { id_imp: number; periodo: string | null; gestion: string | null; materia_nombre: string | null; paralelo: string | null; id_mat: number | null; docente_nombre: string | null; }

export const fechasValidator: ValidatorFn = (control: AbstractControl): ValidationErrors | null => {
  const inicioInsc = control.get('inicio_inscripciones')?.value;
  const inicioAct = control.get('inicio_actividades')?.value;
  const finAct = control.get('finalizacion_actividades')?.value;
  if (!inicioInsc && !inicioAct && !finAct) return null;
  let errors: any = {};
  if (inicioInsc && inicioAct && new Date(inicioInsc) > new Date(inicioAct)) {
    errors.inscripcionTardia = true;
  }
  if (inicioAct && finAct && new Date(inicioAct) > new Date(finAct)) {
    errors.finTemprano = true;
  }
  return Object.keys(errors).length > 0 ? errors : null;
};

@Component({
  selector: 'app-curso-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle, CursoImagenes, CKEditorModule],
  templateUrl: './curso-create.html',
  styles: ``
})
export class CursoCreate {
  private cursoService       = inject(CursoService);
  private formularioService  = inject(FormularioService);
  private convenioService    = inject(ConvenioService);
  private vendedorService    = inject(VendedorService);
  private areaService        = inject(AreaService);
  private toast              = inject(ToastService);
  private router             = inject(Router);
  private fb                 = inject(FormBuilder);
  private fileUpload         = inject(FileUploadService);
  private http               = inject(HttpClient);

  submitting    = signal(false);
  Editor        = ClassicEditor as any;
  private ckEditors: Record<string, any> = {};
  onEditorReady(editor: any, campo: string) { this.ckEditors[campo] = editor; }
  categorias    = signal<CategoriaCurso[]>([]);
  convenios     = signal<ConvenioOption[]>([]);
  vendedores    = signal<Vendedor[]>([]);
  areas         = signal<{ id: number; titulo: string }[]>([]);
  imparticiones = signal<Imparticion[]>([]);
  formularios   = signal<Formulario[]>([]);
  uploadingImg  = signal(false);
  imgPreview    = signal<string | null>(null);
  uploadingPdf  = signal(false);
  pdfName       = signal<string | null>(null);
  catalogoTareas = signal<any[]>([]);
  todosLosPlanes   = signal<any[]>([]);
  planesSeleccionados = signal<number[]>([]);
  busquedaPlanes = signal('');
  planesFiltrados = computed(() => this.todosLosPlanes().filter(p => p.titulo.toLowerCase().includes(this.busquedaPlanes().toLowerCase())));

  form: FormGroup = this.fb.group({
    nombre_programa:          ['', [Validators.required, Validators.maxLength(200)]],
    slug:                     ['', [Validators.maxLength(300)]],
    descripcion:              [''],
    objetivo:                 [''],
    dirigido:                 [''],
    requisitos:               [''],
    costo_monto:              [null as number | null],
    creditaje:                ['', [Validators.pattern(/^\d+$/)]],
    foto:                     [''],
    titulo_documento1:        [''],
    documento1:               [''],
    imagen_banner_url:        [''],
    imagen_alt:               [''],
    url_video:                [''],
    url_whatsapp:             [''],
    url_whatsapp2:            [''],
    imagenes:                 [[] as string[]],
    inicio_actividades:       [''],
    finalizacion_actividades: [''],
    inicio_inscripciones:     [''],
    mes_facturacion:          [''],
    tipo_honorario:           [null as string | null],
    id_imp:                   [null as number | null],
    convenio_id:              [null as number | null],
    vendedor_id:              [null as number | null],
    categoria_web_id:         [null],
    formulario_id:            [null as number | null],
    area_id:                  [null as number | null],
    estado_web:               ['borrador'],
    destacado:                [false],
    orden:                    [0],
    tareas_catalogo_ids:      [[] as number[]],
  }, { validators: fechasValidator });

  constructor() {
    this.cursoService.getCategorias().subscribe({ next: r => this.categorias.set(r.data) });
    this.convenioService.getAll$().subscribe({ next: r => this.convenios.set(r), error: () => {} });
    this.vendedorService.getAll({ pageSize: 200 }).subscribe({ next: r => this.vendedores.set(r.data.filter(v => v.usuario_id != null)), error: () => {} });
    this.areaService.getAll({ pageSize: 100 }).subscribe({ next: r => this.areas.set(r.data), error: () => {} });
    this.formularioService.getActivos().subscribe({ next: r => this.formularios.set(r), error: () => {} });
    this.http.get<{ data: Imparticion[] }>('/api/v1/imparticiones', {
      params: { pageSize: '200', pageIndex: '1', conInactivos: 'true' }
    }).subscribe({ next: r => this.imparticiones.set(r.data) });

    this.http.get<any>('/api/v1/planes-academicos?pageSize=200').subscribe({
      next: (res: any) => {
        const lista = res.data ?? res ?? [];
        this.todosLosPlanes.set(lista.filter((p: any) => p.estado == 1 || p.estado === "activo" || p.estado === true));
      },
      error: () => {}
    });

    this.http.get<any[]>('/api/v1/catalogo-tareas').subscribe({
      next: (data) => {
        const activos = data.filter(d => d.estado);
        this.catalogoTareas.set(activos);
      }
    });

    this.form.get('nombre_programa')!.valueChanges.subscribe((nombre: string) => {
      this.form.get('slug')!.setValue(generateSlug(nombre ?? ''), { emitEvent: false });
    });
  }

  onImgSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:   this.imgPreview,
      uploading: this.uploadingImg,
      onSuccess: (url) => this.form.patchValue({ foto: url }),
      fallbackMsg: 'No se pudo subir la imagen',
    });
  }

  removeImg(): void {
    this.imgPreview.set(null);
    this.form.patchValue({ foto: '' });
  }

  onPdfSelected(event: Event): void {
    this.fileUpload.handleFileSelect(event, {
      uploading:   this.uploadingPdf,
      fileName:    this.pdfName,
      onSuccess:   (url) => this.form.patchValue({ documento1: url }),
      fallbackMsg: 'No se pudo subir el documento',
    });
  }

  removePdf(): void {
    this.pdfName.set(null);
    this.form.patchValue({ documento1: '' });
  }

  imparticionLabel(imp: Imparticion): string {
    const mat = imp.materia_nombre ?? `ID ${imp.id_mat}`;
    const doc = imp.docente_nombre?.trim() || '';
    return `[${imp.periodo}] ${mat}${doc ? ' — ' + doc : ''}`;
  }

  togglePlan(planId: number) {
    const current = this.planesSeleccionados();
    if (current.includes(planId)) {
      this.planesSeleccionados.set(current.filter(id => id !== planId));
    } else {
      this.planesSeleccionados.set([...current, planId]);
    }
  }

  onSubmit(): void {
    this.form.markAllAsTouched();
    if (this.form.errors?.['inscripcionTardia']) {
      this.toast.warning('Verifica las fechas', 'El inicio de inscripciones debe ser antes del inicio de actividades.');
      return;
    }
    if (this.form.errors?.['finTemprano']) {
      this.toast.warning('Verifica las fechas', 'La finalización debe ser posterior al inicio de actividades.');
      return;
    }
    if (this.form.invalid) {
      this.toast.warning('Revisa el formulario', 'Completa los campos obligatorios antes de continuar.');
      return;
    }
    if (this.uploadingImg() || this.uploadingPdf()) return;
    for (const [campo, editor] of Object.entries(this.ckEditors)) {
      this.form.get(campo)?.setValue(editor.getData());
    }

    this.submitting.set(true);
    this.cursoService.create({ ...this.form.value, planes: this.planesSeleccionados() }).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'El programa, la Versión 1 y los planes quedaron registrados.');
        this.router.navigate(['/cenefco/cursos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar el curso'));
        this.submitting.set(false);
      }
    });
  }



  toggleTarea(id: number, event: Event) {
    const isChecked = (event.target as HTMLInputElement).checked;
    const current = this.form.get('tareas_catalogo_ids')?.value || [];
    if (isChecked) {
      this.form.get('tareas_catalogo_ids')?.setValue([...current, id]);
    } else {
      this.form.get('tareas_catalogo_ids')?.setValue(current.filter((val: number) => val !== id));
    }
  }
}








