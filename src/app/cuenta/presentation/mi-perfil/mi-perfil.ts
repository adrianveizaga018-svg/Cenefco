import { Component, inject, signal, OnDestroy } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { AuthService, AuthUser } from '../../../auth/application/services/auth.service';
import { PageTitle } from '../../../common/components/page-title/page-title';

@Component({
  selector: 'app-mi-perfil',
  imports: [FormsModule, NgIcon, PageTitle],
  templateUrl: './mi-perfil.html',
  styles: ``
})
export class MiPerfil implements OnDestroy {
  auth = inject(AuthService);

  private datosTimer?: ReturnType<typeof setTimeout>;
  private passTimer?:  ReturnType<typeof setTimeout>;

  name  = this.auth.currentUser()?.nombre ?? '';
  email = this.auth.currentUser()?.email ?? '';

  currentPassword    = '';
  newPassword        = '';
  newPasswordConfirm = '';
  showPasswords      = signal(false);

  savingDatos    = signal(false);
  savingPassword = signal(false);
  successDatos   = signal('');
  successPass    = signal('');
  errorDatos     = signal('');
  errorPass      = signal('');

  get iniciales(): string {
    return (this.auth.currentUser()?.nombre ?? 'U')
      .split(' ')
      .slice(0, 2)
      .map(w => w[0])
      .join('')
      .toUpperCase();
  }

  guardarDatos(): void {
    if (!this.name.trim()) { this.errorDatos.set('El nombre es requerido.'); return; }
    this.savingDatos.set(true);
    this.successDatos.set('');
    this.errorDatos.set('');

    this.auth.updatePerfil({ nombre: this.name.trim() }).subscribe({
      next: () => {
        this.savingDatos.set(false);
        this.successDatos.set('Datos actualizados correctamente.');
        this.datosTimer = setTimeout(() => this.successDatos.set(''), 3000);
      },
      error: (err: any) => {
        this.savingDatos.set(false);
        this.errorDatos.set(err?.error?.message ?? 'Error al actualizar los datos.');
      },
    });
  }

  ngOnDestroy(): void {
    clearTimeout(this.datosTimer);
    clearTimeout(this.passTimer);
  }

  guardarPassword(): void {
    if (!this.currentPassword) { this.errorPass.set('Ingresa tu contraseña actual.'); return; }
    if (!this.newPassword)     { this.errorPass.set('Ingresa la nueva contraseña.'); return; }
    if (this.newPassword.length < 8) { this.errorPass.set('La contraseña debe tener al menos 8 caracteres.'); return; }
    if (this.newPassword !== this.newPasswordConfirm) { this.errorPass.set('Las contraseñas no coinciden.'); return; }

    this.savingPassword.set(true);
    this.successPass.set('');
    this.errorPass.set('');

    this.auth.updatePerfil({
      current_password:      this.currentPassword,
      password:              this.newPassword,
      password_confirmation: this.newPasswordConfirm,
    }).subscribe({
      next: () => {
        this.savingPassword.set(false);
        this.successPass.set('Contraseña actualizada correctamente.');
        this.currentPassword = '';
        this.newPassword = '';
        this.newPasswordConfirm = '';
        this.passTimer = setTimeout(() => this.successPass.set(''), 3000);
      },
      error: (err: any) => {
        this.savingPassword.set(false);
        this.errorPass.set(err?.error?.error ?? err?.error?.message ?? 'Error al cambiar la contraseña.');
      },
    });
  }
}
