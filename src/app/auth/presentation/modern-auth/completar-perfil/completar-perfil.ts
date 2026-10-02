import { Component, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { AuthService } from '../../../application/services/auth.service';
import { PostAuthService } from '../../../application/services/post-auth.service';

@Component({
  selector: 'app-completar-perfil',
  imports: [FormsModule, NgIcon],
  templateUrl: './completar-perfil.html',
})
export class CompletarPerfil {
  private auth     = inject(AuthService);
  private postAuth = inject(PostAuthService);

  nombre          = '';
  apellido        = '';
  apellidoMaterno = '';
  ci       = '';
  telefono = '';
  loading  = signal(false);
  error    = signal('');

  constructor() {
    // Google entrega nombres y un solo texto de apellidos: se proponen separados
    // para que la persona los confirme o corrija.
    const user = this.auth.currentUser();
    this.nombre   = user?.nombre ?? '';
    this.ci       = user?.ci ?? '';
    this.telefono = user?.telefono ?? '';

    if (user?.apellidoMaterno) {
      this.apellido        = user.apellido ?? '';
      this.apellidoMaterno = user.apellidoMaterno;
    } else {
      const partes = (user?.apellido ?? '').trim().split(/\s+/).filter(Boolean);
      if (partes.length === 2) {
        [this.apellido, this.apellidoMaterno] = partes;
      } else {
        this.apellido = partes.join(' ');
      }
    }
  }

  submit(): void {
    const nombre   = this.nombre.trim();
    const apellido = this.apellido.trim();
    const ci       = this.ci.trim();
    const telefono = this.telefono.trim();

    if (!nombre || !apellido || !ci || !telefono) {
      this.error.set('Por favor completa todos los campos obligatorios.');
      return;
    }

    this.loading.set(true);
    this.error.set('');

    this.auth.completeProfile(ci, telefono, {
      nombre,
      apellido,
      apellido_materno: this.apellidoMaterno.trim() || null,
    }).subscribe({
      next: () => {
        this.postAuth.continuar();
      },
      error: (err: any) => {
        this.loading.set(false);
        const msg = err?.error?.message ?? err?.error?.error ?? 'Error al guardar el perfil.';
        this.error.set(msg);
      },
    });
  }
}
