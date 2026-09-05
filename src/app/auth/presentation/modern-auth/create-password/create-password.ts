import { Component, inject, signal, OnInit, OnDestroy } from '@angular/core';
import { RouterLink, ActivatedRoute, Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { AuthService } from '../../../../auth/application/services/auth.service';

@Component({
  selector: 'app-create-password',
  imports: [RouterLink, FormsModule, NgIcon],
  templateUrl: './create-password.html',
  styles: ``
})
export class CreatePassword implements OnInit, OnDestroy {
  private auth    = inject(AuthService);
  private route   = inject(ActivatedRoute);
  private router  = inject(Router);

  password             = '';
  passwordConfirmation = '';
  showPassword         = signal(false);
  showConfirm          = signal(false);
  loading              = signal(false);
  success              = signal(false);
  error                = signal('');

  private token        = '';
  private email        = '';
  private navTimer?: ReturnType<typeof setTimeout>;

  ngOnInit(): void {
    this.token = this.route.snapshot.queryParamMap.get('token') ?? '';
    this.email = this.route.snapshot.queryParamMap.get('email') ?? '';

    if (!this.token || !this.email) {
      this.error.set('Enlace inválido o expirado. Solicita uno nuevo.');
    }
  }

  submit(): void {
    if (!this.password || !this.passwordConfirmation) {
      this.error.set('Completa ambos campos.');
      return;
    }
    if (this.password.length < 8) {
      this.error.set('La contraseña debe tener al menos 8 caracteres.');
      return;
    }
    if (this.password !== this.passwordConfirmation) {
      this.error.set('Las contraseñas no coinciden.');
      return;
    }

    this.loading.set(true);
    this.error.set('');

    this.auth.resetPassword(this.token, this.email, this.password, this.passwordConfirmation).subscribe({
      next: () => {
        this.loading.set(false);
        this.success.set(true);
        this.navTimer = setTimeout(() => this.router.navigate(['/auth-modern/login']), 3000);
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set(err?.error?.error ?? 'El enlace es inválido o ha expirado.');
      },
    });
  }

  ngOnDestroy(): void {
    clearTimeout(this.navTimer);
  }
}
