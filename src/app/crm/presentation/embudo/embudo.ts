import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { CdkDrag, CdkDragDrop, CdkDropList, CdkDropListGroup } from '@angular/cdk/drag-drop';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { extractErrorMessage } from '../../../utils/http-error';
import {
  ContactoCrm, CrmService, DetalleLead, ETAPAS, Etapa, LeadCrm, ORIGENES, Origen, VendedorCrm,
} from '../../application/services/crm.service';

/**
 * Embudo de ventas. Se arrastra al interesado entre etapas; "Inscrito" lo marca solo
 * la inscripción real (Caja, portal o su panel). Al abrir uno se registra el contacto.
 */
@Component({
  selector: 'app-embudo-crm',
  standalone: true,
  imports: [PageTitle, NgIcon, FormsModule, DatePipe, RouterLink, CdkDropListGroup, CdkDropList, CdkDrag],
  templateUrl: './embudo.html',
  styles: `
    .cdk-drag-preview { box-shadow: 0 8px 24px rgb(0 0 0 / 0.18); border-radius: 0.5rem; }
    .cdk-drag-placeholder { opacity: 0.35; }
    .cdk-drop-list-dragging .cdk-drag { transition: transform 200ms cubic-bezier(0, 0, 0.2, 1); }
  `,
})
export class EmbudoCrmComponent implements OnInit {
  private crm   = inject(CrmService);
  private toast = inject(ToastService);
  private auth  = inject(AuthService);

  readonly etapas = ETAPAS;
  readonly origenes = Object.entries(ORIGENES) as [Origen, string][];
  readonly supervisa = this.auth.hasPermission('crm.supervisar');

  leads      = signal<LeadCrm[]>([]);
  vendedores = signal<VendedorCrm[]>([]);
  cargando   = signal(true);
  q          = signal('');
  vendedor   = signal('');
  origen     = signal('');

  detalle      = signal<DetalleLead | null>(null);
  cargandoFicha = signal(false);
  guardando    = signal(false);
  contacto     = signal<ContactoCrm>(this.contactoVacio());

  readonly columnas = computed(() => this.etapas.map(e => ({ ...e, leads: this.leads().filter(l => l.estado === e.valor) })));
  readonly sinAtender = computed(() => this.leads().filter(l => l.sin_atender).length);

  ngOnInit(): void {
    this.cargar();
    this.crm.vendedores().subscribe({ next: v => this.vendedores.set(v), error: () => {} });
  }

  cargar(): void {
    this.cargando.set(true);
    this.crm.leads({ q: this.q(), vendedor_id: this.vendedor(), origen: this.origen() }).subscribe({
      next: (res) => { this.leads.set(res.data); this.cargando.set(false); },
      error: (err: HttpErrorResponse) => {
        this.cargando.set(false);
        this.toast.error('No se pudo cargar el embudo', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  nombreOrigen(o: Origen): string { return ORIGENES[o] ?? o; }

  /** Enlace para escribirle por WhatsApp desde el número del vendedor. */
  whatsapp(celular: string | null): string | null {
    const d = (celular ?? '').replace(/\D+/g, '');
    if (!d) return null;
    return `https://wa.me/${d.length === 8 ? '591' + d : d}`;
  }

  async alSoltar(ev: CdkDragDrop<Etapa, Etapa, LeadCrm>): Promise<void> {
    const lead = ev.item.data;
    const destino = ev.container.data;
    if (lead.estado === destino) return;

    if (destino === 'inscrito') {
      this.toast.warning('Se marca solo', 'Pasa a "Inscrito" cuando se registra su inscripción en Caja, el portal o su panel.');
      return;
    }
    let motivo: string | null = null;
    if (destino === 'perdido') {
      const r = await Swal.fire({
        title: '¿Por qué se perdió?', input: 'select', inputPlaceholder: 'Elige el motivo',
        inputOptions: { 'Precio': 'Precio', 'Horario': 'Horario', 'Eligió otra institución': 'Eligió otra institución', 'No responde': 'No responde', 'Ya no le interesa': 'Ya no le interesa', 'Otro': 'Otro' },
        inputValidator: v => (!v ? 'Elige un motivo.' : null),
        showCancelButton: true, confirmButtonText: 'Marcar perdido', cancelButtonText: 'Cancelar', confirmButtonColor: '#64748b',
      });
      if (!r.isConfirmed) return;
      motivo = r.value as string;
    }

    const anterior = lead.estado;
    this.leads.update(ls => ls.map(l => l.id === lead.id ? { ...l, estado: destino, motivo_perdida: motivo } : l));
    this.crm.moverEtapa(lead.id, destino, motivo).subscribe({
      error: (err: HttpErrorResponse) => {
        this.leads.update(ls => ls.map(l => l.id === lead.id ? { ...l, estado: anterior } : l));
        this.toast.error('No se pudo mover', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  abrir(lead: LeadCrm): void {
    this.cargandoFicha.set(true);
    this.contacto.set(this.contactoVacio());
    this.detalle.set({ lead, seguimientos: [], inscripciones: [] });
    this.crm.detalle(lead.id).subscribe({
      next: (d) => { this.detalle.set(d); this.cargandoFicha.set(false); },
      error: (err: HttpErrorResponse) => {
        this.cargandoFicha.set(false);
        this.toast.error('No se pudo abrir', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  cerrarFicha(): void { this.detalle.set(null); }

  actualizarContacto<K extends keyof ContactoCrm>(campo: K, valor: ContactoCrm[K]): void {
    this.contacto.update(c => ({ ...c, [campo]: valor }));
  }

  guardarContacto(): void {
    const d = this.detalle();
    if (!d) return;
    this.guardando.set(true);
    this.crm.registrarContacto(d.lead.id, this.contacto()).subscribe({
      next: () => {
        this.guardando.set(false);
        this.toast.success('Contacto registrado', d.lead.nombre);
        this.cargar();
        this.abrir(d.lead);
      },
      error: (err: HttpErrorResponse) => {
        this.guardando.set(false);
        this.toast.error('No se pudo registrar', extractErrorMessage(err, 'Revisa los datos.'));
      },
    });
  }

  reasignar(vendedorId: string): void {
    const d = this.detalle();
    if (!d || !vendedorId) return;
    this.crm.asignar(d.lead.id, Number(vendedorId)).subscribe({
      next: () => { this.toast.success('Reasignado', d.lead.nombre); this.cargar(); this.abrir(d.lead); },
      error: (err: HttpErrorResponse) => this.toast.error('No se pudo reasignar', extractErrorMessage(err, 'Inténtalo de nuevo.')),
    });
  }

  async nuevoInteresado(): Promise<void> {
    const r = await Swal.fire({
      title: 'Nuevo interesado',
      html: `
        <div style="display:grid;gap:10px;text-align:left">
          <label>Nombre<input id="ni-nombre" class="swal2-input" style="margin:4px 0 0;width:100%" maxlength="150"></label>
          <label>Celular<input id="ni-celular" class="swal2-input" style="margin:4px 0 0;width:100%" maxlength="30" placeholder="7xxxxxxx"></label>
          <label>Correo (opcional)<input id="ni-correo" type="email" class="swal2-input" style="margin:4px 0 0;width:100%" maxlength="150"></label>
          <label>Nota (opcional)<input id="ni-nota" class="swal2-input" style="margin:4px 0 0;width:100%" maxlength="500" placeholder="Qué programa le interesa, de dónde llegó…"></label>
        </div>`,
      focusConfirm: false,
      showCancelButton: true, confirmButtonText: 'Registrar', cancelButtonText: 'Cancelar', confirmButtonColor: '#0b4258',
      preConfirm: () => {
        const val = (id: string) => ((document.getElementById(id) as HTMLInputElement)?.value ?? '').trim();
        const datos = { nombre: val('ni-nombre'), celular: val('ni-celular') || null, correo: val('ni-correo') || null, nota: val('ni-nota') || null };
        if (!datos.nombre) { Swal.showValidationMessage('Escribe el nombre.'); return false; }
        if (!datos.celular && !datos.correo) { Swal.showValidationMessage('Escribe al menos el celular o el correo.'); return false; }
        return datos;
      },
    });
    if (!r.isConfirmed || !r.value) return;
    this.crm.crear(r.value).subscribe({
      next: (res) => { this.toast.success('Interesado registrado', 'Si ya existía, se sumó a su historial.'); this.cargar(); this.abrirPorId(res.id); },
      error: (err: HttpErrorResponse) => this.toast.error('No se pudo registrar', extractErrorMessage(err, 'Revisa los datos.')),
    });
  }

  private abrirPorId(id: number): void {
    this.crm.detalle(id).subscribe({ next: d => this.detalle.set(d), error: () => {} });
  }

  private contactoVacio(): ContactoCrm {
    return { tipo: 'whatsapp', resultado: 'contactado', nota: null, proxima_accion: null, proxima_accion_at: null };
  }

  readonly hoy = new Date().toISOString().slice(0, 10);
  readonly resultados: { valor: ContactoCrm['resultado']; etiqueta: string }[] = [
    { valor: 'no_contesto', etiqueta: 'No contestó' },
    { valor: 'contactado', etiqueta: 'Hablamos' },
    { valor: 'interesado', etiqueta: 'Está interesado' },
    { valor: 'no_interesado', etiqueta: 'No le interesa' },
  ];
  readonly tipos: { valor: ContactoCrm['tipo']; etiqueta: string }[] = [
    { valor: 'whatsapp', etiqueta: 'WhatsApp' }, { valor: 'llamada', etiqueta: 'Llamada' },
    { valor: 'reunion', etiqueta: 'Reunión' }, { valor: 'correo', etiqueta: 'Correo' }, { valor: 'otro', etiqueta: 'Otro' },
  ];
  etiquetaResultado(r: string): string { return this.resultados.find(x => x.valor === r)?.etiqueta ?? r; }
}
