import { Component, inject, signal, OnInit, AfterViewInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { AuthService } from '../../../auth/application/services/auth.service';

declare var google: any;

@Component({
  selector: 'app-student-access',
  imports: [FormsModule, RouterLink, NgIcon],
  templateUrl: './student-access.html',
})
export class StudentAccess implements OnInit, AfterViewInit {
  private auth   = inject(AuthService);
  private router = inject(Router);

  email    = '';
  password = '';
  loading      = signal(false);
  error        = signal('');
  showPassword = signal(false);
  modoLogin    = signal<'opciones' | 'email'>('opciones');

  // Datos del curso desde donde vino
  cursoUrl   = signal<string | null>(null);
  cursoNombre = signal<string | null>(null);

  ngOnInit(): void {
    // Guardar el curso del que vino
    const returnUrl = localStorage.getItem('portal_return_url');
    if (returnUrl) {
      this.cursoUrl.set(returnUrl);
      // Extraer nombre del curso del slug de la URL
      const slug = returnUrl.split('/cursos/')[1] ?? null;
      if (slug) {
        this.cursoNombre.set(slug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
      }
    }
  }

  ngAfterViewInit(): void {
    this.initGoogle();
  }

  private initGoogle(): void {
    if (typeof google !== 'undefined' && google.accounts) {
      google.accounts.id.initialize({
        client_id: '272016198702-6nn2d1ibdeu73v2dicuh4n0i911ei6sa.apps.googleusercontent.com',
        callback: this.handleGoogleResponse.bind(this),
      });
      google.accounts.id.renderButton(
        document.getElementById('google-btn-student'),
        { theme: 'outline', size: 'large', width: '100%', text: 'continue_with' }
      );
    }
  }

  private navigateAfterLogin(requireProfileCompletion: boolean): void {
    if (requireProfileCompletion) {
      this.router.navigate(['/auth/completar-perfil']);
      return;
    }

    const portalReturnUrl = localStorage.getItem('portal_return_url');
    const autoInscribir   = localStorage.getItem('cenefco_auto_inscribir');

    if (portalReturnUrl && autoInscribir && this.auth.isEstudiante()) {
      localStorage.removeItem('portal_return_url');
      localStorage.removeItem('cenefco_auto_inscribir');
      const portalBase = window.location.origin.replace(':4200', ':4201');
      window.location.href = portalBase + portalReturnUrl;
      return;
    }

    if (this.auth.isEstudiante()) {
      this.router.navigate(['/estudiante/dashboard']);
    } else {
      this.router.navigate(['/dashboards/cenefco']);
    }
  }

  handleGoogleResponse(response: any): void {
    this.loading.set(true);
    this.error.set('');
    this.auth.loginGoogle(response.credential).subscribe({
      next: (res: any) => {
        this.navigateAfterLogin(!!res.require_profile_completion);
      },
      error: (err) => {
        this.loading.set(false);
        const msg = err?.error?.message ?? err?.error?.error ?? 'Error al autenticar con Google.';
        this.error.set(msg);
      }
    });
  }

  submit(): void {
    if (!this.email || !this.password) {
      this.error.set('Ingresa tu correo y contraseña.');
      return;
    }
    this.loading.set(true);
    this.error.set('');
    this.auth.login(this.email, this.password).subscribe({
      next: () => { this.navigateAfterLogin(false); },
      error: (err) => {
        this.loading.set(false);
        const msg = err?.error?.message ?? err?.error?.error ?? 'Credenciales incorrectas.';
        this.error.set(msg);
      },
    });
  }
}
