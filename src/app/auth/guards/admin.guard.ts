import { inject } from '@angular/core';
import { CanActivateFn, Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { AuthService } from '../application/services/auth.service';

/**
 * Guard que protege las rutas del panel de administración.
 * Los estudiantes (participante, ciudadano, estudiante) son redirigidos
 * a su área personal. Solo el personal interno (Admin, Operador, coordinador, docente)
 * puede acceder.
 */
export const adminGuard: CanActivateFn = (route: ActivatedRouteSnapshot, state: RouterStateSnapshot) => {
  const auth   = inject(AuthService);
  const router = inject(Router);

  if (!auth.isLoggedIn()) {
    return router.createUrlTree(['/auth-modern/login']);
  }

  // Si es estudiante y NO está intentando acceder a la zona de estudiante o su perfil
  if (auth.isEstudiante() && !state.url.startsWith('/estudiante') && !state.url.startsWith('/auth/completar-perfil')) {
    return router.createUrlTree(['/estudiante/dashboard']);
  }

  return true;
};
