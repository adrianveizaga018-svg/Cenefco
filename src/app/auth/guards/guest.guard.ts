import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../application/services/auth.service';

export const guestGuard: CanActivateFn = () => {
  const auth   = inject(AuthService);
  const router = inject(Router);

  if (!auth.isLoggedIn()) {
    return true;
  }

  // Estudiantes van a su área personal, no al dashboard de admin
  if (auth.isEstudiante()) {
    return router.createUrlTree(['/estudiante/dashboard']);
  }

  return router.createUrlTree(['/dashboards/cenefco']);
};
