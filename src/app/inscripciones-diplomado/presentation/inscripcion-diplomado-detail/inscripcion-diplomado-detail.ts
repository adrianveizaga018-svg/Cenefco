import { Component, OnInit, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { forkJoin } from 'rxjs';
import { InscripcionDiplomadoService } from '../../application/services/inscripcion-diplomado.service';
import { InscripcionDiplomado } from '../../domain/models/inscripcion-diplomado.model';
import { CiudadService } from '../../../ciudades/application/services/ciudad.service';
import { Ciudad } from '../../../ciudades/domain/models/ciudad.model';
import { ExpedidoService } from '../../../expedido/application/services/expedido.service';
import { Expedido } from '../../../expedido/domain/models/expedido.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { extractErrorMessage } from '../../../utils/http-error';
import Swal from 'sweetalert2';

interface MedioPago { id: number; nombre: string; }

const ESTADOS = [
  { value: 'pendiente',  label: 'Pendiente' },
  { value: 'revisado',   label: 'Revisado' },
  { value: 'aceptado',   label: 'Aceptado' },
  { value: 'rechazado',  label: 'Rechazado' },
  { value: 'contactado', label: 'Contactado' },
  { value: 'inscrito',   label: 'Inscrito' },
];

@Component({
  selector: 'app-inscripcion-diplomado-detail',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './inscripcion-diplomado-detail.html',
})
export class InscripcionDiplomadoDetail implements OnInit {
  private service       = inject(InscripcionDiplomadoService);
  private ciudadService  = inject(CiudadService);
  private expedidoService = inject(ExpedidoService);
  private http          = inject(HttpClient);
  private toast         = inject(ToastService);
  private route         = inject(ActivatedRoute);
  private router        = inject(Router);
  private fb            = inject(FormBuilder);
  private fileUpload    = inject(FileUploadService);

  readonly estados = ESTADOS;

  inscripcion = signal<InscripcionDiplomado | null>(null);
  ciudades    = signal<Ciudad[]>([]);
  expedidos   = signal<Expedido[]>([]);
  mediosPago  = signal<MedioPago[]>([]);
  loading    = signal(true);
  saving     = signal(false);
  convirtiendo = signal(false);
  convertirError = signal<string | null>(null);

  idImpManual  = signal<number | null>(null);
  idPlanManual = signal<number | null>(null);

  subiendoDoc = signal<string | null>(null);
  private uploadingFlag = signal(false);

  form = this.fb.group({
    nombre:                    ['', [Validators.required]],
    apellido_paterno:          [''],
    apellido_materno:          [''],
    fecha_nacimiento:          [''],
    email:                     ['', [Validators.required, Validators.email]],
    ci:                        [''],
    expedido_id:               [null as number | null],
    telefono_grupo_inscritos:  [''],
    ciudad_residencia_id:      [null as number | null],
    provincia_especificar:     [''],
    medio_pago_id:             [null as number | null],
    monto_pagado:              [null as number | null],
    sugerencia_curso:          [''],
    recomendar_docente:        [false],
    detalle_docente:           [''],
    estado:                    ['pendiente'],
    notificado:                [false],
  });

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));

    forkJoin({
      ciudades:   this.ciudadService.getAll({ pageSize: 200 }),
      expedidos:  this.expedidoService.getAll({ pageSize: 100 }),
      mediosPago: this.http.get<{ data: MedioPago[] }>('/api/v1/medios-pago', { params: { pageSize: '100' } }),
      inscripcion: this.service.getById(id),
    }).subscribe({
      next: ({ ciudades, expedidos, mediosPago, inscripcion: data }) => {
        this.ciudades.set(ciudades.data);
        this.expedidos.set(expedidos.data);
        this.mediosPago.set(mediosPago.data);

        this.inscripcion.set(data);
        this.form.patchValue({
          nombre:                   data.nombre,
          apellido_paterno:         data.apellido_paterno,
          apellido_materno:         data.apellido_materno,
          fecha_nacimiento:         data.fecha_nacimiento,
          email:                    data.email,
          ci:                       data.ci,
          expedido_id:              data.expedido_id,
          telefono_grupo_inscritos: data.telefono_grupo_inscritos,
          ciudad_residencia_id:     data.ciudad_residencia_id,
          provincia_especificar:    data.provincia_especificar,
          medio_pago_id:            data.medio_pago_id,
          monto_pagado:             data.monto_pagado,
          sugerencia_curso:         data.sugerencia_curso,
          recomendar_docente:       data.recomendar_docente,
          detalle_docente:          data.detalle_docente,
          estado:                   data.estado,
          notificado:               data.notificado,
        });
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la inscripción a diplomado');
        this.loading.set(false);
      },
    });
  }

  iniciales(): string {
    const nombre = this.inscripcion()?.nombre_completo ?? '';
    return nombre ? nombre.split(' ').filter(Boolean).slice(0, 2).map(s => s[0]).join('').toUpperCase() : '?';
  }

  estadoBadgeClass(): string {
    switch (this.inscripcion()?.estado) {
      case 'inscrito':   return 'bg-success/10 text-success';
      case 'aceptado':   return 'bg-success/10 text-success';
      case 'rechazado':  return 'bg-danger/10 text-danger';
      case 'contactado': return 'bg-blue-100 text-blue-700';
      case 'revisado':   return 'bg-warning/10 text-warning';
      default:           return 'bg-default-100 text-default-500';
    }
  }

  estadoLabel(): string {
    const estado = this.inscripcion()?.estado;
    return this.estados.find(e => e.value === estado)?.label ?? (estado || '—');
  }

  reemplazarDocumento(event: Event, campo: 'archivo_ci' | 'archivo_titulo' | 'archivo_cv' | 'archivo_foto_3x3' | 'archivo_comprobante_pago'): void {
    const id = this.inscripcion()?.id;
    if (!id) return;

    this.subiendoDoc.set(campo);
    this.fileUpload.handleFileSelect(event, {
      uploading: this.uploadingFlag,
      onSuccess: (url) => {
        this.service.update(id, { [campo]: url } as any).subscribe({
          next: (data) => {
            this.inscripcion.set(data);
            this.toast.success('Documento actualizado', 'El archivo fue reemplazado correctamente');
            this.subiendoDoc.set(null);
          },
          error: (err: HttpErrorResponse) => {
            this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar el nuevo documento'));
            this.subiendoDoc.set(null);
          },
        });
      },
      fallbackMsg: 'No se pudo subir el archivo',
    });
  }

  guardar(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    const id = this.inscripcion()?.id;
    if (!id) return;

    this.saving.set(true);
    this.service.update(id, this.form.value as any).subscribe({
      next: (data) => {
        this.inscripcion.set(data);
        this.toast.success('¡Guardado!', 'Inscripción a diplomado actualizada correctamente');
        this.saving.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err));
        this.saving.set(false);
      },
    });
  }

  convertirInscripcion(): void {
    const id = this.inscripcion()?.id;
    if (!id) return;

    Swal.fire({
      title: '¿Convertir a inscripción?',
      html: 'Se creará (o reutilizará) un usuario y se generará la inscripción correspondiente.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#1a56db',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, convertir',
      cancelButtonText: 'Cancelar',
    }).then((result) => {
      if (!result.isConfirmed) return;
      this.ejecutarConversion(id);
    });
  }

  private ejecutarConversion(id: number): void {
    this.convirtiendo.set(true);
    this.convertirError.set(null);

    const payload: { id_imp?: number; id_plan?: number } = {};
    if (this.idImpManual())  payload.id_imp  = this.idImpManual()!;
    if (this.idPlanManual()) payload.id_plan = this.idPlanManual()!;

    this.service.convertirInscripcion(id, payload).subscribe({
      next: (res) => {
        this.toast.success('¡Convertida!', res.mensaje);
        this.convirtiendo.set(false);
        this.router.navigate(['/cenefco/inscripcion-detail', res.inscripcion_id]);
      },
      error: (err: HttpErrorResponse) => {
        this.convirtiendo.set(false);
        if (err.status === 422) {
          this.convertirError.set(extractErrorMessage(err));
        } else {
          this.toast.error('Error', extractErrorMessage(err));
        }
      },
    });
  }

  eliminar(): void {
    const p = this.inscripcion();
    if (!p) return;

    Swal.fire({
      title: '¿Eliminar inscripción a diplomado?',
      html: `Se eliminará el registro de <b>${p.nombre_completo}</b>. Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then((result) => {
      if (!result.isConfirmed) return;
      this.service.delete(p.id).subscribe({
        next: () => {
          this.toast.success('¡Eliminada!', 'La inscripción a diplomado fue eliminada');
          this.router.navigate(['/cenefco/inscripciones-diplomado']);
        },
        error: () => this.toast.error('Error', 'No se pudo eliminar el registro'),
      });
    });
  }
}
