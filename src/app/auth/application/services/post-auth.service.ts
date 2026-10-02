import { Injectable, inject } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from './auth.service';

const VOLVER_KEY = 'cenefco_volver_portal';

/**
 * Decide a dónde va el usuario después de autenticarse.
 * Si llegó desde el portal (p. ej. al pulsar "Inscribirme"), lo devuelve
 * a esa página con la sesión ya iniciada allí.
 */
@Injectable({ providedIn: 'root' })
export class PostAuthService {
  private auth   = inject(AuthService);
  private router = inject(Router);

  /** Guarda la ruta del portal a la que hay que volver (solo rutas internas). */
  recordarOrigen(volver: string | null): void {
    if (volver && /^\/(?!\/)/.test(volver)) {
      sessionStorage.setItem(VOLVER_KEY, volver);
    }
  }

  vieneDelPortal(): boolean {
    return !!sessionStorage.getItem(VOLVER_KEY);
  }

  continuar(requiereCompletarPerfil = false): void {
    if (requiereCompletarPerfil) {
      this.router.navigate(['/auth/completar-perfil']);
      return;
    }

    const volver = sessionStorage.getItem(VOLVER_KEY);
    if (volver && this.auth.isEstudiante()) {
      sessionStorage.removeItem(VOLVER_KEY);
      this.auth.crearHandoff().subscribe({
        next: (res) => {
          window.location.href = `${res.portal_url}${volver}#sso=${res.code}`;
        },
        error: () => this.irAlInicio(),
      });
      return;
    }

    sessionStorage.removeItem(VOLVER_KEY);
    this.irAlInicio();
  }

  private irAlInicio(): void {
    this.router.navigate([this.auth.isEstudiante() ? '/estudiante/dashboard' : '/dashboards/cenefco']);
  }
}
