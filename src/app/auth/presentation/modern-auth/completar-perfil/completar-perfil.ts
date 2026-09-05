import { Component, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { AuthService } from '../../../application/services/auth.service';

@Component({
  selector: 'app-completar-perfil',
  imports: [FormsModule, NgIcon],
  templateUrl: './completar-perfil.html',
})
export class CompletarPerfil {
  private auth   = inject(AuthService);
  private router = inject(Router);

  ci       = '';
  telefono = '';
  loading  = signal(false);
  error    = signal('');

  submit(): void {
    if (!this.ci || !this.telefono) {
      this.error.set('Por favor completa todos los campos.');
      return;
    }

    this.loading.set(true);
    this.error.set('');

    this.auth.completeProfile(this.ci, this.telefono).subscribe({
      next: () => {
        if (this.auth.isEstudiante()) {
          this.router.navigate(['/estudiante/dashboard']);
        } else {
          this.router.navigate(['/dashboards/cenefco']);
        }
      },
      error: (err: any) => {
        this.loading.set(false);
        const msg = err?.error?.message ?? err?.error?.error ?? 'Error al guardar el perfil.';
        this.error.set(msg);
      },
    });
  }
}
