import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { RouterLink, Router } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { NgClass } from '@angular/common';
import { NotificacionesService } from '../../application/services/notificacion.service';
import { EnviarComunicadoPayload, Prioridad, Destinatario, TipoComunicado, prioridadConfig, tipoBadgeClass } from '../../domain/models/notificacion.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { RoleService } from '../../../roles/application/services/role.service';
import { Role } from '../../../roles/domain/models/role.model';
import { UsuarioService } from '../../../usuarios/application/services/usuario.service';
import { Usuario } from '../../../usuarios/domain/models/usuario.model';

@Component({
  selector: 'app-notificacion-enviar',
  standalone: true,
  imports: [NgIcon, RouterLink, FormsModule, NgClass, PageTitle],
  templateUrl: './notificacion-enviar.html',
})
export class NotificacionEnviar implements OnInit {
  private notifSv = inject(NotificacionesService);
  private roleSv  = inject(RoleService);
  private usuarioSv = inject(UsuarioService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private cdr     = inject(ChangeDetectorRef);

  enviando   = signal(false);
  imagenPreview = signal<string | null>(null);
  imagenFile = signal<File | null>(null);

  roles     = signal<Role[]>([]);
  usuarios  = signal<Usuario[]>([]);
  filtroPersonal = signal('');

  prioridadConfig = prioridadConfig;
  tipoBadgeClass  = tipoBadgeClass;

  readonly prioridades: { valor: Prioridad; label: string }[] = [
    { valor: 'baja',    label: 'Baja' },
    { valor: 'media',   label: 'Media' },
    { valor: 'alta',    label: 'Alta' },
    { valor: 'critica', label: 'Crítica' },
  ];

  readonly destinatarios: { valor: Destinatario; label: string; icono: string }[] = [
    { valor: 'todos',   label: 'Todo el personal',      icono: 'lucideUsers' },
    { valor: 'rol',     label: 'Por rol',                icono: 'lucideShield' },
    { valor: 'usuario', label: 'Personal específico',    icono: 'lucideUser' },
  ];

  form = {
    titulo:       '',
    mensaje:      '',
    tipo:         'comunicado' as TipoComunicado,
    prioridad:    'media' as Prioridad,
    destinatario: 'todos' as Destinatario,
    rol_destino:  '',
    usuario_ids:  [] as number[],
    expires_at:   '',
  };

  ngOnInit(): void {
    this.roleSv.getAll().subscribe({ next: r => this.roles.set(r) });
    this.usuarioSv.getAll({ pageSize: 200 }).subscribe({ next: res => this.usuarios.set(res.data) });
  }

  get usuariosFiltrados(): Usuario[] {
    const q = this.filtroPersonal().trim().toLowerCase();
    if (!q) return this.usuarios();
    return this.usuarios().filter(u => `${u.nombre} ${u.apellido}`.toLowerCase().includes(q));
  }

  get usuariosSeleccionados(): Usuario[] {
    const ids = new Set(this.form.usuario_ids);
    return this.usuarios().filter(u => ids.has(u.id));
  }

  toggleUsuario(id: number, checked: boolean): void {
    this.form.usuario_ids = checked
      ? [...this.form.usuario_ids, id]
      : this.form.usuario_ids.filter(uid => uid !== id);
  }

  quitarUsuario(id: number): void {
    this.form.usuario_ids = this.form.usuario_ids.filter(uid => uid !== id);
  }

  onImagenChange(event: Event) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;
    this.imagenFile.set(file);
    const reader = new FileReader();
    reader.onload = e => {
      this.imagenPreview.set(e.target?.result as string);
      this.cdr.detectChanges();
    };
    reader.readAsDataURL(file);
  }

  quitarImagen() {
    this.imagenFile.set(null);
    this.imagenPreview.set(null);
  }

  enviar() {
    if (!this.form.titulo.trim() || !this.form.mensaje.trim()) {
      this.toast.warning('Campos requeridos', 'El título y el mensaje son obligatorios.');
      return;
    }
    if (this.form.destinatario === 'rol' && !this.form.rol_destino) {
      this.toast.warning('Selecciona un rol', 'Elige a qué rol se enviará el comunicado.');
      return;
    }
    if (this.form.destinatario === 'usuario' && this.form.usuario_ids.length === 0) {
      this.toast.warning('Selecciona destinatarios', 'Elige al menos una persona del personal.');
      return;
    }

    const payload: EnviarComunicadoPayload = {
      titulo:       this.form.titulo,
      mensaje:      this.form.mensaje,
      tipo:         this.form.tipo,
      prioridad:    this.form.prioridad,
      destinatario: this.form.destinatario,
    };

    if (this.form.destinatario === 'rol' && this.form.rol_destino.trim()) {
      payload.rol_destino = this.form.rol_destino;
    }
    if (this.form.destinatario === 'usuario') {
      payload.usuario_ids = this.form.usuario_ids;
    }
    if (this.form.expires_at) payload.expires_at = this.form.expires_at;
    if (this.imagenFile()) payload.imagen = this.imagenFile()!;

    this.enviando.set(true);
    this.notifSv.enviarComunicado(payload).subscribe({
      next: () => {
        this.toast.success('Comunicado enviado', 'La notificación fue enviada correctamente.');
        this.router.navigate(['/cenefco/notificaciones/enviados']);
      },
      error: (err: any) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo enviar el comunicado.');
        this.enviando.set(false);
        this.cdr.detectChanges();
      }
    });
  }
}
