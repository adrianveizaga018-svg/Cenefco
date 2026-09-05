import { Component, inject, signal, AfterViewInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { AuthService } from '../../../../auth/application/services/auth.service';
import { HttpClient } from '@angular/common/http';
import { switchMap, tap } from 'rxjs/operators';

declare var google: any;

@Component({
  selector: 'app-register',
  imports: [FormsModule, RouterLink, NgIcon],
  templateUrl: './register.html',
})
export class Register implements AfterViewInit {
  private auth   = inject(AuthService);
  private router = inject(Router);
  private http   = inject(HttpClient);

  nombre   = '';
  apellido = '';
  email    = '';
  ci       = '';
  telefono = '';
  password = '';
  passwordConfirm = '';

  loading      = signal(false);
  error        = signal('');
  showPassword = signal(false);
  showConfirm  = signal(false);

  ngAfterViewInit(): void {
    if (typeof google !== 'undefined' && google.accounts) {
      google.accounts.id.initialize({
        client_id: 'TU_CLIENT_ID_DE_GOOGLE', // Reemplazar con ID real
        callback: this.handleGoogleResponse.bind(this),
      });
      google.accounts.id.renderButton(
        document.getElementById('google-register-btn'),
        { theme: 'outline', size: 'large', width: '100%' }
      );
    }
  }

  handleGoogleResponse(response: any): void {
    this.loading.set(true);
    this.error.set('');
    this.auth.loginGoogle(response.credential).subscribe({
      next: (res: any) => {
        if (res.require_profile_completion) {
          this.router.navigate(['/auth/completar-perfil']);
        } else if (this.auth.isEstudiante()) {
          this.router.navigate(['/estudiante/dashboard']);
        } else {
          this.router.navigate(['/dashboards/cenefco']);
        }
      },
      error: (err: any) => {
        this.loading.set(false);
        const msg = err?.error?.message ?? err?.error?.error ?? 'Error al autenticar con Google.';
        this.error.set(msg);
      },
    });
  }

  submit(): void {
    if (!this.nombre || !this.apellido || !this.email || !this.ci || !this.telefono || !this.password) {
      this.error.set('Por favor completa todos los campos.');
      return;
    }
    if (this.password !== this.passwordConfirm) {
      this.error.set('Las contraseñas no coinciden.');
      return;
    }
    if (this.password.length < 8) {
      this.error.set('La contraseña debe tener al menos 8 caracteres.');
      return;
    }

    this.loading.set(true);
    this.error.set('');

    // Paso 1: Registrar → devuelve token+user igual que login
    // Paso 2: Con ese token activo, completar perfil (CI + Teléfono)
    this.auth.register(
      this.nombre,
      this.apellido,
      this.email,
      this.password,
      this.passwordConfirm
    ).pipe(
      // El AuthService ya guarda el token en localStorage en el tap interno
      switchMap(() => this.auth.completeProfile(this.ci, this.telefono))
    ).subscribe({
      next: () => {
        if (this.auth.isEstudiante()) {
          this.router.navigate(['/estudiante/dashboard']);
        } else {
          this.router.navigate(['/dashboards/cenefco']);
        }
      },
      error: (err: any) => {
        this.loading.set(false);
        // Si el registro fue ok pero falló el CI, ir a completar perfil
        if (this.auth.isLoggedIn()) {
          this.router.navigate(['/auth/completar-perfil']);
        } else {
          const msg = err?.error?.message ?? err?.error?.error ?? 'Error al crear la cuenta.';
          this.error.set(msg);
        }
      },
    });
  }
}
