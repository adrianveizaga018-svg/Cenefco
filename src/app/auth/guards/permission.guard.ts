import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../application/services/auth.service';

export function permissionGuard(permiso: string): CanActivateFn {
  return () => {
    const auth   = inject(AuthService);
    const router = inject(Router);

    if (auth.hasPermission(permiso)) {
      return true;
    }

    // Estudiantes sin permiso van a su área, no al admin
    if (auth.isEstudiante()) {
      return router.createUrlTree(['/estudiante/dashboard']);
    }

    return router.createUrlTree(['/dashboards/cenefco']);
  };
}
