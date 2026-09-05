import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { InscripcionService } from '../../application/services/inscripcion.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { CampoFormularioDefinicion, ClaveRaiz } from '../../domain/models/inscripcion.model';

interface UsuarioAcademico {
  id_us:     number;
  nombre:    string;
  appaterno: string | null;
  apmaterno: string | null;
  ci:        string | null;
  email:     string | null;
  celular:   string | null;
}

@Component({
  selector: 'app-inscripcion-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './inscripcion-edit.html',
})
export class InscripcionEdit {
  private service    = inject(InscripcionService);
  private toast      = inject(ToastService);
  private router     = inject(Router);
  private route      = inject(ActivatedRoute);
  private fb         = inject(FormBuilder);
  private http       = inject(HttpClient);
  private fileUpload = inject(FileUploadService);

  submitting  = signal(false);
  loading     = signal(true);
  idUs        = signal<number | null>(null);
  documentos  = signal<Record<string, string>>({});
  subiendoDoc = signal(false);

  formularioCampos = signal<CampoFormularioDefinicion[]>([]);
  camposExtra      = signal<Record<string, any>>({});

  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    fecha_ins:       [''],
    observacion_ins: [''],
    observacion:     [''],
    estado:          [1],
  });

  usuarioForm = this.fb.group({
    nombre:    ['', [Validators.required]],
    appaterno: [''],
    apmaterno: [''],
    ci:        [''],
    email:     ['', [Validators.email]],
    celular:   [''],
  });

  constructor() {
    this.service.getDetalle(this.id).subscribe({
      next: (d) => {
        const ins = d.inscripcion;
        this.form.patchValue({
          fecha_ins:       ins.fecha_ins,
          observacion_ins: ins.observacion_ins,
          observacion:     ins.observacion,
          estado:          ins.estado,
        } as any);
        const docs = typeof ins.documentos === 'string' ? JSON.parse(ins.documentos) : (ins.documentos ?? {});
        this.documentos.set({ ...docs });
        const extra = typeof ins.campos_extra === 'string' ? JSON.parse(ins.campos_extra) : (ins.campos_extra ?? {});
        this.camposExtra.set({ ...extra });
        this.formularioCampos.set(d.formulario_campos ?? []);
        this.idUs.set(ins.id_us);
        this.cargarUsuario(ins.id_us);
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/inscripciones']); }
    });
  }

  private cargarUsuario(idUs: number): void {
    this.http.get<UsuarioAcademico>(`/api/v1/usuarios-academicos/${idUs}`).subscribe({
      next: (u) => { this.usuarioForm.patchValue(u); this.loading.set(false); },
      error: () => {
        this.toast.error('Error', 'No se pudieron cargar los datos del estudiante');
        this.loading.set(false);
      },
    });
  }

  nombresDocumentos(): string[] {
    const clavesFormulario = new Set(this.formularioCampos().filter(c => c.tipo === 'file').map(c => c.nombre_campo));
    return Object.keys(this.documentos()).filter(clave => !clavesFormulario.has(clave));
  }

  onDocumentoCampoSeleccionado(campo: CampoFormularioDefinicion, event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    this.subiendoDoc.set(true);
    this.fileUpload.uploadFile(file).subscribe({
      next: (res) => {
        this.documentos.update(docs => ({ ...docs, [campo.nombre_campo]: res.url }));
        this.subiendoDoc.set(false);
        input.value = '';
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo subir el documento'));
        this.subiendoDoc.set(false);
        input.value = '';
      },
    });
  }

  setCampoExtra(nombreCampo: string, valor: any): void {
    this.camposExtra.update(c => ({ ...c, [nombreCampo]: valor }));
  }

  valorCampo(campo: CampoFormularioDefinicion): any {
    if (campo.clave_raiz) return this.usuarioForm.get(this.controlUsuario(campo.clave_raiz))?.value ?? '';
    return this.camposExtra()[campo.nombre_campo] ?? '';
  }

  onCampoInput(campo: CampoFormularioDefinicion, event: Event): void {
    const target = event.target as HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement;
    if (campo.clave_raiz) { this.usuarioForm.get(this.controlUsuario(campo.clave_raiz))?.setValue(target.value); return; }
    this.setCampoExtra(campo.nombre_campo, target.value);
  }

  onCampoCheckbox(campo: CampoFormularioDefinicion, event: Event): void {
    this.setCampoExtra(campo.nombre_campo, (event.target as HTMLInputElement).checked);
  }

  private controlUsuario(claveRaiz: ClaveRaiz): string {
    return claveRaiz === 'telefono' ? 'celular'
      : claveRaiz === 'apellido_paterno' ? 'appaterno'
      : claveRaiz === 'apellido_materno' ? 'apmaterno'
      : claveRaiz;
  }

  quitarDocumento(clave: string): void {
    const docs = { ...this.documentos() };
    delete docs[clave];
    this.documentos.set(docs);
  }

  onDocumentoSeleccionado(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    this.subiendoDoc.set(true);
    this.fileUpload.uploadFile(file).subscribe({
      next: (res) => {
        const base  = file.name.replace(/\.[^.]+$/, '').replace(/[^a-zA-Z0-9_-]/g, '_') || 'documento';
        let clave = base;
        let n = 2;
        const docsActuales = this.documentos();
        while (docsActuales[clave]) { clave = `${base}_${n++}`; }
        this.documentos.update(docs => ({ ...docs, [clave]: res.url }));
        this.subiendoDoc.set(false);
        input.value = '';
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo subir el documento'));
        this.subiendoDoc.set(false);
        input.value = '';
      },
    });
  }

  onSubmit(): void {
    if (this.usuarioForm.invalid) { this.usuarioForm.markAllAsTouched(); return; }
    this.submitting.set(true);

    const guardarInscripcion = (estudianteYaActualizado: boolean) => {
      this.service.update(this.id, {
        ...this.form.value,
        documentos:    this.documentos(),
        campos_extra:  this.camposExtra(),
      } as any).subscribe({
        next: () => { this.toast.success('¡Actualizada!', 'Inscripción actualizada'); this.router.navigate(['/cenefco/inscripciones']); },
        error: (err: HttpErrorResponse) => {
          if (estudianteYaActualizado) {
            this.toast.error(
              'Actualización incompleta',
              `Los datos del estudiante se guardaron, pero la inscripción no se pudo actualizar: ${extractErrorMessage(err)}`
            );
          } else {
            this.toast.error('Error', extractErrorMessage(err));
          }
          this.submitting.set(false);
        }
      });
    };

    const idUs = this.idUs();
    if (idUs) {
      this.http.put(`/api/v1/usuarios-academicos/${idUs}`, this.usuarioForm.value).subscribe({
        next: () => guardarInscripcion(true),
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudieron actualizar los datos del estudiante'));
          this.submitting.set(false);
        }
      });
    } else {
      guardarInscripcion(false);
    }
  }
}
