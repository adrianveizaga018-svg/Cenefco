import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { ContactoCrm, CrmService, ETAPAS, ORIGENES, Origen, TareaCrm } from '../../application/services/crm.service';

/** Lo que hay que hacer hoy: seguimientos vencidos primero, luego los de hoy, con registro de un clic. */
@Component({
  selector: 'app-tareas-crm',
  standalone: true,
  imports: [PageTitle, NgIcon, DatePipe, RouterLink],
  templateUrl: './tareas.html',
})
export class TareasCrmComponent implements OnInit {
  private crm   = inject(CrmService);
  private toast = inject(ToastService);
  private auth  = inject(AuthService);

  readonly supervisa = this.auth.hasPermission('crm.supervisar');

  tareas     = signal<TareaCrm[]>([]);
  cargando   = signal(true);
  procesando = signal<number | null>(null);

  readonly vencidas = computed(() => this.tareas().filter(t => t.vencida));
  readonly deHoy = computed(() => this.tareas().filter(t => !t.vencida));

  ngOnInit(): void { this.cargar(); }

  cargar(): void {
    this.cargando.set(true);
    this.crm.tareas().subscribe({
      next: (res) => { this.tareas.set(res.data); this.cargando.set(false); },
      error: (err: HttpErrorResponse) => {
        this.cargando.set(false);
        this.toast.error('No se pudieron cargar las tareas', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  registrar(t: TareaCrm, resultado: ContactoCrm['resultado']): void {
    this.procesando.set(t.id);
    this.crm.registrarContacto(t.id, { tipo: resultado === 'no_contesto' ? 'llamada' : 'whatsapp', resultado, nota: null, proxima_accion: null, proxima_accion_at: null }).subscribe({
      next: (res) => {
        this.procesando.set(null);
        // Sale de la lista: su próxima acción quedó para otro día o se cerró
        this.tareas.update(ts => ts.filter(x => x.id !== t.id));
        this.toast.success(resultado === 'no_contesto' ? 'Siguiente intento agendado' : 'Contacto registrado',
          res.estado === 'perdido' ? `${t.nombre} se cerró: no responde` : t.nombre);
      },
      error: (err: HttpErrorResponse) => {
        this.procesando.set(null);
        this.toast.error('No se pudo registrar', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  whatsapp(celular: string | null): string | null {
    const d = (celular ?? '').replace(/\D+/g, '');
    if (!d) return null;
    return `https://wa.me/${d.length === 8 ? '591' + d : d}`;
  }

  etapa(e: string): string { return ETAPAS.find(x => x.valor === e)?.etiqueta ?? e; }
  origen(o: Origen): string { return ORIGENES[o] ?? o; }
}
