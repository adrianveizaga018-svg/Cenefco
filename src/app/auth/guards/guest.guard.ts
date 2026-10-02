import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../application/services/auth.service';

export const guestGuard: CanActivateFn = (route) => {
  const auth   = inject(AuthService);
  const router = inject(Router);

  // Si viene del portal con sesión ya abierta, la pantalla de acceso lo devuelve al portal
  if (!auth.isLoggedIn() || route.queryParamMap.has('volver')) {
    return true;
  }

  // Estudiantes van a su área personal, no al dashboard de admin
  if (auth.isEstudiante()) {
    return router.createUrlTree(['/estudiante/dashboard']);
  }

  return router.createUrlTree(['/dashboards/cenefco']);
};
