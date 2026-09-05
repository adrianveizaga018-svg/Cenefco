import { Component, inject, signal, computed, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { SlicePipe } from '@angular/common';
import Swal from 'sweetalert2';
import { MensajeContactoService } from '../../application/services/mensaje-contacto.service';
import { MensajeContacto } from '../../domain/models/mensaje-contacto.model';
import { SecretariaService } from '../../../secretarias/application/services/secretaria.service';
import { Secretaria } from '../../../secretarias/domain/models/secretaria.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';

const ESTADO_BADGE: Record<string, string> = {
  nuevo:      'bg-primary/15 text-primary',
  pendiente:  'bg-warning/15 text-warning',
  respondido: 'bg-success/15 text-success',
  archivado:  'bg-default-200 text-default-500',
};

const ESTADO_LABEL: Record<string, string> = {
  nuevo:      'Nuevo',
  pendiente:  'Pendiente',
  respondido: 'Respondido',
  archivado:  'Archivado',
};

@Component({
  selector: 'app-mensaje-contacto-detail',
  standalone: true,
  imports: [NgIcon, FormsModule, PageTitle, RouterLink, SlicePipe],
  templateUrl: './mensaje-contacto-detail.html',
})
export class MensajeContactoDetail {
  private service    = inject(MensajeContactoService);
  private secretariaService = inject(SecretariaService);
  private toast       = inject(ToastService);
  private route        = inject(ActivatedRoute);
  private router       = inject(Router);
  private cdr          = inject(ChangeDetectorRef);

  mensaje    = signal<MensajeContacto | null>(null);
  secretaria = signal<Secretaria | null>(null);
  loading    = signal(true);
  saving     = signal(false);
  deleting   = signal(false);
  respuesta  = signal('');
  estadoRespuesta = signal<'respondido' | 'archivado'>('respondido');

  respuestaLength = computed(() => this.respuesta().trim().length);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(id).subscribe({
      next: (data) => {
        this.mensaje.set(data);
        if (data.respuesta) this.respuesta.set(data.respuesta);
        this.loading.set(false);
        if (data.secretaria_destino_id) this.cargarSecretaria(data.secretaria_destino_id);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el mensaje');
        this.loading.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  private cargarSecretaria(id: number): void {
    this.secretariaService.getById(id).subscribe({
      next: (s) => { this.secretaria.set(s); this.cdr.detectChanges(); },
      error: () => {  }
    });
  }

  estadoBadgeClass(estado: string): string {
    return ESTADO_BADGE[estado] ?? 'bg-default-200 text-default-600';
  }

  estadoLabel(estado: string): string {
    return ESTADO_LABEL[estado] ?? estado;
  }

  iniciales(nombre: string | null | undefined): string {
    if (!nombre) return '?';
    return nombre.trim().charAt(0).toUpperCase();
  }

  onSubmit(): void {
    const m = this.mensaje();
    if (!m || !this.respuesta().trim()) return;

    this.saving.set(true);
    this.service.responder(m.id, { respuesta: this.respuesta(), estado: this.estadoRespuesta() }).subscribe({
      next: (updated) => {
        this.mensaje.set(updated);
        this.toast.success(
          this.estadoRespuesta() === 'archivado' ? 'Archivado' : 'Respondido',
          this.estadoRespuesta() === 'archivado'
            ? 'La respuesta fue enviada y el mensaje quedó archivado'
            : 'La respuesta fue enviada correctamente'
        );
        this.saving.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo enviar la respuesta');
        this.saving.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  eliminar(): void {
    const m = this.mensaje();
    if (!m) return;

    Swal.fire({
      title: '¿Eliminar mensaje?',
      text: 'Esta acción no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (!result.isConfirmed) return;

      this.deleting.set(true);
      this.service.delete(m.id).subscribe({
        next: () => {
          this.toast.success('Eliminado', 'El mensaje ha sido eliminado');
          this.router.navigate(['/cenefco/mensajes-contacto']);
        },
        error: () => {
          this.toast.error('Error', 'No se pudo eliminar el mensaje');
          this.deleting.set(false);
          this.cdr.detectChanges();
        }
      });
    });
  }
}
