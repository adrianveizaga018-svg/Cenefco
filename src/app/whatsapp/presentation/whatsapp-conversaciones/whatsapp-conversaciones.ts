import { Component, inject, signal, OnInit } from '@angular/core';
import { DatePipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { WhatsappService } from '../../../whatsapp/application/services/whatsapp.service';
import { WhatsappConversacion, WhatsappEtiqueta } from '../../../whatsapp/domain/models/whatsapp.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { WhatsappBotBanner } from '../../components/whatsapp-bot-banner/whatsapp-bot-banner';

@Component({
  selector: 'app-whatsapp-conversaciones',
  imports: [DatePipe, RouterLink, NgIcon, FormsModule, PageTitle, WhatsappBotBanner],
  templateUrl: './whatsapp-conversaciones.html',
  styles: ``
})
export class WhatsappConversaciones implements OnInit {
  private service = inject(WhatsappService);
  private toast   = inject(ToastService);

  conversaciones = signal<WhatsappConversacion[]>([]);
  total          = signal(0);
  loading        = signal(true);
  enviando       = signal(false);

  etiquetas        = signal<WhatsappEtiqueta[]>([]);
  etiquetaFiltroId = '';

  showModalEtiquetas = false;
  convSeleccionadaId: number | null = null;
  etiquetasConv: number[] = [];
  guardandoEtiquetas = false;

  showModalNuevaEtiqueta = false;
  etiquetaEditId: number | null = null;
  etiquetaNombre = '';
  etiquetaColor  = '#6366f1';
  guardandoEtiqueta = false;

  pageIndex    = 1;
  pageSize     = 15;
  query        = '';
  estadoFiltro = '';

  seleccionados = new Set<number>();
  mensajeEnvio  = '';
  archivoAdjunto: File | null = null;
  captionAdjunto = '';
  tipoAdjunto: 'image' | 'document' = 'image';

  readonly estados = [
    { value: '',                        label: 'Todos' },
    { value: 'soporte',                 label: '🔴 Soporte' },
    { value: 'menu',                    label: 'Menú' },
    { value: 'inscripcion_nombre',      label: 'Inscripción — Nombre' },
    { value: 'inscripcion_ci',          label: 'Inscripción — CI' },
    { value: 'inscripcion_email',       label: 'Inscripción — Email' },
    { value: 'inscripcion_confirmar',   label: 'Inscripción — Confirmar' },
    { value: 'consulta_ci',             label: 'Consulta CI' },
  ];

  readonly coloresDisponibles = [
    '#6366f1', '#8b5cf6', '#ec4899', '#ef4444',
    '#f97316', '#eab308', '#22c55e', '#14b8a6',
    '#3b82f6', '#06b6d4', '#64748b', '#78716c',
  ];

  ngOnInit(): void {
    this.load();
    this.cargarEtiquetas();
  }

  load(): void {
    this.loading.set(true);
    this.seleccionados.clear();
    this.service.getConversaciones({
      pageIndex:   this.pageIndex,
      pageSize:    this.pageSize,
      query:       this.query,
      estado:      this.estadoFiltro,
      etiqueta_id: this.etiquetaFiltroId || undefined,
    }).subscribe({
      next: res => {
        this.conversaciones.set(res.data);
        this.total.set(res.total);
        this.loading.set(false);
      },
      error: () => this.loading.set(false),
    });
  }

  cargarEtiquetas(): void {
    this.service.getEtiquetas().subscribe({
      next: res => this.etiquetas.set(res.data),
    });
  }

  search(): void { this.pageIndex = 1; this.load(); }

  changePage(page: number): void { this.pageIndex = page; this.load(); }

  get totalPages(): number {
    return Math.ceil(this.total() / this.pageSize);
  }

  toggleSeleccion(id: number): void {
    if (this.seleccionados.has(id)) {
      this.seleccionados.delete(id);
    } else {
      this.seleccionados.add(id);
    }
  }

  toggleTodos(): void {
    if (this.todosSeleccionados) {
      this.seleccionados.clear();
    } else {
      this.conversaciones().forEach(c => this.seleccionados.add(c.id));
    }
  }

  get todosSeleccionados(): boolean {
    const convs = this.conversaciones();
    return convs.length > 0 && convs.every(c => this.seleccionados.has(c.id));
  }

  get algunoSeleccionado(): boolean { return this.seleccionados.size > 0; }

  get phonesSeleccionados(): string[] {
    return this.conversaciones()
      .filter(c => this.seleccionados.has(c.id))
      .map(c => c.phone);
  }

  onArchivoSeleccionado(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;
    this.archivoAdjunto = file;
    if (file) {
      this.tipoAdjunto = file.type === 'application/pdf' ? 'document' : 'image';
    }
  }

  limpiarAdjunto(): void {
    this.archivoAdjunto = null;
    this.captionAdjunto = '';
  }

  enviarASeleccionados(): void {
    const phones = this.phonesSeleccionados;
    this.enviando.set(true);

    if (this.archivoAdjunto) {
      this.service.enviarMedia(phones, this.tipoAdjunto, this.archivoAdjunto, this.captionAdjunto).subscribe({
        next: (res) => { this.onEnvioOk(res.exitosos); this.limpiarAdjunto(); },
        error: () => { this.toast.error('Error', 'No se pudo enviar el archivo.'); this.enviando.set(false); },
      });
      return;
    }

    if (!this.mensajeEnvio.trim()) {
      this.toast.warning('Atención', 'Escribe un mensaje o adjunta un archivo.');
      this.enviando.set(false);
      return;
    }

    if (phones.length === 1) {
      this.service.enviar(phones[0], this.mensajeEnvio).subscribe({
        next: () => { this.onEnvioOk(1); },
        error: () => { this.toast.error('Error', 'No se pudo enviar el mensaje.'); this.enviando.set(false); },
      });
    } else {
      this.service.enviarMasivo(phones, this.mensajeEnvio).subscribe({
        next: (res) => { this.onEnvioOk(res.exitosos); },
        error: () => { this.toast.error('Error', 'No se pudo enviar el mensaje.'); this.enviando.set(false); },
      });
    }
  }

  private onEnvioOk(exitosos: number): void {
    this.toast.success('Enviado', `Mensaje enviado a ${exitosos} contacto(s).`);
    this.mensajeEnvio = '';
    this.seleccionados.clear();
    this.enviando.set(false);
  }

  abrirModalEtiquetas(conv: WhatsappConversacion): void {
    this.convSeleccionadaId = conv.id;
    this.etiquetasConv = conv.etiquetas?.map(e => e.id) ?? [];
    this.showModalEtiquetas = true;
  }

  cerrarModalEtiquetas(): void {
    this.showModalEtiquetas = false;
    this.convSeleccionadaId = null;
  }

  toggleEtiquetaConv(id: number): void {
    const idx = this.etiquetasConv.indexOf(id);
    if (idx === -1) {
      this.etiquetasConv.push(id);
    } else {
      this.etiquetasConv.splice(idx, 1);
    }
  }

  guardarEtiquetasConv(): void {
    if (!this.convSeleccionadaId) return;
    this.guardandoEtiquetas = true;
    this.service.asignarEtiquetas(this.convSeleccionadaId, this.etiquetasConv).subscribe({
      next: (res) => {
        const id = this.convSeleccionadaId!;
        this.conversaciones.update(convs =>
          convs.map(c => c.id === id ? { ...c, etiquetas: res.etiquetas } : c)
        );
        this.guardandoEtiquetas = false;
        this.cerrarModalEtiquetas();
        this.toast.success('Etiquetas', 'Etiquetas actualizadas.');
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron guardar las etiquetas.');
        this.guardandoEtiquetas = false;
      },
    });
  }

  abrirNuevaEtiqueta(): void {
    this.etiquetaEditId = null;
    this.etiquetaNombre = '';
    this.etiquetaColor  = '#6366f1';
    this.showModalNuevaEtiqueta = true;
  }

  abrirEditarEtiqueta(e: WhatsappEtiqueta): void {
    this.etiquetaEditId = e.id;
    this.etiquetaNombre = e.nombre;
    this.etiquetaColor  = e.color;
    this.showModalNuevaEtiqueta = true;
  }

  cerrarModalEtiqueta(): void {
    this.showModalNuevaEtiqueta = false;
  }

  guardarEtiqueta(): void {
    if (!this.etiquetaNombre.trim()) return;
    this.guardandoEtiqueta = true;

    const obs = this.etiquetaEditId
      ? this.service.actualizarEtiqueta(this.etiquetaEditId, this.etiquetaNombre, this.etiquetaColor)
      : this.service.crearEtiqueta(this.etiquetaNombre, this.etiquetaColor);

    obs.subscribe({
      next: () => {
        this.cargarEtiquetas();
        this.guardandoEtiqueta = false;
        this.cerrarModalEtiqueta();
        this.toast.success('Etiqueta', this.etiquetaEditId ? 'Etiqueta actualizada.' : 'Etiqueta creada.');
      },
      error: () => {
        this.toast.error('Error', 'No se pudo guardar la etiqueta.');
        this.guardandoEtiqueta = false;
      },
    });
  }

  eliminarEtiqueta(e: WhatsappEtiqueta): void {
    if (!confirm(`¿Eliminar la etiqueta "${e.nombre}"?`)) return;
    this.service.eliminarEtiqueta(e.id).subscribe({
      next: () => {
        this.cargarEtiquetas();
        this.toast.success('Etiqueta', 'Etiqueta eliminada.');
      },
      error: () => this.toast.error('Error', 'No se pudo eliminar la etiqueta.'),
    });
  }

  badgeClass(estado: string): string {
    const map: Record<string, string> = {
      menu:                    'bg-default-100 text-default-600',
      inscripcion_nombre:      'bg-blue-100 text-blue-600',
      inscripcion_ci:          'bg-blue-100 text-blue-600',
      inscripcion_email:       'bg-blue-100 text-blue-600',
      inscripcion_confirmar:   'bg-blue-100 text-blue-600',
      consulta_ci:             'bg-violet-100 text-violet-600',
      soporte:                 'bg-danger/15 text-danger font-semibold',
    };
    return map[estado] ?? 'bg-default-100 text-default-500';
  }

  estadoLabel(estado: string): string {
    const map: Record<string, string> = {
      menu:                    'Menú',
      inscripcion_nombre:      'Inscripción',
      inscripcion_ci:          'Inscripción',
      inscripcion_email:       'Inscripción',
      inscripcion_confirmar:   'Inscripción',
      consulta_ci:             'Consulta CI',
      soporte:                 '🔴 Soporte',
    };
    return map[estado] ?? estado;
  }

  contextoResumen(ctx: Record<string, any> | null): string {
    if (!ctx) return '';
    if (ctx['ins_programa_nombre']) return `📚 ${ctx['ins_programa_nombre']}`;
    if (ctx['ins_nombre'])          return `👤 ${ctx['ins_nombre']}`;
    if (ctx['ins_programa_id'])     return `Prog. #${ctx['ins_programa_id']}`;
    return '';
  }

  formatPhone(conv: { phone: string; phone_display: string | null }): string {
    if (conv.phone_display) return conv.phone_display;

    const digits = conv.phone.split('@')[0].split(':')[0].replace(/\D/g, '');
    if (digits.length > 10) return `+${digits}`;
    return digits;
  }
}
