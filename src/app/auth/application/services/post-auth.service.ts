import { Injectable, inject } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from './auth.service';

const VOLVER_KEY = 'cenefco_volver_portal';
const DESTINO_PANEL_KEY = 'cenefco_destino_panel';

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

  /** Ruta del panel que pidió iniciar sesión (solo rutas internas, no la del propio login). */
  recordarDestinoPanel(url: string): void {
    if (/^\/(?!\/)/.test(url) && !url.startsWith('/auth')) {
      sessionStorage.setItem(DESTINO_PANEL_KEY, url);
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

    const destino = sessionStorage.getItem(DESTINO_PANEL_KEY);
    sessionStorage.removeItem(DESTINO_PANEL_KEY);
    // El estudiante solo vuelve a su área; el personal a cualquier ruta del panel
    if (destino && (!this.auth.isEstudiante() || destino.startsWith('/estudiante'))) {
      this.router.navigateByUrl(destino);
      return;
    }

    this.irAlInicio();
  }

  private irAlInicio(): void {
    this.router.navigate([this.auth.isEstudiante() ? '/estudiante/dashboard' : '/dashboards/cenefco']);
  }
}
