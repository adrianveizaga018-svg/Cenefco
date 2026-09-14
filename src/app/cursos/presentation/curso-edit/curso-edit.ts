import { Component, computed, inject, signal, OnInit } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { forkJoin, of } from 'rxjs';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { CursoService } from '../../application/services/curso.service';
import { CategoriaCurso, TipoCurso, PlanDoc } from '../../domain/models/curso.model';
import { FormularioService } from '../../../formularios/application/services/formulario.service';
import { Formulario } from '../../../formularios/domain/models/formulario.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { CursoImagenes } from '../../../common/components/curso-imagenes/curso-imagenes';
import { CKEditorModule } from '@ckeditor/ckeditor5-angular';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Swal from 'sweetalert2';
import { ConvenioService } from '../../../convenios/application/services/convenio.service';
import { ConvenioOption } from '../../../convenios/domain/models/convenio.model';
import { VendedorService } from '../../../vendedores/application/services/vendedor.service';
import { Vendedor } from '../../../vendedores/domain/models/vendedor.model';
import { NgSelectModule } from '@ng-select/ng-select';
import { CursoReglamento } from '../curso-reglamento/curso-reglamento';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { AreaService } from '../../../areas/application/services/area.service';
import { generateSlug } from '../../../utils/slug';
import { portalUrl } from '../../../constants';
import { DocentePerfilService } from '../../../docentes-perfil/application/services/docente-perfil.service';
import { DecimalPipe, DatePipe } from '@angular/common';
import { CertificadoService } from '../../../certificados/application/services/certificado.service';
import { ListaAprobado, ImportarParticipantesExcelResult } from '../../../certificados/domain/models/certificado.model';
import { AuthService } from '../../../auth/application/services/auth.service';
import QRCode from 'qrcode';
import * as XLSX from 'xlsx';

interface Imparticion { id_imp: number; periodo: string | null; gestion: string | null; materia_nombre: string | null; paralelo: string | null; id_mat: number | null; docente_nombre: string | null; }

interface DocentePerfil {
  id: number;
  nombre_completo: string;
  titulo_academico: string | null;
  especialidad: string | null;
  foto_url: string | null;
  email_publico: string | null;
}

type Tab = 'datos' | 'planes' | 'docentes' | 'reglamento' | 'inscritos' | 'participantes';

interface InscriptoRow {
  id_ins:            number;
  estudiante_nombre: string | null;
  estudiante_ci:     string | null;
  estudiante_email:  string | null;
  estudiante_celular: string | null;
  fecha_ins:         string | null;
  estado:            number;
  canal_venta:       string | null;
  total_pagado:      number | null;
  cuotas_pagadas:    number | null;
  periodo:           string | null;
  gestion:           string | null;
  es_participante:   boolean;
}

@Component({
  selector: 'app-curso-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle, CursoImagenes, CKEditorModule, CursoReglamento, NgSelectModule, DecimalPipe, DatePipe],
  templateUrl: './curso-edit.html',
  styles: ``,
})
export class CursoEdit implements OnInit {
  readonly portalBase = portalUrl;

  private readonly SLUGS_CON_PREFIJO_CURSOS = ['excel-avanzado-3ra'];

  portalCursoUrl(slug: string): string {
    return this.SLUGS_CON_PREFIJO_CURSOS.includes(slug)
      ? `https://cenefco.com/cursos/${slug}`
      : `${this.portalBase}/${slug}`;
  }

  qrDataUrl   = signal<string | null>(null);
  qrUrl       = signal<string | null>(null);
  qrTitulo    = signal<string>('');
  qrSubtitulo = signal<string>('');
  qrCopiado   = signal(false);

  async generarQRCurso(): Promise<void> {
    const slug = this.form.get('slug')?.value;
    if (!slug) return;
    await this.generarQR(
      this.portalCursoUrl(slug),
      'Escanea el código para ver el curso en el portal',
      'qr-curso'
    );
  }

  async generarQRParticipantes(): Promise<void> {
    const slug = this.form.get('slug')?.value;
    if (!slug) return;
    await this.generarQR(
      this.portalCursoUrl(slug) + '-participantes',
      'Escanea el código para ver los participantes',
      'qr-participantes'
    );
  }

  private async generarQR(url: string, subtitulo: string, prefijoArchivo: string): Promise<void> {
    const dataUrl = await QRCode.toDataURL(url, { width: 300, margin: 2, color: { dark: '#111827', light: '#ffffff' } });
    const nombre  = this.form.get('nombre_programa')?.value || 'curso';
    this.qrDataUrl.set(dataUrl);
    this.qrUrl.set(url);
    this.qrTitulo.set(`${prefijoArchivo}-${nombre}`);
    this.qrSubtitulo.set(subtitulo);
    this.qrCopiado.set(false);
  }

  cerrarQR(): void {
    this.qrDataUrl.set(null);
    this.qrUrl.set(null);
  }

  descargarQR(): void {
    const url    = this.qrDataUrl();
    const nombre = this.qrTitulo() || 'qr';
    if (!url) return;
    const a = document.createElement('a');
    a.href = url;
    a.download = `${nombre.toLowerCase().replace(/\s+/g, '-')}.png`;
    a.click();
  }

  async copiarUrlQR(): Promise<void> {
    const url = this.qrUrl();
    if (!url) return;
    await navigator.clipboard.writeText(url);
    this.qrCopiado.set(true);
    setTimeout(() => this.qrCopiado.set(false), 2000);
  }

  private cursoService      = inject(CursoService);
  private formularioService = inject(FormularioService);
  private convenioService   = inject(ConvenioService);
  private vendedorService   = inject(VendedorService);
  private auth              = inject(AuthService);
  private toast             = inject(ToastService);
  private router            = inject(Router);
  private route             = inject(ActivatedRoute);
  private fb                = inject(FormBuilder);
  private fileUpload        = inject(FileUploadService);
  private areaService       = inject(AreaService);
  private http              = inject(HttpClient);

  submitting       = signal(false);
  togglingEstado   = signal(false);
  Editor          = ClassicEditor as any;
  private ckEditors: Record<string, any> = {};
  onEditorReady(editor: any, campo: string) { this.ckEditors[campo] = editor; }
  loadingCurso  = signal(true);
  categorias    = signal<CategoriaCurso[]>([]);
  tipos         = signal<TipoCurso[]>([]);
  planesDoc     = signal<PlanDoc[]>([]);
  convenios     = signal<ConvenioOption[]>([]);
  vendedores    = signal<Vendedor[]>([]);
  areas         = signal<{ id: number; titulo: string }[]>([]);
  formularios   = signal<Formulario[]>([]);
  catalogoTareas = signal<any[]>([]);

  imparticiones = signal<Imparticion[]>([]);
  uploadingImg  = signal(false);
  imgPreview    = signal<string | null>(null);
  uploadingPdf  = signal(false);
  pdfName       = signal<string | null>(null);
  private id!: number;
  private slug!: string;

  activeTab        = signal<Tab>('datos');
  cursoIdPrograma  = signal<number | null>(null);

  get esVendedorRestringido(): boolean {
    return this.auth.currentUser()?.rolNombre === 'vendedor';
  }

  cursoDocentes        = signal<DocentePerfil[]>([]);
  cursoDocentesLoading = signal(false);
  cursoDocentesLoaded  = signal(false);
  quitandoId           = signal<number | null>(null);

  todosPerfil        = signal<DocentePerfil[]>([]);
  todosPerfilLoading = signal(false);
  todosPerfilLoaded  = signal(false);
  mostrarPicker      = signal(false);
  docenteBusqueda    = signal('');
  agregandoId        = signal<number | null>(null);

  private docentePerfilSvc = inject(DocentePerfilService);

  inscritos        = signal<InscriptoRow[]>([]);
  inscritosLoading = signal(false);
  inscritosLoaded  = signal(false);
  inscritosBusq    = signal('');

  inscritosSeleccionados  = signal<Set<number>>(new Set());
  marcandoParticipanteId  = signal<number | null>(null);
  marcandoParticipantesBulk = signal(false);

  private certSvc = inject(CertificadoService);

  participantes        = signal<ListaAprobado[]>([]);
  participantesLoading = signal(false);
  participantesLoaded  = signal(false);
  participantesBusq    = signal('');
  partic_guardando     = signal(false);
  partic_eliminandoId  = signal<number | null>(null);
  participantesSeleccionados = signal<Set<number>>(new Set());
  eliminandoParticipantesBulk = signal(false);

  partic_nombre               = signal<string>('');
  partic_appaterno            = signal<string>('');
  partic_apmaterno            = signal<string>('');
  partic_ci                   = signal<string>('');
  partic_soloNombreCompleto   = signal(false);
  partic_email                = signal<string>('');
  partic_notaFinal            = signal<string>('');
  partic_condicion            = signal<string>('aprobado');
  partic_obs                  = signal<string>('');
  partic_comprobante_url      = signal<string | null>(null);
  partic_comprobante_nombre   = signal<string | null>(null);
  partic_comprobante_preview  = signal<string | null>(null);
  partic_comprobante_uploading = signal(false);
  partic_comprobante_esPdf    = signal(false);

  editandoId              = signal<number | null>(null);
  edit_appaterno          = signal<string>('');
  edit_apmaterno          = signal<string>('');
  edit_nombre             = signal<string>('');
  edit_condicion          = signal<string>('aprobado');
  edit_nota               = signal<string>('');
  edit_obs                = signal<string>('');
  edit_comp_url           = signal<string | null>(null);
  edit_comp_nombre        = signal<string | null>(null);
  edit_comp_preview       = signal<string | null>(null);
  edit_comp_esPdf         = signal(false);
  edit_comp_uploading     = signal(false);
  edit_guardando          = signal(false);

  participantesFiltradosTab = computed(() => {
    const q = this.participantesBusq().toLowerCase().trim();
    if (!q) return this.participantes();
    return this.participantes().filter(p =>
      ((p.estudiante_nombre ?? '') + ' ' + (p.estudiante_ci ?? '') + ' ' + (p.estudiante_email ?? '')).toLowerCase().includes(q)
    );
  });

  inscritosFiltrados = computed(() => {
    const q = this.inscritosBusq().toLowerCase().trim();
    if (!q) return this.inscritos();
    return this.inscritos().filter(i =>
      ((i.estudiante_nombre ?? '') + ' ' + (i.estudiante_ci ?? '') + ' ' + (i.estudiante_email ?? '')).toLowerCase().includes(q)
    );
  });

  inscritosActivosCount = computed(() => this.inscritos().filter(i => i.estado === 1).length);

  inscritosTotalPagado = computed(() => this.inscritos().reduce((sum, i) => sum + (i.total_pagado ?? 0), 0));

  inscritosSeleccionadosCount = computed(() => this.inscritosSeleccionados().size);

  participantesSeleccionadosCount = computed(() => this.participantesSeleccionados().size);

  docentesFiltrados = computed(() => {
    const asignados = new Set(this.cursoDocentes().map(d => d.id));
    const q = this.docenteBusqueda().toLowerCase().trim();
    return this.todosPerfil()
      .filter(d => !asignados.has(d.id))
      .filter(d => !q || (d.nombre_completo + ' ' + (d.especialidad ?? '') + ' ' + (d.titulo_academico ?? '')).toLowerCase().includes(q));
  });

  imparticionLabel(imp: Imparticion): string {
    const mat = imp.materia_nombre ?? `ID ${imp.id_mat}`;
    const doc = imp.docente_nombre?.trim() || '';
    return `[${imp.periodo}] ${mat}${doc ? ' — ' + doc : ''}`;
  }

  imparticionActualLabel = signal('Sin impartición asignada');

  private actualizarImparticionActualLabel(idImp: number | null): void {
    if (idImp == null) {
      this.imparticionActualLabel.set('Sin impartición asignada');
      return;
    }
    const imp = this.imparticiones().find(i => i.id_imp === idImp);
    this.imparticionActualLabel.set(imp ? this.imparticionLabel(imp) : `Impartición #${idImp}`);
  }

  inicialesDocente(nombre: string): string {
    return nombre.split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase();
  }

  loadCursoDocentes(): void {
    this.cursoDocentesLoading.set(true);
    this.cursoService.getDocentes(this.id).subscribe({
      next: (r) => { this.cursoDocentes.set(r); this.cursoDocentesLoading.set(false); this.cursoDocentesLoaded.set(true); },
      error: () => { this.cursoDocentesLoading.set(false); this.toast.error('Error', 'No se pudieron cargar los docentes'); },
    });
  }

  abrirPicker(): void {
    this.mostrarPicker.set(true);
    this.docenteBusqueda.set('');
    if (!this.todosPerfilLoaded()) {
      this.todosPerfilLoading.set(true);
      this.docentePerfilSvc.getAll({ pageSize: 500 }).subscribe({
        next: (r) => { this.todosPerfil.set(r.data); this.todosPerfilLoading.set(false); this.todosPerfilLoaded.set(true); },
        error: () => { this.todosPerfilLoading.set(false); this.toast.error('Error', 'No se pudieron cargar los docentes'); },
      });
    }
  }

  agregarDocente(d: DocentePerfil): void {
    this.agregandoId.set(d.id);
    this.cursoService.attachDocente(this.id, d.id).subscribe({
      next: () => {
        this.cursoDocentes.update(list => [...list, d]);
        this.agregandoId.set(null);
        this.mostrarPicker.set(false);
        this.docenteBusqueda.set('');
        this.toast.success('Docente agregado', `${d.nombre_completo} fue asignado al curso.`);
      },
      error: () => {
        this.agregandoId.set(null);
        this.toast.error('Error', 'No se pudo agregar el docente.');
      },
    });
  }

  quitarDocente(d: DocentePerfil): void {
    Swal.fire({
      title: '¿Quitar docente?',
      text: `${d.nombre_completo} será removido de este curso.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, quitar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.quitandoId.set(d.id);
      this.cursoService.detachDocente(this.id, d.id).subscribe({
        next: () => {
          this.cursoDocentes.update(list => list.filter(x => x.id !== d.id));
          this.quitandoId.set(null);
          this.toast.success('Docente quitado', `${d.nombre_completo} fue removido del curso.`);
        },
        error: () => {
          this.quitandoId.set(null);
          this.toast.error('Error', 'No se pudo quitar el docente.');
        },
      });
    });
  }

  setTab(tab: Tab): void {
    this.activeTab.set(tab);
    if (tab === 'docentes'      && !this.cursoDocentesLoaded())  this.loadCursoDocentes();
    if (tab === 'inscritos'     && !this.inscritosLoaded())      this.loadInscritos();
    if (tab === 'participantes' && !this.participantesLoaded())  this.loadParticipantes();
  }

  private impId(): number | null {
    return this.form.get('id_imp')?.value ?? null;
  }

  loadParticipantes(): void {
    const impId = this.impId();
    if (!impId) { this.participantesLoaded.set(true); return; }
    this.participantesLoading.set(true);
    this.certSvc.getAprobados({ imparte_id: impId, pageSize: 500 }).subscribe({
      next: r => {
        this.participantes.set(r.data);
        this.participantesLoading.set(false);
        this.participantesLoaded.set(true);
      },
      error: () => {
        this.participantesLoading.set(false);
        this.toast.error('Error', 'No se pudieron cargar los participantes.');
      },
    });
  }

  limpiarFormParticipante(): void {
    this.partic_nombre.set('');
    this.partic_appaterno.set('');
    this.partic_apmaterno.set('');
    this.partic_ci.set('');
    this.partic_email.set('');
    this.partic_notaFinal.set('');
    this.partic_condicion.set('aprobado');
    this.partic_obs.set('');
    this.partic_comprobante_url.set(null);
    this.partic_comprobante_nombre.set(null);
    this.partic_comprobante_preview.set(null);
    this.partic_comprobante_esPdf.set(false);
  }

  onComprobanteSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0];
    if (!file) return;

    const esPdf = file.type === 'application/pdf';
    this.partic_comprobante_esPdf.set(esPdf);
    this.partic_comprobante_nombre.set(file.name);
    this.partic_comprobante_url.set(null);
    this.partic_comprobante_preview.set(null);

    if (!esPdf) {
      const reader = new FileReader();
      reader.onload = (e) => this.partic_comprobante_preview.set(e.target?.result as string);
      reader.readAsDataURL(file);
    }

    this.partic_comprobante_uploading.set(true);
    this.fileUpload.uploadFile(file).subscribe({
      next: (res) => {
        this.partic_comprobante_url.set(res.url);
        this.partic_comprobante_uploading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo subir el comprobante.');
        this.partic_comprobante_nombre.set(null);
        this.partic_comprobante_preview.set(null);
        this.partic_comprobante_uploading.set(false);
        input.value = '';
      },
    });
  }

  quitarComprobante(): void {
    this.partic_comprobante_url.set(null);
    this.partic_comprobante_nombre.set(null);
    this.partic_comprobante_preview.set(null);
    this.partic_comprobante_esPdf.set(false);
  }

  agregarParticipante(): void {
    const impId = this.impId();
    const soloNombreCompleto = this.partic_soloNombreCompleto();
    const ci = this.partic_ci().trim();
    const nombre = this.partic_nombre().trim();

    if (!impId) {
      this.toast.warning('Sin impartición', 'El curso no tiene una impartición asignada.');
      return;
    }
    if (!soloNombreCompleto && !ci) {
      this.toast.warning('CI requerido', 'Ingresa el carnet de identidad del participante.');
      return;
    }
    if (!nombre) {
      this.toast.warning('Nombre requerido', 'Ingresa el nombre del participante.');
      return;
    }

    this.partic_guardando.set(true);
    this.certSvc.registrarParticipanteLibre({
      imparte_id:      impId,
      nombre,
      appaterno:       soloNombreCompleto ? undefined : (this.partic_appaterno().trim() || undefined),
      apmaterno:       soloNombreCompleto ? undefined : (this.partic_apmaterno().trim() || undefined),
      ci:              soloNombreCompleto ? undefined : ci,
      email:           this.partic_email().trim() || undefined,
      condicion:       this.partic_condicion() || 'aprobado',
      nota_final:      this.partic_notaFinal() ? parseFloat(this.partic_notaFinal()) : null,
      observacion:     this.partic_obs().trim() || undefined,
      comprobante_url: this.partic_comprobante_url() ?? undefined,
      solo_nombre_completo: soloNombreCompleto,
    }).subscribe({
      next: (row: any) => {
        this.partic_guardando.set(false);
        this.limpiarFormParticipante();
        this.participantesLoaded.set(false);
        this.loadParticipantes();
        const nombreCompleto = row.estudiante_nombre ?? `${nombre}`;
        this.toast.success('Participante agregado', `${nombreCompleto} fue registrado exitosamente.`);
        this.preguntarGenerarCertificadoExterno(nombreCompleto);
      },
      error: (err: HttpErrorResponse) => {
        this.partic_guardando.set(false);
        let msg: string;
        if (err.status === 422) {
          const errores = err.error?.errors;
          msg = errores ? Object.values(errores).flat().join(' ') : (err.error?.message ?? 'El participante ya está registrado.');
        } else {
          msg = extractErrorMessage(err, 'No se pudo agregar el participante.');
        }
        this.toast.error('Error', msg);
      },
    });
  }

  generarCertificadoExterno(p: ListaAprobado): void {
    const nombre = (p.estudiante_nombre ?? '').trim();
    if (!nombre) {
      this.toast.warning('Sin nombre', 'Este participante no tiene un nombre completo registrado.');
      return;
    }
    this.elegirCursoCertificadoExterno([nombre]);
  }

  generarCertificadosImportados(): void {
    const nombres = this.importResultado()?.registrados ?? [];
    if (!nombres.length) return;
    this.elegirCursoCertificadoExterno(nombres);
  }

  private preguntarGenerarCertificadoExterno(nombreCompleto: string): void {
    Swal.fire({
      title: '¿Generar certificado?',
      text: `¿Deseas generar un certificado para ${nombreCompleto}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, generar',
      cancelButtonText: 'No',
      confirmButtonColor: '#6366f1',
      cancelButtonColor: '#6b7280',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.elegirCursoCertificadoExterno([nombreCompleto]);
    });
  }

  private elegirCursoCertificadoExterno(nombres: string[]): void {
    Swal.fire({
      title: 'Cargando cursos disponibles...',
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: () => Swal.showLoading(),
    });

    this.certSvc.listarCursosCertificadoExterno().subscribe({
      next: (res) => {
        if (!res.cursos.length) {
          Swal.fire('Sin cursos', 'No hay cursos disponibles en el servicio de certificados.', 'info');
          return;
        }

        let cursoSeleccionadoId: number | null = null;

        const filasHtml = res.cursos.map(c => `
          <button type="button" class="swal-curso-item" data-id="${c.id}" data-nombre="${this.escapeHtml(c.curso.toLowerCase())}"
            style="all:unset;box-sizing:border-box;display:flex;flex-direction:column;gap:3px;text-align:left;
              padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:border-color .15s,background-color .15s;">
            <span style="font-weight:600;font-size:14px;color:#1f2937;line-height:1.35;word-break:break-word;">
              ${this.escapeHtml(c.curso)}
            </span>
            <span style="font-size:12px;color:#6b7280;">
              ${this.escapeHtml(c.estado_display)} · ${c.total_participantes} participante${c.total_participantes === 1 ? '' : 's'}
            </span>
          </button>
        `).join('');

        const subtitulo = nombres.length > 1
          ? `<p style="margin:0 0 12px;font-size:13px;color:#6b7280;">Se registrarán <strong>${nombres.length} participantes</strong> en el curso que elijas.</p>`
          : '';

        Swal.fire<number>({
          title: 'Selecciona el curso de certificación',
          width: '640px',
          html: `
            <div style="text-align:left;">
              ${subtitulo}
              <input id="swal-curso-search" type="text" placeholder="Buscar curso por nombre..."
                style="width:100%;box-sizing:border-box;padding:10px 14px;border:1px solid #d1d5db;
                  border-radius:10px;margin-bottom:12px;font-size:14px;outline:none;">
              <div id="swal-curso-list" style="max-height:360px;overflow-y:auto;display:flex;flex-direction:column;gap:8px;padding-right:4px;">
                ${filasHtml}
              </div>
              <p id="swal-curso-empty" style="display:none;text-align:center;color:#9ca3af;font-size:13px;padding:16px 0;">
                Ningún curso coincide con la búsqueda.
              </p>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'Registrar',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#6366f1',
          focusConfirm: false,
          didOpen: () => {
            const popup        = Swal.getPopup()!;
            const searchInput  = popup.querySelector<HTMLInputElement>('#swal-curso-search')!;
            const emptyMsg     = popup.querySelector<HTMLElement>('#swal-curso-empty')!;
            const items        = Array.from(popup.querySelectorAll<HTMLButtonElement>('.swal-curso-item'));

            const marcarSeleccionado = (seleccionado: HTMLButtonElement) => {
              items.forEach(i => {
                i.style.borderColor = '#e5e7eb';
                i.style.backgroundColor = 'transparent';
              });
              seleccionado.style.borderColor = '#6366f1';
              seleccionado.style.backgroundColor = '#eef2ff';
            };

            items.forEach(item => {
              item.addEventListener('click', () => {
                cursoSeleccionadoId = Number(item.dataset['id']);
                marcarSeleccionado(item);
                Swal.resetValidationMessage();
              });
            });

            searchInput.addEventListener('input', () => {
              const q = searchInput.value.toLowerCase().trim();
              let visibles = 0;
              items.forEach(item => {
                const coincide = !q || (item.dataset['nombre'] ?? '').includes(q);
                item.style.display = coincide ? 'flex' : 'none';
                if (coincide) visibles++;
              });
              emptyMsg.style.display = visibles === 0 ? 'block' : 'none';
            });

            searchInput.focus();
          },
          preConfirm: () => {
            if (cursoSeleccionadoId == null) {
              Swal.showValidationMessage('Selecciona un curso de la lista.');
              return false;
            }
            return cursoSeleccionadoId;
          },
        }).then(sel => {
          if (!sel.isConfirmed || sel.value == null) return;
          this.registrarEnCertificadoExterno(sel.value, nombres);
        });
      },
      error: () => {
        Swal.fire('Error', 'No se pudo conectar con el servicio de certificados.', 'error');
      },
    });
  }

  private escapeHtml(texto: string): string {
    return texto
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  private registrarEnCertificadoExterno(cursoId: number, nombres: string[]): void {
    this.certSvc.registrarEstudiantesCertificadoExterno(cursoId, nombres).subscribe({
      next: (res) => {
        const registrados = res.registrados?.length ?? 0;
        const duplicados  = res.duplicados?.length ?? 0;

        if (registrados > 0 && duplicados === 0) {
          this.toast.success('Certificado', registrados === 1
            ? `${nombres[0]} fue agregado al curso de certificación.`
            : `${registrados} participantes fueron agregados al curso de certificación.`);
        } else if (registrados > 0 && duplicados > 0) {
          this.toast.warning('Certificado', `${registrados} agregado(s), ${duplicados} ya estaban registrados en ese curso.`);
        } else {
          this.toast.warning('Ya registrados', nombres.length === 1
            ? `${nombres[0]} ya estaba registrado en ese curso de certificación.`
            : 'Todos los participantes ya estaban registrados en ese curso de certificación.');
        }
      },
      error: (err: HttpErrorResponse) => {
        if (err.status === 409) {
          this.toast.warning('Ya registrados', nombres.length === 1
            ? `${nombres[0]} ya estaba registrado en ese curso de certificación.`
            : 'Todos los participantes ya estaban registrados en ese curso de certificación.');
        } else {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo registrar en el servicio de certificados.'));
        }
      },
    });
  }


  importModalAbierto      = signal(false);
  importArchivo           = signal<File | null>(null);
  importando              = signal(false);
  importResultado         = signal<ImportarParticipantesExcelResult | null>(null);
  importSoloNombreCompleto = signal(false);
  importArrastrando       = signal(false);

  private readonly extensionesPermitidas = ['.xlsx', '.xls', '.csv'];

  abrirModalImportar(): void {
    if (!this.impId()) {
      this.toast.warning('Sin impartición', 'El curso no tiene una impartición asignada.');
      return;
    }
    this.importArchivo.set(null);
    this.importResultado.set(null);
    this.importSoloNombreCompleto.set(false);
    this.importArrastrando.set(false);
    this.importModalAbierto.set(true);
  }

  cerrarModalImportar(): void {
    if (this.importando()) return;
    this.importModalAbierto.set(false);
  }

  onImportArchivoSeleccionado(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.importArchivo.set(file);
    this.importResultado.set(null);
  }

  onImportDragOver(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    if (this.importando()) return;
    this.importArrastrando.set(true);
  }

  onImportDragLeave(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    this.importArrastrando.set(false);
  }

  onImportDrop(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    this.importArrastrando.set(false);
    if (this.importando()) return;

    const file = event.dataTransfer?.files?.[0] ?? null;
    if (!file) return;

    const nombre = file.name.toLowerCase();
    const esValido = this.extensionesPermitidas.some(ext => nombre.endsWith(ext));
    if (!esValido) {
      this.toast.warning('Archivo no válido', 'Solo se aceptan archivos .xlsx, .xls o .csv.');
      return;
    }

    this.importArchivo.set(file);
    this.importResultado.set(null);
  }

  importarParticipantesExcel(): void {
    const impId = this.impId();
    const archivo = this.importArchivo();
    if (!impId || !archivo) return;

    this.importando.set(true);
    this.importResultado.set(null);

    this.certSvc.importarParticipantesExcel(impId, archivo, this.importSoloNombreCompleto()).subscribe({
      next: (res) => {
        this.importResultado.set(res);
        this.importando.set(false);
        if (res.insertados > 0) {
          this.participantesLoaded.set(false);
          this.loadParticipantes();
          this.toast.success('Importación completa', `${res.insertados} participante(s) agregado(s).`);
        } else {
          this.toast.warning('Sin cambios', 'No se agregó ningún participante nuevo — revisa el detalle de filas omitidas.');
        }
      },
      error: (err: HttpErrorResponse) => {
        this.importando.set(false);
        const msg = err.status === 422
          ? (err.error?.message ?? 'El archivo no pudo procesarse.')
          : extractErrorMessage(err, 'No se pudo importar el archivo.');
        this.toast.error('Error', msg);
      },
    });
  }

  descargarPlantillaImportacion(): void {
    const encabezados = ['CI', 'Nombre', 'Apellido Paterno', 'Apellido Materno', 'Email', 'Condición', 'Nota Final', 'Observación'];
    const ejemplo      = ['12345678', 'Ana', 'Lopez', 'Gomez', 'ana@ejemplo.com', 'aprobado', 88, ''];

    const hoja = XLSX.utils.aoa_to_sheet([encabezados, ejemplo]);
    const libro = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(libro, hoja, 'Participantes');
    XLSX.writeFile(libro, 'plantilla-participantes.xlsx');
  }

  descargarPlantillaSoloNombreCompleto(): void {
    const filas = [['Lopez Gomez Ana'], ['Perez Mamani Juan Carlos']];
    const hoja = XLSX.utils.aoa_to_sheet(filas);
    const libro = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(libro, hoja, 'Participantes');
    XLSX.writeFile(libro, 'plantilla-participantes-nombre-completo.xlsx');
  }

  eliminarParticipante(p: ListaAprobado): void {
    Swal.fire({
      title: '¿Eliminar participante?',
      text: `${p.estudiante_nombre ?? 'Este participante'} será removido de la lista.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.partic_eliminandoId.set(p.id);
      this.certSvc.deleteAprobado(p.id).subscribe({
        next: () => {
          this.participantes.update(list => list.filter(x => x.id !== p.id));
          this.partic_eliminandoId.set(null);
          this.toast.success('Eliminado', `${p.estudiante_nombre ?? 'Participante'} fue removido.`);
        },
        error: () => {
          this.partic_eliminandoId.set(null);
          this.toast.error('Error', 'No se pudo eliminar el participante.');
        },
      });
    });
  }

  estaSeleccionadoParticipante(id: number): boolean {
    return this.participantesSeleccionados().has(id);
  }

  limpiarSeleccionParticipantes(): void {
    this.participantesSeleccionados.set(new Set());
  }

  toggleSeleccionParticipante(id: number, checked: boolean): void {
    this.participantesSeleccionados.update(set => {
      const nuevo = new Set(set);
      if (checked) nuevo.add(id); else nuevo.delete(id);
      return nuevo;
    });
  }

  toggleSeleccionarTodosParticipantes(checked: boolean): void {
    this.participantesSeleccionados.set(
      checked ? new Set(this.participantesFiltradosTab().map(p => p.id)) : new Set()
    );
  }

  eliminarParticipantesSeleccionados(): void {
    const ids = Array.from(this.participantesSeleccionados());
    if (!ids.length) return;

    Swal.fire({
      title: `¿Eliminar ${ids.length} participante(s)?`,
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.eliminandoParticipantesBulk.set(true);
      this.certSvc.deleteAprobadosBulk(ids).subscribe({
        next: (res) => {
          const idsSet = new Set(ids);
          this.participantes.update(list => list.filter(x => !idsSet.has(x.id)));
          this.participantesSeleccionados.set(new Set());
          this.eliminandoParticipantesBulk.set(false);
          this.toast.success('Eliminados', `${res.eliminados} participante(s) removido(s).`);
        },
        error: () => {
          this.eliminandoParticipantesBulk.set(false);
          this.toast.error('Error', 'No se pudieron eliminar los participantes seleccionados.');
        },
      });
    });
  }

  inicialesParticipante(nombre: string): string {
    return nombre.split(' ').filter(Boolean).slice(0, 2).map(s => s[0]).join('').toUpperCase();
  }

  abrirEdicion(p: ListaAprobado): void {
    this.editandoId.set(p.id);
    this.edit_appaterno.set(p.estudiante_appaterno ?? '');
    this.edit_apmaterno.set(p.estudiante_apmaterno ?? '');
    this.edit_nombre.set(p.estudiante_nombre_pila ?? '');
    this.edit_condicion.set(p.condicion);
    this.edit_nota.set(p.nota_final != null ? String(p.nota_final) : '');
    this.edit_obs.set(p.observacion ?? '');
    this.edit_comp_url.set(p.comprobante_url ?? null);
    this.edit_comp_nombre.set(p.comprobante_url ? 'Comprobante actual' : null);
    this.edit_comp_preview.set(null);
    this.edit_comp_esPdf.set(false);
    this.edit_comp_uploading.set(false);
  }

  cancelarEdicion(): void {
    this.editandoId.set(null);
  }

  onEditComprobanteSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0];
    if (!file) return;
    const esPdf = file.type === 'application/pdf';
    this.edit_comp_esPdf.set(esPdf);
    this.edit_comp_nombre.set(file.name);
    this.edit_comp_url.set(null);
    this.edit_comp_preview.set(null);
    if (!esPdf) {
      const reader = new FileReader();
      reader.onload = (e) => this.edit_comp_preview.set(e.target?.result as string);
      reader.readAsDataURL(file);
    }
    this.edit_comp_uploading.set(true);
    this.fileUpload.uploadFile(file).subscribe({
      next: (res) => { this.edit_comp_url.set(res.url); this.edit_comp_uploading.set(false); },
      error: () => {
        this.toast.error('Error', 'No se pudo subir el comprobante.');
        this.edit_comp_nombre.set(null); this.edit_comp_uploading.set(false); input.value = '';
      },
    });
  }

  guardarEdicion(p: ListaAprobado): void {
    this.edit_guardando.set(true);

    const appaterno = this.edit_appaterno().trim();
    const apmaterno = this.edit_apmaterno().trim();
    const nombre    = this.edit_nombre().trim();
    const nombreCambio =
      appaterno !== (p.estudiante_appaterno ?? '') ||
      apmaterno !== (p.estudiante_apmaterno ?? '') ||
      nombre    !== (p.estudiante_nombre_pila ?? '');

    forkJoin({
      aprobado: this.certSvc.updateAprobado(p.id, {
        condicion:       this.edit_condicion(),
        nota_final:      this.edit_nota() ? parseFloat(this.edit_nota()) : null,
        observacion:     this.edit_obs().trim() || null,
        comprobante_url: this.edit_comp_url(),
      }),
      estudiante: nombreCambio
        ? this.certSvc.updateEstudianteNombre(p.usuario_id, { nombre, appaterno, apmaterno })
        : of(null),
    }).subscribe({
      next: ({ aprobado: row }: { aprobado: any; estudiante: any }) => {
        this.participantes.update(list => list.map(x => x.id === p.id ? {
          ...x,
          condicion:        row.condicion        ?? x.condicion,
          nota_final:       row.nota_final       ?? null,
          observacion:      row.observacion      ?? null,
          comprobante_url:  row.comprobante_url  ?? null,
          estudiante_appaterno:   appaterno,
          estudiante_apmaterno:   apmaterno,
          estudiante_nombre_pila: nombre,
          estudiante_nombre: [appaterno, apmaterno, nombre].filter(Boolean).join(' '),
        } : x));
        this.edit_guardando.set(false);
        this.editandoId.set(null);
        this.toast.success('Actualizado', `${p.estudiante_nombre ?? 'Participante'} fue actualizado.`);
      },
      error: (err: HttpErrorResponse) => {
        this.edit_guardando.set(false);
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar.'));
      },
    });
  }

  condicionBadgeClass(condicion: string): string {
    if (condicion === 'aprobado') return 'bg-success/10 text-success';
    if (condicion === 'reprobado') return 'bg-danger/10 text-danger';
    return 'bg-default-100 text-default-500';
  }

  loadInscritos(): void {
    if (!this.id) return;
    this.inscritosLoading.set(true);
    this.http.get<{ data: any[] }>('/api/v1/inscripciones', {
      params: { programa_id: String(this.id), pageSize: '500', pageIndex: '1' }
    }).subscribe({
      next: r => {
        this.inscritos.set(r.data.map((i: any) => ({
          id_ins:             i.id_ins,
          estudiante_nombre:  i.estudiante_nombre  ?? null,
          estudiante_ci:      i.estudiante_ci      ?? null,
          estudiante_email:   i.estudiante_email   ?? null,
          estudiante_celular: i.estudiante_celular ?? null,
          fecha_ins:          i.fecha_ins          ?? null,
          estado:             i.estado             ?? 1,
          canal_venta:        i.canal_venta        ?? null,
          total_pagado:       i.total_pagado       ?? null,
          cuotas_pagadas:     i.cuotas_pagadas     ?? null,
          periodo:            i.periodo            ?? null,
          gestion:            i.gestion            ?? null,
          es_participante:    !!i.es_participante,
        })));
        this.inscritosLoading.set(false);
        this.inscritosLoaded.set(true);
      },
      error: () => {
        this.inscritosLoading.set(false);
        this.toast.error('Error', 'No se pudieron cargar los inscritos.');
      },
    });
  }

  estaSeleccionadoInscrito(id: number): boolean {
    return this.inscritosSeleccionados().has(id);
  }

  toggleSeleccionInscrito(id: number, checked: boolean): void {
    this.inscritosSeleccionados.update(set => {
      const nuevo = new Set(set);
      if (checked) nuevo.add(id); else nuevo.delete(id);
      return nuevo;
    });
  }

  toggleSeleccionarTodosInscritos(checked: boolean): void {
    this.inscritosSeleccionados.set(
      checked ? new Set(this.inscritosFiltrados().filter(i => i.estado === 1 && !i.es_participante).map(i => i.id_ins)) : new Set()
    );
  }

  private marcarInscritosComoParticipanteLocal(ids: number[]): void {
    const set = new Set(ids);
    this.inscritos.update(list => list.map(i => set.has(i.id_ins) ? { ...i, es_participante: true } : i));
  }

  marcarComoParticipante(row: InscriptoRow): void {
    this.marcandoParticipanteId.set(row.id_ins);
    this.http.post(`/api/v1/inscripciones/${row.id_ins}/marcar-participante`, {}).subscribe({
      next: () => {
        this.marcandoParticipanteId.set(null);
        this.toast.success('Participante registrado', `${row.estudiante_nombre ?? 'El estudiante'} ahora es participante.`);
        this.marcarInscritosComoParticipanteLocal([row.id_ins]);
        this.inscritosSeleccionados.update(set => { const s = new Set(set); s.delete(row.id_ins); return s; });
        this.participantesLoaded.set(false);
      },
      error: (err) => {
        this.marcandoParticipanteId.set(null);
        this.toast.error('No se pudo marcar', err?.error?.error ?? 'El inscrito aún no terminó de pagar.');
      },
    });
  }

  marcarSeleccionadosComoParticipantes(): void {
    const ids = Array.from(this.inscritosSeleccionados());
    if (!ids.length) return;

    this.marcandoParticipantesBulk.set(true);
    this.http.post<{ registrados: any[]; omitidos: { id_ins: number; motivo: string }[] }>(
      '/api/v1/inscripciones/marcar-participantes',
      { ids }
    ).subscribe({
      next: (r) => {
        this.marcandoParticipantesBulk.set(false);
        this.inscritosSeleccionados.set(new Set());
        this.participantesLoaded.set(false);
        const omitidosIds = new Set(r.omitidos.map(o => o.id_ins));
        this.marcarInscritosComoParticipanteLocal(ids.filter(id => !omitidosIds.has(id)));
        if (r.omitidos.length) {
          this.toast.error('Algunos no se marcaron', `${r.registrados.length} registrados, ${r.omitidos.length} omitidos (revisa si ya terminaron de pagar).`);
        } else {
          this.toast.success('Participantes registrados', `${r.registrados.length} inscritos ahora son participantes.`);
        }
      },
      error: () => {
        this.marcandoParticipantesBulk.set(false);
        this.toast.error('Error', 'No se pudo procesar la selección.');
      },
    });
  }

  form: FormGroup = this.fb.group({
    nombre_programa:          ['', [Validators.required, Validators.maxLength(200)]],
    slug:                     ['', [Validators.maxLength(300)]],
    descripcion:              [''],
    objetivo:                 [''],
    dirigido:                 [''],
    requisitos:               [''],
    costo_monto:              [null as number | null],
    creditaje:                ['', [Validators.pattern(/^\d+$/)]],
    nota:                     [''],
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
    id_tipoprograma:          [null],
    id_plandoc:               [null as number | null],
    id_imp:                   [null as number | null],
    convenio_id:              [null as number | null],
    vendedor_id:              [null as number | null],
    categoria_web_id:         [null],
    formulario_id:            [null as number | null],
    area_id:                  [null as number | null],
    estado_web:               ['borrador'],
    destacado:                [false],
    orden:                    [0],
    meta_titulo:              ['', [Validators.maxLength(300)]],
    meta_descripcion:         ['', [Validators.maxLength(500)]],
    mensaje_exito:            [''],
    tareas_catalogo_ids:      [[] as number[]],
  });

  ngOnInit(): void {
    this.slug = this.route.snapshot.paramMap.get('slug') ?? '';

    this.cursoService.getCategorias().subscribe({ next: r => this.categorias.set(r.data) });
    this.cursoService.getTipos().subscribe({ next: r => this.tipos.set(r.data) });
    this.cursoService.getPlanesDoc().subscribe({ next: r => this.planesDoc.set(r.data) });
    this.convenioService.getAll$().subscribe({ next: r => this.convenios.set(r), error: () => {} });
    this.vendedorService.getAll({ pageSize: 200 }).subscribe({ next: r => this.vendedores.set(r.data.filter(v => v.usuario_id != null)), error: () => {} });
    this.areaService.getAll({ pageSize: 100 }).subscribe({ next: r => this.areas.set(r.data), error: () => {} });
    this.formularioService.getActivos().subscribe({ next: r => this.formularios.set(r), error: () => {} });
    this.http.get<any[]>('/api/v1/catalogo-tareas').subscribe({
      next: (data) => this.catalogoTareas.set(data.filter(d => d.estado)),
      error: () => {}
    });
    this.http.get<{ data: Imparticion[] }>('/api/v1/imparticiones', {
      params: { pageSize: '200', pageIndex: '1', conInactivos: 'true' }
    }).subscribe({
      next: r => {
        this.imparticiones.set(r.data);
        this.actualizarImparticionActualLabel(this.form.get('id_imp')?.value ?? null);
      },
      error: () => {}
    });

    this.form.get('nombre_programa')!.valueChanges.subscribe((nombre: string) => {
      this.form.get('slug')!.setValue(generateSlug(nombre ?? ''), { emitEvent: false });
    });

    this.cursoService.getBySlug(this.slug).subscribe({
      next: (curso) => {
        this.id = curso.id_programa;
        this.form.patchValue({
          nombre_programa:          curso.nombre_programa,
          slug:                     curso.slug,
          descripcion:              curso.descripcion,
          objetivo:                 curso.objetivo,
          dirigido:                 curso.dirigido,
          requisitos:               curso.requisitos,
          costo_monto:              curso.costo_monto,
          creditaje:                curso.creditaje,
          nota:                     curso.nota,
          foto:                     curso.foto,
          titulo_documento1:        curso.titulo_documento1,
          documento1:               curso.documento1,
          imagen_banner_url:        curso.imagen_banner_url,
          imagen_alt:               curso.imagen_alt,
          url_video:                curso.url_video,
          url_whatsapp:             curso.url_whatsapp,
          url_whatsapp2:            curso.url_whatsapp2,
          imagenes:                 Array.isArray(curso.imagenes) ? curso.imagenes : (typeof curso.imagenes === 'string' ? JSON.parse(curso.imagenes) : []),
          inicio_actividades:       curso.inicio_actividades,
          finalizacion_actividades: curso.finalizacion_actividades,
          inicio_inscripciones:     curso.inicio_inscripciones,
          mes_facturacion:          curso.mes_facturacion,
          tipo_honorario:           curso.tipo_honorario ?? null,
          id_tipoprograma:          curso.id_tipoprograma,
          categoria_web_id:         curso.categoria_web_id,
          formulario_id:            curso.formulario_id ?? null,
          estado_web:               curso.estado_web,
          destacado:                curso.destacado,
          orden:                    curso.orden,
          meta_titulo:              curso.meta_titulo,
          meta_descripcion:         curso.meta_descripcion,
          mensaje_exito:            curso.mensaje_exito,
          id_plandoc:               curso.id_plandoc,
          id_imp:                   curso.id_imp ?? null,
          convenio_id:              (curso as any).convenio_id ?? null,
          vendedor_id:              curso.vendedor_id ?? null,
          area_id:                  (curso as any).area_id ?? null,
          tareas_catalogo_ids:      (curso as any).tareas_catalogo_ids ?? [],
        });
        if (curso.foto) this.imgPreview.set(curso.foto);
        if (curso.documento1) this.pdfName.set(curso.titulo_documento1 ?? curso.documento1);
        this.cursoIdPrograma.set(this.id);
        this.actualizarImparticionActualLabel(curso.id_imp ?? null);
        if (this.esVendedorRestringido) {
          this.form.disable();
          this.activeTab.set('inscritos');
        }
        this.loadingCurso.set(false); this.cargarPlanes();
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el curso'));
        this.router.navigate(['/cenefco/cursos']);
      }
    });
  }

  onImgSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:     this.imgPreview,
      uploading:   this.uploadingImg,
      onSuccess:   (url) => this.form.patchValue({ foto: url }),
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

  toggleEstado(): void {
    const estadoActual  = this.form.get('estado_web')!.value as string;
    const nuevoEstado   = estadoActual === 'publicado' ? 'borrador' : 'publicado';
    const accion        = nuevoEstado === 'publicado' ? 'publicar' : 'cerrar';
    const titulo        = nuevoEstado === 'publicado' ? '¿Publicar curso?' : '¿Cerrar curso?';
    const texto         = nuevoEstado === 'publicado'
      ? 'El curso será visible en el portal y en el chatbot de WhatsApp.'
      : 'El curso dejará de aparecer en el portal y en el chatbot de WhatsApp.';

    Swal.fire({
      title: titulo,
      text: texto,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: nuevoEstado === 'publicado' ? '#16a34a' : '#dc2626',
      cancelButtonColor:  '#6b7280',
      confirmButtonText:  nuevoEstado === 'publicado' ? 'Sí, publicar' : 'Sí, cerrar',
      cancelButtonText:   'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.togglingEstado.set(true);
      this.cursoService.update(this.id, { estado_web: nuevoEstado }).subscribe({
        next: () => {
          this.form.get('estado_web')!.setValue(nuevoEstado, { emitEvent: false });
          this.togglingEstado.set(false);
          this.toast.success(
            nuevoEstado === 'publicado' ? 'Curso publicado' : 'Curso cerrado',
            nuevoEstado === 'publicado'
              ? 'El curso ya es visible en el portal y el chatbot.'
              : 'El curso fue ocultado del portal y el chatbot.'
          );
        },
        error: (err: HttpErrorResponse) => {
          this.togglingEstado.set(false);
          this.toast.error('Error', extractErrorMessage(err, `No se pudo ${accion} el curso`));
        },
      });
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

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.uploadingImg() || this.uploadingPdf()) return;
    for (const [campo, editor] of Object.entries(this.ckEditors)) {
      this.form.get(campo)?.setValue(editor.getData());
    }

    this.submitting.set(true);
    this.cursoService.update(this.id, this.form.value).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Curso actualizado exitosamente');
        this.router.navigate(['/cenefco/cursos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el curso'));
        this.submitting.set(false);
      }
    });
  }
}

