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

  enlaceVencido        = signal(false);

  /** true = viene del correo de credenciales enviado desde Caja ("Crear mi contraseña"). */
  esActivacion         = false;
  email                = '';
  private token        = '';
  private navTimer?: ReturnType<typeof setTimeout>;

  ngOnInit(): void {
    const params = this.route.snapshot.queryParamMap;
    this.token        = params.get('token') ?? '';
    this.email        = params.get('email') ?? '';
    this.esActivacion = params.get('tipo') === 'activacion';

    if (!this.token || !this.email) {
      this.error.set('Enlace inválido o incompleto. Solicita uno nuevo.');
      this.enlaceVencido.set(true);
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

    this.auth.resetPassword(this.token, this.email, this.password, this.passwordConfirmation, this.esActivacion ? 'activacion' : undefined).subscribe({
      next: () => {
        this.loading.set(false);
        this.success.set(true);
        this.navTimer = setTimeout(() => this.router.navigate(['/auth-modern/login']), 3000);
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set(err?.error?.error ?? 'El enlace es inválido o ha expirado.');
        this.enlaceVencido.set(err?.status === 422 && !err?.error?.errors);
      },
    });
  }

  ngOnDestroy(): void {
    clearTimeout(this.navTimer);
  }
}
