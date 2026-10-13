import { Component, ElementRef, OnDestroy, OnInit, ViewChild, computed, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { extractErrorMessage } from '../../../utils/http-error';
import {
  ChatService, Conversacion, HiloChat, MensajeChat, Participante, PersonaChat, ResultadoBusqueda,
} from '../../application/services/chat.service';

/** Trozo de texto de un mensaje: texto plano, enlace externo, enlace del sistema o mención. */
type Segmento = { tipo: 'texto' | 'url' | 'ruta' | 'mencion'; texto: string; destino?: string };

const INTERVALO_HILO = 4000;   // mensajes nuevos de la conversación abierta
const INTERVALO_LISTA = 12000; // lista de conversaciones y no leídos
const RUTAS_SISTEMA: [RegExp, string][] = [
  [/^\/cenefco\/inscripcion-detail\/(\d+)$/, 'Inscripción #$1'],
  [/^\/cenefco\/estudiantes\/(\d+)$/, 'Ficha del estudiante #$1'],
  [/^\/cenefco\/campana-seguimiento\/(\d+)$/, 'Campaña #$1'],
  [/^\/crm\/(embudo|tareas|tablero)$/, 'Ventas: $1'],
  [/^\/caja\/[\w-]+$/, 'Caja'],
];

/**
 * Chat interno del personal: directos privados, grupos por área y canal de avisos.
 * Se actualiza solo cada pocos segundos.
 */
@Component({
  selector: 'app-chat',
  standalone: true,
  imports: [NgIcon, FormsModule, DatePipe, RouterLink],
  templateUrl: './chat.html',
  styles: `:host { display: block; }`,
})
export class ChatComponent implements OnInit, OnDestroy {
  private chat   = inject(ChatService);
  private toast  = inject(ToastService);
  private auth   = inject(AuthService);
  private route  = inject(ActivatedRoute);
  private router = inject(Router);

  @ViewChild('hiloScroll') hiloRef?: ElementRef<HTMLDivElement>;
  @ViewChild('caja') cajaRef?: ElementRef<HTMLTextAreaElement>;

  readonly yo = this.auth.currentUser()?.id ?? 0;

  conversaciones = signal<Conversacion[]>([]);
  administra     = signal(false);
  sinConexion    = signal(false);
  directorioListo = signal(false);
  filtro         = signal('');
  activa         = signal<number | null>(null);
  hilo           = signal<HiloChat | null>(null);
  cargandoHilo   = signal(false);
  cargandoMas    = signal(false);

  texto     = signal('');
  archivo   = signal<File | null>(null);
  respondeA = signal<MensajeChat | null>(null);
  menciones = signal<Map<number, string>>(new Map());
  enviando  = signal(false);

  // Sugerencias de @mención
  sugerencias = signal<Participante[]>([]);

  // Búsqueda
  buscando   = signal(false);
  resultados = signal<ResultadoBusqueda[] | null>(null);

  // Diálogos de nuevo mensaje / grupo
  dialogo     = signal<'directo' | 'grupo' | 'gestionar' | null>(null);
  directorio  = signal<PersonaChat[]>([]);
  filtroGente = signal('');
  grupoNombre = signal('');
  grupoDesc   = signal('');
  elegidos    = signal<Set<number>>(new Set());

  private imagenes = new Map<number, string>();
  imagenesListas = signal(0); // fuerza el redibujado cuando llega una imagen
  private timers: ReturnType<typeof setInterval>[] = [];

  readonly lista = computed(() => {
    const f = this.filtro().trim().toLowerCase();
    return f ? this.conversaciones().filter(c => c.nombre.toLowerCase().includes(f)) : this.conversaciones();
  });
  readonly actual = computed(() => this.conversaciones().find(c => c.id === this.activa()) ?? null);
  readonly gente = computed(() => {
    const f = this.filtroGente().trim().toLowerCase();
    return f ? this.directorio().filter(p => p.nombre.toLowerCase().includes(f)) : this.directorio();
  });
  readonly miembrosActuales = computed(() => new Set((this.hilo()?.participantes ?? []).map(p => p.id)));

  /** Mensajes agrupados por día para los separadores de fecha. */
  readonly porDia = computed(() => {
    const grupos: { dia: string; mensajes: MensajeChat[] }[] = [];
    for (const m of this.hilo()?.mensajes ?? []) {
      const dia = m.created_at.slice(0, 10);
      const ultimo = grupos[grupos.length - 1];
      if (ultimo?.dia === dia) ultimo.mensajes.push(m); else grupos.push({ dia, mensajes: [m] });
    }
    return grupos;
  });

  /** En un directo: mi último mensaje que la otra persona ya leyó. */
  readonly vistoHasta = computed(() => {
    const h = this.hilo();
    if (!h || h.conversacion.tipo !== 'directo') return 0;
    return h.participantes.find(p => p.id !== this.yo)?.ultimo_leido_id ?? 0;
  });

  ngOnInit(): void {
    this.cargarLista(true);
    this.timers.push(setInterval(() => this.cargarLista(), INTERVALO_LISTA));
    this.timers.push(setInterval(() => this.traerNuevos(), INTERVALO_HILO));
  }

  ngOnDestroy(): void {
    this.timers.forEach(clearInterval);
    this.imagenes.forEach(url => URL.revokeObjectURL(url));
  }

  // ── Lista ───────────────────────────────────────────────────────────────
  cargarLista(primeraVez = false): void {
    this.chat.conversaciones().subscribe({
      next: (res) => {
        this.sinConexion.set(false);
        this.conversaciones.set(res.data);
        this.administra.set(res.administra);
        if (primeraVez) {
          const pedida = Number(this.route.snapshot.queryParamMap.get('c'));
          const abrir = res.data.find(c => c.id === pedida) ?? res.data[0];
          if (abrir) this.abrir(abrir.id);
        }
      },
      error: (err: HttpErrorResponse) => { this.sinConexion.set(true); if (primeraVez) this.toast.error('No se pudo abrir el chat', extractErrorMessage(err, 'Inténtalo de nuevo.')); },
    });
  }

  abrir(id: number): void {
    if (this.activa() === id && this.hilo()) return;
    this.activa.set(id);
    this.resultados.set(null);
    this.respondeA.set(null);
    this.cargandoHilo.set(true);
    this.router.navigate([], { queryParams: { c: id }, replaceUrl: true });
    this.chat.mensajes(id).subscribe({
      next: (h) => {
        this.hilo.set(h);
        this.cargandoHilo.set(false);
        this.cargarImagenes(h.mensajes);
        this.alFinal();
        this.marcarLeido();
      },
      error: (err: HttpErrorResponse) => {
        this.cargandoHilo.set(false);
        this.toast.error('No se pudo abrir la conversación', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  cargarAnteriores(): void {
    const h = this.hilo();
    if (!h?.mensajes.length) return;
    this.cargandoMas.set(true);
    const alto = this.hiloRef?.nativeElement.scrollHeight ?? 0;
    this.chat.mensajes(h.conversacion.id, { antesDe: h.mensajes[0].id }).subscribe({
      next: (prev) => {
        this.hilo.update(x => x ? { ...x, mensajes: [...prev.mensajes, ...x.mensajes], hay_mas: prev.hay_mas } : x);
        this.cargandoMas.set(false);
        this.cargarImagenes(prev.mensajes);
        // Mantener la vista donde estaba
        setTimeout(() => { const el = this.hiloRef?.nativeElement; if (el) el.scrollTop = el.scrollHeight - alto; });
      },
      error: () => this.cargandoMas.set(false),
    });
  }

  private traerNuevos(): void {
    const h = this.hilo();
    if (!h || this.cargandoHilo()) return;
    const ultimo = h.mensajes[h.mensajes.length - 1]?.id ?? 0;
    this.chat.mensajes(h.conversacion.id, { despuesDe: ultimo || undefined }).subscribe({
      next: (nuevo) => {
        if (this.activa() !== h.conversacion.id) return;
        const ids = new Set(h.mensajes.map(m => m.id));
        const llegaron = nuevo.mensajes.filter(m => !ids.has(m.id));
        const estabaAbajo = this.estaAbajo();
        this.hilo.update(x => x ? { ...x, mensajes: ultimo ? [...x.mensajes, ...llegaron] : nuevo.mensajes, participantes: nuevo.participantes } : x);
        if (llegaron.length) {
          this.cargarImagenes(llegaron);
          if (estabaAbajo) this.alFinal();
          this.marcarLeido();
        }
      },
      error: () => {},
    });
  }

  private marcarLeido(): void {
    const h = this.hilo();
    const ultimo = h?.mensajes[h.mensajes.length - 1]?.id;
    if (!h || !ultimo || document.hidden) return;
    this.chat.marcarLeido(h.conversacion.id, ultimo).subscribe({
      next: () => this.conversaciones.update(cs => cs.map(c => c.id === h.conversacion.id ? { ...c, no_leidos: 0 } : c)),
      error: () => {},
    });
  }

  // ── Escribir ────────────────────────────────────────────────────────────
  alEscribir(valor: string): void {
    this.texto.set(valor);
    // @ al final de lo escrito: sugerir participantes
    const m = /(^|\s)@([\p{L}\d]*)$/u.exec(valor);
    if (m && this.hilo()?.conversacion.tipo !== 'directo') {
      const f = m[2].toLowerCase();
      this.sugerencias.set((this.hilo()?.participantes ?? []).filter(p => p.id !== this.yo && p.nombre.toLowerCase().includes(f)).slice(0, 6));
    } else {
      this.sugerencias.set([]);
    }
  }

  mencionar(p: Participante): void {
    const primerNombre = p.nombre.split(' ')[0];
    this.texto.update(t => t.replace(/@([\p{L}\d]*)$/u, '@' + primerNombre + ' '));
    this.menciones.update(m => new Map(m).set(p.id, primerNombre));
    this.sugerencias.set([]);
    this.cajaRef?.nativeElement.focus();
  }

  teclear(ev: KeyboardEvent): void {
    if (ev.key === 'Enter' && !ev.shiftKey) {
      ev.preventDefault();
      if (this.sugerencias().length) { this.mencionar(this.sugerencias()[0]); return; }
      this.enviar();
    } else if (ev.key === 'Escape') {
      this.sugerencias.set([]); this.respondeA.set(null);
    }
  }

  onArchivo(ev: Event): void {
    const f = (ev.target as HTMLInputElement).files?.[0] ?? null;
    if (f && f.size > 15 * 1024 * 1024) { this.toast.warning('Archivo muy grande', 'El máximo es 15 MB.'); return; }
    this.archivo.set(f);
    (ev.target as HTMLInputElement).value = '';
  }

  enviar(): void {
    const h = this.hilo();
    const texto = this.texto().trim();
    if (!h || (!texto && !this.archivo()) || this.enviando()) return;

    const fd = new FormData();
    if (texto) fd.append('cuerpo', texto);
    if (this.archivo()) fd.append('adjunto', this.archivo()!);
    if (this.respondeA()) fd.append('responde_a_id', String(this.respondeA()!.id));
    // Solo cuenta la mención si su nombre sigue en el texto
    for (const [id, nombre] of this.menciones()) if (texto.includes('@' + nombre)) fd.append('menciones[]', String(id));

    this.enviando.set(true);
    this.chat.enviar(h.conversacion.id, fd).subscribe({
      next: (m) => {
        this.enviando.set(false);
        this.texto.set(''); this.archivo.set(null); this.respondeA.set(null); this.menciones.set(new Map());
        this.hilo.update(x => x && !x.mensajes.some(y => y.id === m.id) ? { ...x, mensajes: [...x.mensajes, m] } : x);
        this.cargarImagenes([m]);
        this.alFinal();
        this.cargarLista();
      },
      error: (err: HttpErrorResponse) => {
        this.enviando.set(false);
        this.toast.error('No se pudo enviar', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  async eliminar(m: MensajeChat): Promise<void> {
    const ok = await Swal.fire({ icon: 'warning', title: '¿Eliminar este mensaje?', text: 'Todos verán "Mensaje eliminado".',
      showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', confirmButtonColor: '#dc2626' });
    if (!ok.isConfirmed) return;
    this.chat.eliminar(m.id).subscribe({
      next: () => this.hilo.update(x => x ? { ...x, mensajes: x.mensajes.map(y => y.id === m.id ? { ...y, eliminado: true, cuerpo: null, adjunto: null } : y) } : x),
      error: (err: HttpErrorResponse) => this.toast.error('No se pudo eliminar', extractErrorMessage(err, 'Inténtalo de nuevo.')),
    });
  }

  // ── Adjuntos ────────────────────────────────────────────────────────────
  imagen(id: number): string | null {
    this.imagenesListas();
    return this.imagenes.get(id) ?? null;
  }

  private cargarImagenes(mensajes: MensajeChat[]): void {
    for (const m of mensajes) {
      if (!m.adjunto?.es_imagen || this.imagenes.has(m.id)) continue;
      this.imagenes.set(m.id, '');
      this.chat.adjunto(m.id).subscribe({
        next: (blob) => { this.imagenes.set(m.id, URL.createObjectURL(blob)); this.imagenesListas.update(n => n + 1); if (this.estaAbajo(200)) this.alFinal(); },
        error: () => this.imagenes.delete(m.id),
      });
    }
  }

  descargar(m: MensajeChat): void {
    this.chat.adjunto(m.id).subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        Object.assign(document.createElement('a'), { href: url, download: m.adjunto?.nombre ?? 'archivo' }).click();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
      },
      error: () => this.toast.error('No se pudo descargar', 'El archivo ya no está disponible.'),
    });
  }

  peso(bytes: number): string {
    return bytes > 1024 * 1024 ? (bytes / 1024 / 1024).toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
  }

  // ── Texto con enlaces y menciones ───────────────────────────────────────
  segmentos(texto: string | null): Segmento[] {
    if (!texto) return [];
    const partes: Segmento[] = [];
    const patron = /(https?:\/\/[^\s]+)|(\/(?:cenefco|crm|caja)\/[\w\-/]+)|(@[\p{L}\d]+)/gu;
    let ultimo = 0;
    for (const m of texto.matchAll(patron)) {
      if (m.index! > ultimo) partes.push({ tipo: 'texto', texto: texto.slice(ultimo, m.index) });
      if (m[1]) {
        const ruta = this.rutaInterna(m[1]);
        partes.push(ruta ? { tipo: 'ruta', texto: this.etiquetaRuta(ruta), destino: ruta } : { tipo: 'url', texto: m[1], destino: m[1] });
      } else if (m[2]) {
        partes.push({ tipo: 'ruta', texto: this.etiquetaRuta(m[2]), destino: m[2] });
      } else {
        partes.push({ tipo: 'mencion', texto: m[3] });
      }
      ultimo = m.index! + m[0].length;
    }
    if (ultimo < texto.length) partes.push({ tipo: 'texto', texto: texto.slice(ultimo) });
    return partes;
  }

  /** Un enlace al propio panel (copiado de la barra del navegador) se muestra como tarjeta interna. */
  private rutaInterna(url: string): string | null {
    try {
      const u = new URL(url);
      return u.origin === window.location.origin && /^\/(cenefco|crm|caja)\//.test(u.pathname) ? u.pathname : null;
    } catch { return null; }
  }

  private etiquetaRuta(ruta: string): string {
    for (const [re, etiqueta] of RUTAS_SISTEMA) if (re.test(ruta)) return ruta.replace(re, etiqueta);
    return ruta;
  }

  // ── Búsqueda ────────────────────────────────────────────────────────────
  buscar(): void {
    const q = this.filtro().trim();
    if (q.length < 2) { this.resultados.set(null); return; }
    this.buscando.set(true);
    this.chat.buscar(q).subscribe({
      next: (res) => { this.resultados.set(res.data); this.buscando.set(false); },
      error: () => this.buscando.set(false),
    });
  }

  // ── Nuevo directo / grupo ───────────────────────────────────────────────
  abrirDialogo(tipo: 'directo' | 'grupo' | 'gestionar'): void {
    this.dialogo.set(tipo);
    this.filtroGente.set('');
    this.elegidos.set(new Set());
    const conv = this.actual();
    this.grupoNombre.set(tipo === 'gestionar' ? conv?.nombre ?? '' : '');
    this.grupoDesc.set(tipo === 'gestionar' ? conv?.descripcion ?? '' : '');
    this.directorioListo.set(false);
    this.chat.directorio().subscribe({ next: d => { this.directorio.set(d); this.directorioListo.set(true); }, error: () => this.directorioListo.set(true) });
  }

  escribirA(p: PersonaChat): void {
    this.chat.abrirDirecto(p.id).subscribe({
      next: (res) => { this.dialogo.set(null); this.hilo.set(null); this.activa.set(null); this.cargarLista(); this.abrir(res.id); },
      error: (err: HttpErrorResponse) => this.toast.error('No se pudo abrir', extractErrorMessage(err, 'Inténtalo de nuevo.')),
    });
  }

  alternar(id: number): void {
    this.elegidos.update(s => { const n = new Set(s); if (n.has(id)) { n.delete(id); } else { n.add(id); } return n; });
  }

  crearGrupo(): void {
    if (!this.grupoNombre().trim()) { this.toast.warning('Falta el nombre', 'Ponle un nombre al grupo.'); return; }
    this.chat.crearGrupo(this.grupoNombre().trim(), this.grupoDesc().trim() || null, [...this.elegidos()]).subscribe({
      next: (res) => { this.dialogo.set(null); this.toast.success('Grupo creado', this.grupoNombre()); this.cargarLista(); this.hilo.set(null); this.activa.set(null); this.abrir(res.id); },
      error: (err: HttpErrorResponse) => this.toast.error('No se pudo crear', extractErrorMessage(err, 'Inténtalo de nuevo.')),
    });
  }

  guardarGrupo(): void {
    const conv = this.actual();
    if (!conv) return;
    this.chat.actualizarGrupo(conv.id, { nombre: this.grupoNombre().trim(), descripcion: this.grupoDesc().trim() || null, agregar: [...this.elegidos()] }).subscribe({
      next: () => { this.dialogo.set(null); this.toast.success('Grupo actualizado', ''); this.cargarLista(); this.hilo.set(null); this.abrir(conv.id); },
      error: (err: HttpErrorResponse) => this.toast.error('No se pudo guardar', extractErrorMessage(err, 'Inténtalo de nuevo.')),
    });
  }

  quitarMiembro(p: Participante): void {
    const conv = this.actual();
    if (!conv) return;
    this.chat.actualizarGrupo(conv.id, { quitar: [p.id] }).subscribe({
      next: () => this.hilo.update(x => x ? { ...x, participantes: x.participantes.filter(y => y.id !== p.id) } : x),
      error: (err: HttpErrorResponse) => this.toast.error('No se pudo quitar', extractErrorMessage(err, 'Inténtalo de nuevo.')),
    });
  }

  // ── Utilidades ──────────────────────────────────────────────────────────
  /** "Hoy", "Ayer" o "martes 13 de octubre" para el separador de días. */
  diaTexto(dia: string): string {
    const fecha = new Date(dia + 'T12:00:00');
    const hoy = new Date(); hoy.setHours(12, 0, 0, 0);
    const dias = Math.round((hoy.getTime() - fecha.getTime()) / 86400000);
    if (dias === 0) return 'Hoy';
    if (dias === 1) return 'Ayer';
    return fecha.toLocaleDateString('es-BO', { weekday: 'long', day: 'numeric', month: 'long', ...(dias > 300 ? { year: 'numeric' } : {}) });
  }

  iniciales(nombre: string): string {
    return nombre.split(' ').filter(Boolean).slice(0, 2).map(p => p[0]).join('').toUpperCase() || '?';
  }

  private estaAbajo(margen = 80): boolean {
    const el = this.hiloRef?.nativeElement;
    return !el || el.scrollHeight - el.scrollTop - el.clientHeight < margen;
  }

  private alFinal(): void {
    setTimeout(() => { const el = this.hiloRef?.nativeElement; if (el) el.scrollTop = el.scrollHeight; });
  }
}
