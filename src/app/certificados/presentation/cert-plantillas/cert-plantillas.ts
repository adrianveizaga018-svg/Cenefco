import {
  Component, inject, signal, computed,
  ElementRef, ViewChild, AfterViewInit, OnDestroy, ChangeDetectorRef,
} from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { CertificadoService } from '../../application/services/certificado.service';
import { CertPlantilla, CertCampo } from '../../domain/models/certificado.model';
import { CursoService } from '../../../cursos/application/services/curso.service';
import { ProgramaAcademicoService } from '../../../programas-academicos/application/services/programa-academico.service';
import { CertConfigProgramaService } from '../../../cert-config-programas/application/services/cert-config-programa.service';
import { extractErrorMessage } from '../../../utils/http-error';
import Swal from 'sweetalert2';

const CAMPO_VACIO = {
  clave: '', etiqueta: '', tipo: 'texto',
  pos_x_pct: 50, pos_y_pct: 50, ancho_pct: null as number | null,
  tamano_pt: 48, fuente: null as string | null,
  color: '#000000', alineacion: 'center',
  negrita: false, cursiva: false, mayusculas: 'upper',
  valor_fijo: '', activo: true, orden: 0,
};

interface DragState { campoId: number; offsetX: number; offsetY: number; }

@Component({
  selector: 'app-cert-plantillas',
  imports: [NgIcon, PageTitle, RouterLink],
  templateUrl: './cert-plantillas.html',
})
export class CertPlantillas implements AfterViewInit, OnDestroy {
  @ViewChild('canvasRef') canvasRef!: ElementRef<HTMLDivElement>;

  /** Ancho actual del canvas en píxeles DOM (actualizado por ResizeObserver) */
  canvasWidthPx = signal(800);
  private resizeObserver: ResizeObserver | null = null;

  private svc        = inject(CertificadoService);
  private cursoSvc   = inject(CursoService);
  private progSvc    = inject(ProgramaAcademicoService);
  private configSvc  = inject(CertConfigProgramaService);
  private toast      = inject(ToastService);
  private router     = inject(Router);
  private cdr        = inject(ChangeDetectorRef);

  // ── Listas ────────────────────────────────────────────────────────────────
  plantillas    = signal<CertPlantilla[]>([]);
  programas     = signal<any[]>([]);
  versiones     = signal<any[]>([]);
  cargando      = signal(false);

  // ── Selección ─────────────────────────────────────────────────────────────
  /** Plantilla actualmente abierta en el editor */
  plantillaActiva = signal<CertPlantilla | null>(null);
  /** Programa y versión a los que se asignará */
  programaId    = signal<number | null>(null);
  versionId     = signal<number | null>(null);
  guardandoAsig = signal(false);

  /** Asignación actual cargada desde el servidor para esta plantilla */
  asignacionActual = signal<{ programa_id: number; nombre_programa: string; config_id: number; item_id: number } | null>(null);
  cargandoAsig     = signal(false);

  // ── Editor de campos ──────────────────────────────────────────────────────
  campos        = signal<CertCampo[]>([]);
  selected      = signal<CertCampo | null>(null);
  guardando     = signal(false);
  form          = signal({ ...CAMPO_VACIO });
  showNuevo     = signal(false);
  formNuevo     = signal({ ...CAMPO_VACIO });
  guardandoN    = signal(false);
  imgLoaded     = signal(false);
  imgError      = signal(false);

  // ── Formulario nueva/editar plantilla ─────────────────────────────────────
  showFormPlantilla = signal(false);
  editandoPlantilla = signal<CertPlantilla | null>(null);
  subiendo          = signal(false);
  guardandoForm     = signal(false);
  formPlantilla = signal({
    nombre: '', tipo: 'aprobacion', imagen_url: '',
    ancho_px: 3508, alto_px: 2480, orientacion: 'horizontal',
    calidad_jpg: 95, color_default: '#000000', estado: 'activo',
  });

  // ── Preview ───────────────────────────────────────────────────────────────
  showPreview    = signal(false);
  previewUrl     = signal<string | null>(null);
  previewLoading = signal(false);
  private blobUrl: string | null = null;

  // ── Drag ──────────────────────────────────────────────────────────────────
  private drag: DragState | null = null;
  private mmh = this.onMouseMove.bind(this);
  private muh = this.onMouseUp.bind(this);

  // ── Catálogo de fuentes (cargado desde /api/v1/cert-fuentes) ─────────────
  FUENTES = signal<{ label: string; value: string | null; css: string; grupo: string }[]>([
    // fallback local mientras carga
    { label: 'Predeterminada del sistema', value: null,                               css: 'Arial, sans-serif',      grupo: 'sistema'  },
    { label: 'Asap (negrita)',             value: 'assets/fonts/Asap_700.ttf',        css: '"Asap", sans-serif',     grupo: 'proyecto' },
    { label: 'Roboto',                     value: 'assets/fonts/Roboto_regular.ttf',  css: '"Roboto", sans-serif',   grupo: 'proyecto' },
    { label: 'Open Sans',                  value: 'assets/fonts/Open_Sans_regular.ttf', css: '"Open Sans", sans-serif', grupo: 'proyecto' },
    { label: 'Ubuntu',                     value: 'assets/fonts/Ubuntu_regular.ttf',  css: '"Ubuntu", sans-serif',   grupo: 'proyecto' },
    { label: 'Khand (condensada)',         value: 'assets/fonts/Khand_500.ttf',       css: '"Khand", sans-serif',    grupo: 'proyecto' },
    { label: 'ABeeZee (redondeada)',       value: 'assets/fonts/ABeeZee_regular.ttf', css: '"ABeeZee", sans-serif',  grupo: 'proyecto' },
    { label: 'Times New Roman',            value: 'C:/Windows/Fonts/times.ttf',       css: '"Times New Roman", serif', grupo: 'sistema'},
    { label: 'Georgia',                    value: 'C:/Windows/Fonts/georgia.ttf',     css: '"Georgia", serif',       grupo: 'sistema'  },
    { label: 'Verdana',                    value: 'C:/Windows/Fonts/verdana.ttf',     css: '"Verdana", sans-serif',  grupo: 'sistema'  },
    { label: 'Century Gothic',            value: 'C:/Windows/Fonts/GOTHIC.TTF',       css: '"Century Gothic", sans-serif', grupo: 'sistema'},
    { label: 'Palatino Linotype',         value: 'C:/Windows/Fonts/pala.ttf',         css: '"Palatino Linotype", serif',   grupo: 'sistema'},
  ]);

  /** Dado un valor de fuente (ruta) devuelve el css-family para la preview en canvas */
  cssFamilyForFuente(fuente: string | null): string {
    return this.FUENTES().find(f => f.value === fuente)?.css ?? 'Arial, sans-serif';
  }

  readonly clavesSugeridas = [    { value: 'nombre_participante', label: 'Nombre completo del participante' },
    { value: 'nombre',              label: 'Nombre (solo)' },
    { value: 'apellidos',           label: 'Apellidos (solo)' },
    { value: 'nombre_programa',     label: 'Nombre del programa' },
    { value: 'condicion',           label: 'Condición (APROBADO…)' },
    { value: 'nota_final',          label: 'Nota final' },
    { value: 'horas_academicas',    label: 'Horas académicas' },
    { value: 'creditaje',           label: 'Creditaje' },
    { value: 'fecha_emision',       label: 'Fecha de emisión' },
    { value: 'fecha_inicio',        label: 'Fecha inicio' },
    { value: 'fecha_fin',           label: 'Fecha fin' },
    { value: 'codigo_verificacion', label: 'Código de verificación' },
    { value: 'ci',                  label: 'Carnet de identidad' },
    { value: 'qr_verificacion',     label: 'QR de verificación' },
    { value: 'texto_certifica',     label: 'Texto "certifica que"' },
    { value: 'texto_intro',         label: 'Texto introductorio' },
    { value: 'texto_firma',         label: 'Texto de firma' },
  ];

  constructor() {
    this.cargarPlantillas();
    this.cursoSvc.getAll({ pageSize: 200 }).subscribe({ next: r => this.programas.set(r.data) });
    // Cargar catálogo de fuentes desde el servidor (incluye sólo las disponibles en ese OS)
    this.svc.getFuentes().subscribe({ next: f => { if (f.length) this.FUENTES.set(f); } });
  }

  ngAfterViewInit(): void {
    document.addEventListener('mousemove', this.mmh);
    document.addEventListener('mouseup',   this.muh);
    // Observar cambios de tamaño del canvas para escalar correctamente las fuentes
    if (this.canvasRef?.nativeElement) {
      this.resizeObserver = new ResizeObserver(entries => {
        const w = entries[0]?.contentRect?.width;
        if (w && w > 0) { this.canvasWidthPx.set(w); this.cdr.detectChanges(); }
      });
      this.resizeObserver.observe(this.canvasRef.nativeElement);
      const initialW = this.canvasRef.nativeElement.getBoundingClientRect().width;
      if (initialW > 0) this.canvasWidthPx.set(initialW);
    }
  }

  ngOnDestroy(): void {
    document.removeEventListener('mousemove', this.mmh);
    document.removeEventListener('mouseup',   this.muh);
    this.resizeObserver?.disconnect();
    if (this.blobUrl) URL.revokeObjectURL(this.blobUrl);
  }

  // ── Carga ─────────────────────────────────────────────────────────────────

  cargarPlantillas(): void {
    this.cargando.set(true);
    this.svc.getPlantillas({ pageSize: 100 }).subscribe({
      next: r => { this.plantillas.set(r.data); this.cargando.set(false); },
      error: () => this.cargando.set(false),
    });
  }

  private reobservarCanvas(): void {
    // El canvas puede haberse creado después de que ngAfterViewInit corrió
    // (porque está dentro de @if(plantillaActiva())). Lo reconectamos.
    setTimeout(() => {
      const el = this.canvasRef?.nativeElement;
      if (!el) return;
      this.resizeObserver?.disconnect();
      this.resizeObserver = new ResizeObserver(entries => {
        const w = entries[0]?.contentRect?.width;
        if (w && w > 0) { this.canvasWidthPx.set(w); this.cdr.detectChanges(); }
      });
      this.resizeObserver.observe(el);
      const w = el.getBoundingClientRect().width;
      if (w > 0) { this.canvasWidthPx.set(w); this.cdr.detectChanges(); }
    }, 50);
  }

  seleccionarPlantilla(p: CertPlantilla): void {
    this.plantillaActiva.set(p);
    this.selected.set(null);
    this.imgLoaded.set(false);
    this.imgError.set(false);
    this.campos.set([]);
    // Reset asignación
    this.asignacionActual.set(null);
    this.programaId.set(null);
    this.versionId.set(null);
    this.versiones.set([]);

    // Re-observar el canvas para escalar fuentes correctamente
    this.reobservarCanvas();

    // Cargar campos de esta plantilla
    this.svc.getCampos(p.id).subscribe({
      next: r => this.campos.set(r.data ?? (r as any)),
      error: () => this.toast.error('Error', 'No se pudieron cargar los campos.'),
    });

    // Buscar si esta plantilla ya está asignada a algún programa
    this.cargandoAsig.set(true);
    this.configSvc.getAll().subscribe({
      next: ({ data }) => {
        this.cargandoAsig.set(false);
        // Buscar en todos los configs el item que tenga esta plantilla_id
        let encontrado: { programa_id: number; nombre_programa: string; config_id: number; item_id: number } | null = null;
        for (const cfg of data) {
          const item = cfg.items?.find(i => i.plantilla_id === p.id);
          if (item) {
            encontrado = {
              programa_id:    cfg.programa_id,
              nombre_programa: cfg.nombre_programa ?? `Programa #${cfg.programa_id}`,
              config_id:      cfg.id,
              item_id:        item.id,
            };
            break;
          }
        }
        this.asignacionActual.set(encontrado);
        // Precargar el selector de programa con la asignación existente
        if (encontrado) {
          this.programaId.set(encontrado.programa_id);
          this.progSvc.getImparticiones(encontrado.programa_id).subscribe({
            next: r => this.versiones.set(r),
          });
        }
      },
      error: () => this.cargandoAsig.set(false),
    });
  }

  onProgramaChange(e: Event): void {
    const pid = Number((e.target as HTMLSelectElement).value) || null;
    this.programaId.set(pid);
    this.versionId.set(null);
    this.versiones.set([]);
    if (pid) this.progSvc.getImparticiones(pid).subscribe({ next: r => this.versiones.set(r) });
  }

  guardarAsignacion(): void {
    const pid = this.programaId();
    const pl  = this.plantillaActiva();
    if (!pid || !pl) return;
    this.guardandoAsig.set(true);
    const nombreProg = this.programas().find(p => p.id_programa === pid)?.nombre_programa ?? `Programa #${pid}`;

    this.configSvc.upsert({ programa_id: pid, activo: true }).subscribe({
      next: (config) => {
        const itemExistente = config.items?.[0];
        const payload = {
          nombre_cert: pl.nombre,
          precio: 0, es_gratuito: true, orden: 1, activo: true,
          plantilla_id: pl.id,
        };
        const obs = itemExistente
          ? this.configSvc.updateItem(config.id, itemExistente.id, payload)
          : this.configSvc.createItem(config.id, payload);
        obs.subscribe({
          next: (item) => {
            this.guardandoAsig.set(false);
            this.toast.success('¡Asignado!', `"${pl.nombre}" asignada a ${nombreProg}.`);
            // Actualizar el estado local de asignación
            this.asignacionActual.set({
              programa_id:    pid,
              nombre_programa: nombreProg,
              config_id:      config.id,
              item_id:        item.id,
            });
          },
          error: (err: HttpErrorResponse) => {
            this.guardandoAsig.set(false);
            this.toast.error('Error', extractErrorMessage(err));
          },
        });
      },
      error: (err: HttpErrorResponse) => {
        this.guardandoAsig.set(false);
        this.toast.error('Error', extractErrorMessage(err));
      },
    });
  }

  // ── Formulario plantilla ──────────────────────────────────────────────────

  toggleFormPlantilla(): void {
    if (this.showFormPlantilla()) {
      this.showFormPlantilla.set(false);
      this.editandoPlantilla.set(null);
    } else {
      this.editandoPlantilla.set(null);
      this.formPlantilla.set({ nombre: '', tipo: 'aprobacion', imagen_url: '', ancho_px: 3508, alto_px: 2480, orientacion: 'horizontal', calidad_jpg: 95, color_default: '#000000', estado: 'activo' });
      this.showFormPlantilla.set(true);
    }
  }

  toggleNuevoCampo(): void {
    if (this.showNuevo()) {
      this.showNuevo.set(false);
    } else {
      this.formNuevo.set({ ...CAMPO_VACIO });
      this.showNuevo.set(true);
    }
  }

  abrirNuevaPlantilla(): void {
    this.editandoPlantilla.set(null);
    this.formPlantilla.set({ nombre: '', tipo: 'aprobacion', imagen_url: '', ancho_px: 3508, alto_px: 2480, orientacion: 'horizontal', calidad_jpg: 95, color_default: '#000000', estado: 'activo' });
    this.showFormPlantilla.set(true);
  }

  editarPlantilla(p: CertPlantilla, e: Event): void {
    this.formPlantilla.set({ nombre: p.nombre, tipo: p.tipo, imagen_url: p.imagen_url, ancho_px: p.ancho_px, alto_px: p.alto_px, orientacion: p.orientacion, calidad_jpg: p.calidad_jpg, color_default: p.color_default, estado: p.estado });
    this.showFormPlantilla.set(true);
  }

  onInputPlantilla(field: string, value: any): void { this.formPlantilla.update(f => ({ ...f, [field]: value })); }

  subirImagen(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    this.subiendo.set(true);
    this.svc.uploadImagenPlantilla(file).subscribe({
      next: res => { this.formPlantilla.update(f => ({ ...f, imagen_url: res.url })); this.subiendo.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo subir la imagen.'); this.subiendo.set(false); },
    });
  }

  guardarPlantilla(): void {
    const f  = this.formPlantilla();
    const ed = this.editandoPlantilla();
    if (!f.nombre || !f.imagen_url) { this.toast.warning('Faltan datos', 'Nombre e imagen son obligatorios.'); return; }
    this.guardandoForm.set(true);
    const obs = ed ? this.svc.updatePlantilla(ed.id, f) : this.svc.createPlantilla(f);
    obs.subscribe({
      next: (p) => {
        this.guardandoForm.set(false);
        this.toast.success('Guardado', ed ? 'Plantilla actualizada.' : 'Plantilla creada.');
        this.showFormPlantilla.set(false);
        this.cargarPlantillas();
        if (!ed) this.seleccionarPlantilla(p); // abrir el editor con la nueva plantilla
      },
      error: (err: HttpErrorResponse) => {
        this.guardandoForm.set(false);
        this.toast.error('Error', extractErrorMessage(err));
      },
    });
  }

  eliminarPlantilla(p: CertPlantilla, e: Event): void {
    e.stopPropagation();
    Swal.fire({ title: `¿Eliminar "${p.nombre}"?`, icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#dc2626', cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.svc.deletePlantilla(p.id).subscribe({
        next: () => {
          this.toast.success('Eliminada', 'Plantilla eliminada.');
          if (this.plantillaActiva()?.id === p.id) { this.plantillaActiva.set(null); this.campos.set([]); }
          this.cargarPlantillas();
        },
        error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err)),
      });
    });
  }

  // ── Editor campos ─────────────────────────────────────────────────────────

  seleccionarCampo(c: CertCampo, event?: MouseEvent): void {
    event?.stopPropagation();
    const fresco = this.campos().find(x => x.id === c.id) ?? c;
    this.selected.set(fresco);
    this.form.set({
      clave: fresco.clave, etiqueta: fresco.etiqueta, tipo: fresco.tipo,
      pos_x_pct: fresco.pos_x_pct, pos_y_pct: fresco.pos_y_pct, ancho_pct: fresco.ancho_pct,
      tamano_pt: fresco.tamano_pt, fuente: fresco.fuente ?? null,
      color: fresco.color, alineacion: fresco.alineacion,
      negrita: fresco.negrita, cursiva: fresco.cursiva, mayusculas: fresco.mayusculas,
      valor_fijo: fresco.valor_fijo ?? '', activo: fresco.activo, orden: fresco.orden,
    });
  }

  deseleccionar(): void { if (!this.drag) this.selected.set(null); }

  private static readonly VISUAL_FIELDS = new Set([
    'tamano_pt', 'fuente', 'color', 'alineacion', 'negrita', 'cursiva',
    'mayusculas', 'pos_x_pct', 'pos_y_pct', 'ancho_pct', 'activo', 'tipo',
  ]);

  onField(field: string, value: any): void {
    this.form.update(f => ({ ...f, [field]: value }));
    if (field === 'clave' && String(value).toLowerCase().includes('qr'))
      this.form.update(f => ({ ...f, tipo: 'imagen' }));
    const sel = this.selected();
    if (sel && CertPlantillas.VISUAL_FIELDS.has(field))
      this.campos.update(list => list.map(c => c.id === sel.id ? { ...c, [field]: value } : c));
  }

  guardarCampo(): void {
    const sel = this.selected();
    if (!sel) return;
    const f = this.form();
    this.guardando.set(true);
    this.svc.updateCampo(sel.id, {
      ...f,
      fuente: f.fuente ?? null,
      plantilla_id: this.plantillaActiva()!.id,
      etiqueta: f.etiqueta || f.clave,
    }).subscribe({
      next: (actualizado) => {
        this.guardando.set(false);
        this.toast.success('Guardado', 'Campo actualizado.');
        this.campos.update(list => list.map(c => c.id === sel.id ? actualizado : c));
        this.selected.set(actualizado);
      },
      error: (err: HttpErrorResponse) => { this.guardando.set(false); this.toast.error('Error', extractErrorMessage(err)); },
    });
  }

  abrirNuevoCampo(): void { this.formNuevo.set({ ...CAMPO_VACIO }); this.showNuevo.set(true); }
  onFieldNuevo(field: string, value: any): void {
    this.formNuevo.update(f => ({ ...f, [field]: value }));
    if (field === 'clave' && String(value).toLowerCase().includes('qr'))
      this.formNuevo.update(f => ({ ...f, tipo: 'imagen' }));
  }

  guardarNuevoCampo(): void {
    const f = this.formNuevo();
    if (!f.clave) { this.toast.warning('Falta clave', 'Selecciona el campo.'); return; }
    this.guardandoN.set(true);
    this.svc.createCampo({
      ...f,
      fuente: f.fuente ?? null,
      plantilla_id: this.plantillaActiva()!.id,
      etiqueta: f.etiqueta || f.clave,
    }).subscribe({
      next: (nuevo) => {
        this.guardandoN.set(false);
        this.toast.success('Creado', 'Campo agregado.');
        this.showNuevo.set(false);
        this.campos.update(list => [...list, nuevo]);
        this.seleccionarCampo(nuevo);
      },
      error: (err: HttpErrorResponse) => { this.guardandoN.set(false); this.toast.error('Error', extractErrorMessage(err)); },
    });
  }

  eliminarCampo(c: CertCampo, e?: Event): void {
    e?.stopPropagation();
    Swal.fire({ title: `¿Eliminar "${c.etiqueta}"?`, icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#dc2626', cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.svc.deleteCampo(c.id).subscribe({
        next: () => {
          this.toast.success('Eliminado', 'Campo eliminado.');
          if (this.selected()?.id === c.id) this.selected.set(null);
          this.campos.update(list => list.filter(x => x.id !== c.id));
        },
        error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err)),
      });
    });
  }

  // ── Drag & Drop ───────────────────────────────────────────────────────────

  startDrag(event: MouseEvent, campo: CertCampo): void {
    event.preventDefault(); event.stopPropagation();
    this.seleccionarCampo(campo);
    const cont = this.canvasRef?.nativeElement;
    if (!cont) return;
    const rect = cont.getBoundingClientRect();
    this.drag = {
      campoId: campo.id,
      offsetX: ((event.clientX - rect.left) / rect.width)  * 100 - campo.pos_x_pct,
      offsetY: ((event.clientY - rect.top)  / rect.height) * 100 - campo.pos_y_pct,
    };
  }

  private onMouseMove(e: MouseEvent): void {
    if (!this.drag) return;
    const cont = this.canvasRef?.nativeElement;
    if (!cont) return;
    const rect = cont.getBoundingClientRect();
    const x = Math.min(100, Math.max(0, ((e.clientX - rect.left) / rect.width)  * 100 - this.drag.offsetX));
    const y = Math.min(100, Math.max(0, ((e.clientY - rect.top)  / rect.height) * 100 - this.drag.offsetY));
    this.campos.update(list => list.map(c => c.id === this.drag!.campoId ? { ...c, pos_x_pct: +x.toFixed(2), pos_y_pct: +y.toFixed(2) } : c));
    this.form.update(f => ({ ...f, pos_x_pct: +x.toFixed(2), pos_y_pct: +y.toFixed(2) }));
    this.cdr.detectChanges();
  }

  private onMouseUp(_e: MouseEvent): void {
    if (!this.drag) return;
    const campo = this.campos().find(c => c.id === this.drag!.campoId);
    this.drag = null;
    if (!campo) return;
    if (this.selected()?.id === campo.id) { this.selected.set(campo); this.form.update(f => ({ ...f, pos_x_pct: campo.pos_x_pct, pos_y_pct: campo.pos_y_pct })); }
    this.svc.updateCampo(campo.id, { pos_x_pct: campo.pos_x_pct, pos_y_pct: campo.pos_y_pct }).subscribe({ error: () => {} });
  }

  // ── Preview ───────────────────────────────────────────────────────────────

  abrirPreview(): void {
    const pl = this.plantillaActiva();
    if (!pl) return;
    this.showPreview.set(true);
    this.previewUrl.set(null);
    this.previewLoading.set(true);
    this.svc.previewPlantilla(pl.id, 'jpg').subscribe({
      next: (blob) => {
        if (this.blobUrl) URL.revokeObjectURL(this.blobUrl);
        this.blobUrl = URL.createObjectURL(blob);
        this.previewUrl.set(this.blobUrl);
        this.previewLoading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.previewLoading.set(false);
        this.showPreview.set(false);
        this.toast.error('Error', 'No se pudo generar la vista previa.');
      },
    });
  }

  descargarPreview(format: 'jpg' | 'pdf'): void {
    const pl = this.plantillaActiva();
    if (!pl) return;
    this.svc.previewPlantilla(pl.id, format).subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url;
        a.download = `preview-${pl.nombre.toLowerCase().replace(/\s+/g, '-')}.${format}`;
        a.click(); setTimeout(() => URL.revokeObjectURL(url), 5000);
      },
      error: () => this.toast.error('Error', 'No se pudo descargar.'),
    });
  }

  cerrarPreview(): void { this.showPreview.set(false); }

  // ── Helpers ───────────────────────────────────────────────────────────────

  labelFor(clave: string): string { return this.clavesSugeridas.find(k => k.value === clave)?.label ?? clave; }

  storageUrl(url: string | null | undefined): string {
    if (!url) return '';
    if (url.startsWith('http') || url.startsWith('/')) return url;
    return '/' + url;
  }

  previewText(c: CertCampo): string {
    if (c.tipo === 'imagen' || c.clave.toLowerCase().includes('qr')) return '▣ QR';
    if (c.valor_fijo) return this.applyCase(c.valor_fijo, c.mayusculas);
    const hoy = new Date().toLocaleDateString('es-BO', { day:'2-digit', month:'long', year:'numeric' });
    const map: Record<string, string> = {
      nombre_participante: 'Juan Carlos Pérez Mamani', nombre_completo: 'Juan Carlos Pérez Mamani',
      nombre: 'Juan Carlos', apellidos: 'Pérez Mamani',
      nombre_programa: 'Diplomado en Gestión Pública', programa: 'Diplomado en Gestión Pública',
      condicion: 'Aprobado', nota_final: '87.50', nota: '87.50',
      horas_academicas: '120 horas académicas', horas: '120', creditaje: '4',
      fecha_emision: hoy, fecha_inicio: '01/03/2025', fecha_fin: '30/06/2025',
      codigo_verificacion: 'cenefco-2025-A4X9K2', ci: '7854321',
      texto_certifica: 'otorga el presente certificado a:',
      texto_intro: 'Por haber completado satisfactoriamente:',
      texto_firma: 'En constancia de lo cual se firma y sella.',
    };
    return this.applyCase(map[c.clave] ?? c.etiqueta ?? c.clave, c.mayusculas);
  }

  private applyCase(txt: string, m: string): string {
    if (m === 'upper') return txt.toUpperCase();
    if (m === 'lower') return txt.toLowerCase();
    if (m === 'title') return txt.replace(/\w\S*/g, w => w[0].toUpperCase() + w.slice(1).toLowerCase());
    return txt;
  }

  /**
   * Escala tamano_pt (diseñado para la imagen real) al tamaño actual del canvas DOM.
   * Ej: si la imagen mide 3508px y el canvas DOM mide 800px → factor = 800/3508 ≈ 0.228
   * Un campo de 200pt se verá como 200 * 0.228 ≈ 45px en el canvas.
   */
  scaledFontSize(c: CertCampo): string {
    const imgAncho = this.plantillaActiva()?.ancho_px ?? 3508;
    const canvasW  = this.canvasWidthPx();
    const factor   = canvasW > 0 && imgAncho > 0 ? canvasW / imgAncho : 1;
    return Math.max(6, Math.round(c.tamano_pt * factor)) + 'px';
  }

  fontFamily(c: CertCampo): string { return this.cssFamilyForFuente(c.fuente ?? null); }
}
