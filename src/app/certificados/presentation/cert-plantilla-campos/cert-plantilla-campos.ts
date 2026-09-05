import {
  Component, inject, signal, OnInit, OnDestroy,
  ElementRef, ViewChild, AfterViewInit, ChangeDetectorRef,
} from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { CertificadoService } from '../../application/services/certificado.service';
import { CertCampo, CertPlantilla } from '../../domain/models/certificado.model';
import { extractErrorMessage } from '../../../utils/http-error';
import Swal from 'sweetalert2';

const CAMPO_VACIO = {
  clave: '', etiqueta: '', tipo: 'texto',
  pos_x_pct: 50, pos_y_pct: 50, ancho_pct: null as number | null,
  tamano_pt: 48, color: '#000000', alineacion: 'center',
  negrita: false, cursiva: false, mayusculas: 'upper',
  valor_fijo: '', activo: true, orden: 0,
};

interface DragState {
  campoId: number;
  offsetX: number;
  offsetY: number;
}

@Component({
  selector: 'app-cert-plantilla-campos',
  imports: [NgIcon, PageTitle, RouterLink],
  templateUrl: './cert-plantilla-campos.html',
  styles: ``
})
export class CertPlantillaCampos implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild('canvasContainer') canvasContainerRef!: ElementRef<HTMLDivElement>;

  private service = inject(CertificadoService);
  private toast   = inject(ToastService);
  private route   = inject(ActivatedRoute);
  private cdr     = inject(ChangeDetectorRef);

  plantillaId = signal(0);
  plantilla   = signal<CertPlantilla | null>(null);
  campos      = signal<CertCampo[]>([]);
  loading     = signal(false);

  selected    = signal<CertCampo | null>(null);
  guardando   = signal(false);
  form        = signal({ ...CAMPO_VACIO });

  showNuevo   = signal(false);
  formNuevo   = signal({ ...CAMPO_VACIO });
  guardandoN  = signal(false);

  imgLoaded   = signal(false);
  imgError    = signal(false);

  showPreview    = signal(false);
  previewUrl     = signal<string | null>(null);
  previewLoading = signal(false);
  private previewBlobUrl: string | null = null;

  readonly clavesSugeridas = [
    { value: 'nombre_participante', label: 'Nombre del participante' },
    { value: 'nombre_completo',     label: 'Nombre completo' },
    { value: 'nombre',              label: 'Nombre (solo)' },
    { value: 'apellidos',           label: 'Apellidos (solo)' },
    { value: 'nombre_programa',     label: 'Nombre del programa' },
    { value: 'programa',            label: 'Programa/curso' },
    { value: 'condicion',           label: 'Condición (APROBADO…)' },
    { value: 'nota_final',          label: 'Nota final' },
    { value: 'nota',                label: 'Nota' },
    { value: 'horas_academicas',    label: 'Horas académicas' },
    { value: 'horas',               label: 'Horas (número)' },
    { value: 'creditaje',           label: 'Creditaje' },
    { value: 'fecha_emision',       label: 'Fecha de emisión' },
    { value: 'fecha_inicio',        label: 'Fecha inicio' },
    { value: 'fecha_fin',           label: 'Fecha fin' },
    { value: 'codigo_verificacion', label: 'Código de verificación' },
    { value: 'codigo',              label: 'Código (corto)' },
    { value: 'ci',                  label: 'Carnet de identidad' },
    { value: 'qr_verificacion',     label: 'QR de verificación (imagen)' },
    { value: 'qr',                  label: 'QR (imagen)' },
    { value: 'texto_certifica',     label: 'Texto "certifica que"' },
    { value: 'texto_intro',         label: 'Texto introductorio' },
    { value: 'texto_firma',         label: 'Texto de firma' },
  ];

  private drag: DragState | null = null;
  private mouseMoveHandler = this.onMouseMove.bind(this);
  private mouseUpHandler   = this.onMouseUp.bind(this);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.plantillaId.set(id);
    this.cargar();
    document.addEventListener('mousemove', this.mouseMoveHandler);
    document.addEventListener('mouseup',   this.mouseUpHandler);
  }

  ngOnDestroy(): void {
    document.removeEventListener('mousemove', this.mouseMoveHandler);
    document.removeEventListener('mouseup',   this.mouseUpHandler);
  }

  ngAfterViewInit(): void {}

  cargar(): void {
    this.loading.set(true);
    this.service.getPlantillaById(this.plantillaId()).subscribe({
      next: (p) => {
        this.plantilla.set(p);
        this.campos.set(p.campos ?? []);
        this.loading.set(false);
        this.imgLoaded.set(false);
        this.imgError.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la plantilla.');
        this.loading.set(false);
      },
    });
  }

  onImgLoad(): void  { this.imgLoaded.set(true); this.imgError.set(false); }
  onImgError(): void { this.imgError.set(true);  this.imgLoaded.set(false); }

  seleccionar(c: CertCampo, event?: MouseEvent): void {
    event?.stopPropagation();
    const fresco = this.campos().find(x => x.id === c.id) ?? c;
    this.selected.set(fresco);
    this.form.set({
      clave: fresco.clave, etiqueta: fresco.etiqueta, tipo: fresco.tipo,
      pos_x_pct: fresco.pos_x_pct, pos_y_pct: fresco.pos_y_pct, ancho_pct: fresco.ancho_pct,
      tamano_pt: fresco.tamano_pt, color: fresco.color, alineacion: fresco.alineacion,
      negrita: fresco.negrita, cursiva: fresco.cursiva, mayusculas: fresco.mayusculas,
      valor_fijo: fresco.valor_fijo ?? '', activo: fresco.activo, orden: fresco.orden,
    });
  }

  deseleccionar(): void {
    if (this.drag) return;
    this.selected.set(null);
  }

  private static readonly VISUAL_FIELDS = new Set([
    'tamano_pt', 'color', 'alineacion', 'negrita', 'cursiva',
    'mayusculas', 'pos_x_pct', 'pos_y_pct', 'ancho_pct', 'activo', 'tipo',
  ]);

  onField(field: string, value: any): void {
    this.form.update(f => ({ ...f, [field]: value }));

    if (field === 'clave' && (value === 'qr' || String(value).toLowerCase().includes('qr'))) {
      this.form.update(f => ({ ...f, tipo: 'imagen' }));
    }

    const sel = this.selected();
    if (sel && CertPlantillaCampos.VISUAL_FIELDS.has(field)) {
      this.campos.update(list =>
        list.map(c => c.id === sel.id ? { ...c, [field]: value } : c)
      );
    }
  }

  startDrag(event: MouseEvent, campo: CertCampo): void {
    event.preventDefault();
    event.stopPropagation();
    this.seleccionar(campo);

    const container = this.canvasContainerRef?.nativeElement;
    if (!container) return;
    const rect = container.getBoundingClientRect();

    const mousePctX = ((event.clientX - rect.left) / rect.width)  * 100;
    const mousePctY = ((event.clientY - rect.top)  / rect.height) * 100;

    this.drag = {
      campoId: campo.id,
      offsetX: mousePctX - campo.pos_x_pct,
      offsetY: mousePctY - campo.pos_y_pct,
    };
  }

  private onMouseMove(event: MouseEvent): void {
    if (!this.drag) return;
    const container = this.canvasContainerRef?.nativeElement;
    if (!container) return;

    const rect = container.getBoundingClientRect();
    const mousePctX = ((event.clientX - rect.left) / rect.width)  * 100;
    const mousePctY = ((event.clientY - rect.top)  / rect.height) * 100;

    const newX = Math.min(100, Math.max(0, mousePctX - this.drag.offsetX));
    const newY = Math.min(100, Math.max(0, mousePctY - this.drag.offsetY));

    this.campos.update(list =>
      list.map(c => c.id === this.drag!.campoId
        ? { ...c, pos_x_pct: +newX.toFixed(2), pos_y_pct: +newY.toFixed(2) }
        : c)
    );
    this.form.update(f => ({ ...f, pos_x_pct: +newX.toFixed(2), pos_y_pct: +newY.toFixed(2) }));
    this.cdr.detectChanges();
  }

  private onMouseUp(_event: MouseEvent): void {
    if (!this.drag) return;
    const campo = this.campos().find(c => c.id === this.drag!.campoId);
    this.drag = null;
    if (!campo) return;

    if (this.selected()?.id === campo.id) {
      this.selected.set(campo);
      this.form.update(f => ({ ...f, pos_x_pct: campo.pos_x_pct, pos_y_pct: campo.pos_y_pct }));
    }

    this.service.updateCampo(campo.id, { pos_x_pct: campo.pos_x_pct, pos_y_pct: campo.pos_y_pct }).subscribe({
      next: () => this.toast.success('Guardado', `"${campo.etiqueta}" reposicionado.`),
      error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err)),
    });
  }

  guardar(): void {
    const sel = this.selected();
    if (!sel) return;
    const f = this.form();
    this.guardando.set(true);
    const payload = {
      ...f,
      plantilla_id: this.plantillaId(),
      etiqueta:  f.etiqueta  || f.clave,
      pos_x_pct: isNaN(f.pos_x_pct) ? sel.pos_x_pct : f.pos_x_pct,
      pos_y_pct: isNaN(f.pos_y_pct) ? sel.pos_y_pct : f.pos_y_pct,
      tamano_pt: isNaN(f.tamano_pt)  ? sel.tamano_pt  : f.tamano_pt,
    };
    this.service.updateCampo(sel.id, payload).subscribe({
      next: (actualizado) => {
        this.guardando.set(false);
        this.toast.success('¡Guardado!', 'Campo actualizado.');
        this.campos.update(list => list.map(c => c.id === sel.id ? actualizado : c));
        this.selected.set(actualizado);
        this.form.set({
          clave: actualizado.clave, etiqueta: actualizado.etiqueta, tipo: actualizado.tipo,
          pos_x_pct: actualizado.pos_x_pct, pos_y_pct: actualizado.pos_y_pct,
          ancho_pct: actualizado.ancho_pct, tamano_pt: actualizado.tamano_pt,
          color: actualizado.color, alineacion: actualizado.alineacion,
          negrita: actualizado.negrita, cursiva: actualizado.cursiva,
          mayusculas: actualizado.mayusculas, valor_fijo: actualizado.valor_fijo ?? '',
          activo: actualizado.activo, orden: actualizado.orden,
        });
      },
      error: (err: HttpErrorResponse) => {
        this.guardando.set(false);
        this.toast.error('Error', extractErrorMessage(err));
      },
    });
  }

  abrirNuevo(): void {
    this.formNuevo.set({ ...CAMPO_VACIO });
    this.showNuevo.set(true);
  }

  onFieldNuevo(field: string, value: any): void {
    this.formNuevo.update(f => ({ ...f, [field]: value }));
    if (field === 'clave' && (value === 'qr' || String(value).toLowerCase().includes('qr'))) {
      this.formNuevo.update(f => ({ ...f, tipo: 'imagen' }));
    }
  }

  guardarNuevo(): void {
    const f = this.formNuevo();
    if (!f.clave) { this.toast.warning('Falta clave', 'Seleccione el campo.'); return; }
    this.guardandoN.set(true);
    const payload = {
      ...f,
      plantilla_id: this.plantillaId(),
      etiqueta:  f.etiqueta  || f.clave,
      pos_x_pct: isNaN(f.pos_x_pct) ? 50 : f.pos_x_pct,
      pos_y_pct: isNaN(f.pos_y_pct) ? 50 : f.pos_y_pct,
      tamano_pt: isNaN(f.tamano_pt)  ? 48 : f.tamano_pt,
    };
    this.service.createCampo(payload).subscribe({
      next: () => {
        this.guardandoN.set(false);
        this.toast.success('Creado', 'Campo agregado.');
        this.showNuevo.set(false);
        this.cargar();
      },
      error: (err: HttpErrorResponse) => {
        this.guardandoN.set(false);
        this.toast.error('Error', extractErrorMessage(err));
      },
    });
  }

  deleteCampo(c: CertCampo): void {
    Swal.fire({
      title: `¿Eliminar "${c.etiqueta}"?`, icon: 'warning',
      showCancelButton: true, confirmButtonColor: '#d33',
      cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.service.deleteCampo(c.id).subscribe({
        next: () => {
          this.toast.success('Eliminado', 'Campo eliminado.');
          if (this.selected()?.id === c.id) this.selected.set(null);
          this.cargar();
        },
        error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err)),
      });
    });
  }

  labelFor(clave: string): string {
    return this.clavesSugeridas.find(k => k.value === clave)?.label ?? clave;
  }

  previewText(c: CertCampo): string {
    const esQr = c.tipo === 'imagen' || c.clave.toLowerCase().includes('qr');
    if (esQr) return '▣ QR';
    if (c.valor_fijo) return this.applyCase(c.valor_fijo, c.mayusculas);

    const nombre  = 'Juan Carlos Pérez Mamani';
    const prog    = 'Diplomado en Gestión Pública y Administración del Estado';
    const hoy     = new Date().toLocaleDateString('es-BO', { day:'2-digit', month:'2-digit', year:'numeric' });
    const map: Record<string, string> = {
      nombre_participante: nombre,  nombre_completo: nombre,
      nombre: 'Juan Carlos',        apellidos: 'Pérez Mamani',
      alumno: nombre,               estudiante: nombre,
      nombre_programa: prog,        programa: prog,
      curso: prog,                  diplomado: prog,
      condicion: 'Aprobado',        nota_final: '87.50',
      nota: '87.50',                calificacion: '87.50',
      horas_academicas: '120 horas académicas',
      horas: '120',                 creditaje: '4',
      fecha_emision: hoy,           fecha: hoy,
      fecha_inicio: '01/03/2025',   fecha_fin: '30/06/2025',
      codigo_verificacion: 'cenefco-2025-A4X9K2',
      codigo: 'cenefco-2025-A4X9K2',
      ci: '7854321',                carnet: '7854321',
      texto_certifica: 'otorga el presente certificado a:',
      texto_intro: 'Por haber completado satisfactoriamente:',
      texto_firma: 'En constancia de lo cual se firma y sella.',
    };
    return this.applyCase(map[c.clave] ?? c.etiqueta ?? c.clave, c.mayusculas);
  }

  private applyCase(txt: string, mayusculas: string): string {
    if (mayusculas === 'upper') return txt.toUpperCase();
    if (mayusculas === 'lower') return txt.toLowerCase();
    if (mayusculas === 'title') return txt.replace(/\w\S*/g, w => w[0].toUpperCase() + w.slice(1).toLowerCase());
    return txt;
  }

  scaledFontSize(c: CertCampo): string {
    const container = this.canvasContainerRef?.nativeElement;
    if (!container) return '12px';
    return Math.max(6, c.tamano_pt) + 'px';
  }

  storageUrl(url: string | null): string {
    if (!url) return '';
    if (url.startsWith('http')) return url;
    if (url.startsWith('/storage')) return url;
    return '/storage' + url;
  }

  abrirPreview(): void {
    this.showPreview.set(true);
    this.previewUrl.set(null);
    this.cargarPreview('jpg');
  }

  cargarPreview(format: 'jpg' | 'pdf'): void {
    this.previewLoading.set(true);

    if (this.previewBlobUrl) {
      URL.revokeObjectURL(this.previewBlobUrl);
      this.previewBlobUrl = null;
    }
    this.previewUrl.set(null);

    this.service.previewPlantilla(this.plantillaId(), format).subscribe({
      next: (blob) => {
        if (format === 'pdf') {
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `preview-plantilla-${this.plantillaId()}.pdf`;
          a.click();
          setTimeout(() => URL.revokeObjectURL(url), 5000);
          this.previewLoading.set(false);
        } else {
          this.previewBlobUrl = URL.createObjectURL(blob);
          this.previewUrl.set(this.previewBlobUrl);
          this.previewLoading.set(false);
        }
        this.cdr.detectChanges();
      },
      error: (err: HttpErrorResponse) => {
        this.previewLoading.set(false);
        this.toast.error('Error', extractErrorMessage(err));
        this.cdr.detectChanges();
      },
    });
  }

  descargarJpg(): void {
    this.previewLoading.set(true);
    this.service.previewPlantilla(this.plantillaId(), 'jpg').subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `certificado-preview-${this.plantillaId()}.jpg`;
        a.click();
        setTimeout(() => URL.revokeObjectURL(url), 5000);
        this.previewLoading.set(false);
        this.cdr.detectChanges();
      },
      error: (err: HttpErrorResponse) => {
        this.previewLoading.set(false);
        this.toast.error('Error', extractErrorMessage(err));
        this.cdr.detectChanges();
      },
    });
  }

  cerrarPreview(): void {
    this.showPreview.set(false);
    if (this.previewBlobUrl) {
      URL.revokeObjectURL(this.previewBlobUrl);
      this.previewBlobUrl = null;
    }
    this.previewUrl.set(null);
  }
}
