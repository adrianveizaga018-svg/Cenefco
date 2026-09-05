import { Component, Input, OnChanges, inject, signal } from '@angular/core';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { VentaService } from '../../../ventas/application/services/venta.service';
import { ReglamentoPrograma } from '../../../ventas/domain/models/reglamento.model';
import { ToastService } from '../../../common/application/services/toast.service';

interface SeccionReglamento {
  campo:  keyof ReglamentoPrograma;
  titulo: string;
  icono:  string;
}

@Component({
  selector: 'app-curso-reglamento',
  imports: [NgIcon, FormsModule],
  templateUrl: './curso-reglamento.html',
})
export class CursoReglamento implements OnChanges {
  @Input() idPrograma: number | null = null;

  private service = inject(VentaService);
  private toast   = inject(ToastService);

  reglamento = signal<ReglamentoPrograma | null>(null);
  loading    = signal(false);
  saving     = signal(false);
  editando   = signal(false);
  draft      = signal<Partial<ReglamentoPrograma>>({});

  readonly secciones: SeccionReglamento[] = [
    { campo: 'bienvenida',         titulo: 'Carta de Bienvenida',      icono: 'lucideHeart'        },
    { campo: 'reglas_asistencia',  titulo: 'Asistencia y Puntualidad', icono: 'lucideCalendarCheck' },
    { campo: 'reglas_evaluacion',  titulo: 'Evaluaciones y Trabajos',  icono: 'lucideClipboardList' },
    { campo: 'reglas_pagos',       titulo: 'Compromisos de Pago',      icono: 'lucideCreditCard'   },
    { campo: 'reglas_conducta',    titulo: 'Convivencia y Respeto',    icono: 'lucideHandshake'    },
    { campo: 'reglas_plataformas', titulo: 'Plataformas Digitales',    icono: 'lucideMonitor'      },
    { campo: 'reglas_derechos',    titulo: 'Derechos del Estudiante',  icono: 'lucideShieldCheck'  },
  ];

  ngOnChanges(): void {
    if (this.idPrograma) this.cargar();
  }

  cargar(): void {
    if (!this.idPrograma) return;
    this.loading.set(true);
    this.service.getReglamento(this.idPrograma).subscribe({
      next: r => { this.reglamento.set(r); this.loading.set(false); },
      error: () => { this.loading.set(false); this.toast.error('Error', 'No se pudo cargar el reglamento.'); },
    });
  }

  abrirEditor(): void {
    const r = this.reglamento();
    if (!r) return;
    this.draft.set({
      bienvenida:         r.bienvenida         ?? '',
      reglas_asistencia:  r.reglas_asistencia  ?? '',
      reglas_evaluacion:  r.reglas_evaluacion  ?? '',
      reglas_pagos:       r.reglas_pagos       ?? '',
      reglas_conducta:    r.reglas_conducta    ?? '',
      reglas_plataformas: r.reglas_plataformas ?? '',
      reglas_derechos:    r.reglas_derechos    ?? '',
    });
    this.editando.set(true);
  }

  cancelar(): void { this.editando.set(false); }

  getDraft(campo: string): string {
    return (this.draft() as Record<string, string>)[campo] ?? '';
  }

  setDraft(campo: string, value: string): void {
    this.draft.update(d => ({ ...d, [campo]: value }));
  }

  guardar(): void {
    if (!this.idPrograma) return;
    this.saving.set(true);
    this.service.saveReglamento(this.idPrograma, this.draft()).subscribe({
      next: r => {
        this.reglamento.set(r);
        this.saving.set(false);
        this.editando.set(false);
        this.toast.success('Guardado', 'Reglamento actualizado correctamente.');
      },
      error: () => {
        this.saving.set(false);
        this.toast.error('Error', 'No se pudo guardar el reglamento.');
      },
    });
  }

  getItems(campo: keyof ReglamentoPrograma): string[] {
    const r = this.reglamento();
    if (!r) return [];
    const val = r[campo];
    if (!val) return [];
    return String(val).split('\n').map(l => l.trim()).filter(Boolean);
  }

  esPersonalizado(campo: string): boolean {
    const r = this.reglamento();
    return r ? !!(r as any)[campo + '_personalizado'] : false;
  }
}
