import { Component, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { AuthService } from '../../../../auth/application/services/auth.service';

@Component({
  selector: 'app-reset-password',
  imports: [RouterLink, FormsModule, NgIcon],
  templateUrl: './reset-password.html',
  styles: ``
})
export class ResetPassword {
  private auth = inject(AuthService);

  email   = '';
  loading = signal(false);
  success = signal(false);
  error   = signal('');

  submit(): void {
    if (!this.email) {
      this.error.set('Ingresa tu correo electrónico.');
      return;
    }

    this.loading.set(true);
    this.error.set('');

    this.auth.forgotPassword(this.email).subscribe({
      next: () => {
        this.loading.set(false);
        this.success.set(true);
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set(err?.error?.error ?? 'No encontramos una cuenta con ese correo electrónico.');
      },
    });
  }
}
