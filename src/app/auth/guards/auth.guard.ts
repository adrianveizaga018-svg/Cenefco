import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../application/services/auth.service';
import { PostAuthService } from '../application/services/post-auth.service';

export const authGuard: CanActivateFn = (_route, state) => {
  const auth   = inject(AuthService);
  const router = inject(Router);

  if (auth.isLoggedIn()) {
    return true;
  }

  // Tras iniciar sesión vuelve aquí (p. ej. el enlace "Pagar mi inscripción" del portal)
  inject(PostAuthService).recordarDestinoPanel(state.url);
  return router.createUrlTree(['/auth-modern/login']);
};
