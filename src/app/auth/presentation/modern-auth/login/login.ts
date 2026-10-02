import { Component, inject, signal, OnInit, OnDestroy, AfterViewInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { switchMap } from 'rxjs/operators';
import { AuthService } from '../../../../auth/application/services/auth.service';
import { PostAuthService } from '../../../../auth/application/services/post-auth.service';

declare var google: any;

@Component({
  selector: 'app-login',
  imports: [FormsModule, NgIcon, RouterLink],
  templateUrl: './login.html',
  styles: `
    @keyframes float-bg {
      0%   { transform: translate(0, 0) scale(1); }
      25%  { transform: translate(-12px, -8px) scale(1.03); }
      50%  { transform: translate(8px, -15px) scale(1); }
      75%  { transform: translate(-6px, 8px) scale(0.97); }
      100% { transform: translate(0, 0) scale(1); }
    }
    .animate-float-bg {
      animation: float-bg 20s ease-in-out infinite;
      transform-origin: center;
    }

    @property --snake-angle {
      syntax: '<angle>';
      initial-value: 0deg;
      inherits: false;
    }
    @keyframes snake-border {
      to { --snake-angle: 360deg; }
    }
    .snake-card {
      position: relative;
      border-radius: 12px;
    }
    .snake-card::before {
      content: '';
      position: absolute;
      inset: -1.5px;
      border-radius: 14px;
      background: conic-gradient(
        from var(--snake-angle),
        transparent 80%,
        var(--color-brand-accent) 90%,
        var(--color-brand-accent-light) 93%,
        var(--color-brand-accent) 96%,
        transparent
      );
      animation: snake-border 6s linear infinite;
      z-index: 0;
      opacity: 0.5;
    }
    .snake-card::after {
      content: '';
      position: absolute;
      inset: 1.5px;
      border-radius: 11px;
      background: var(--color-brand-primary-dark);
      z-index: 1;
    }
    .snake-card > * {
      position: relative;
      z-index: 2;
    }
    .snake-card:nth-child(2)::before { animation-delay: -2s; }
    .snake-card:nth-child(3)::before { animation-delay: -4s; }

    .bg-slide {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      transition: opacity 1.5s ease-in-out;
      transform: scale(1.08);
    }
    .bg-slide.active {
      opacity: 1;
      animation: ken-burns 6s ease-out forwards;
    }
    @keyframes ken-burns {
      from { transform: scale(1.08); }
      to   { transform: scale(1); }
    }

    .login-glass {
      background: rgba(255, 255, 255, 0.35);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.35);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
    }
    .login-glass .form-input {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.25);
      color: white;
    }
    .login-glass .form-input::placeholder { color: rgba(255,255,255,0.5); }
    .login-glass .form-input:focus {
      background: rgba(255, 255, 255, 0.2);
      border-color: rgba(255, 255, 255, 0.5);
      outline: none;
    }

    .acceso-tabs {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4px;
      padding: 4px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.12);
    }
    .acceso-tab {
      padding: 8px 12px;
      border-radius: 7px;
      font-size: 0.875rem;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.65);
      transition: all 0.2s ease;
    }
    .acceso-tab.active {
      background: white;
      color: var(--color-brand-primary-dark);
    }

    .slide-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: rgba(255,255,255,0.35);
      transition: all 0.4s ease;
      cursor: pointer;
      border: none;
      padding: 0;
    }
    .slide-dot.active {
      background: white;
      width: 24px;
      border-radius: 4px;
    }
  `
})
export class Login implements OnInit, OnDestroy, AfterViewInit {
  private auth     = inject(AuthService);
  private postAuth = inject(PostAuthService);
  private route    = inject(ActivatedRoute);

  modo = signal<'login' | 'registro'>('login');

  nombre   = '';
  apellido = '';
  apellidoMaterno = '';
  ci       = '';
  telefono = '';
  email    = '';
  password = '';
  loading      = signal(false);
  error        = signal('');
  showPassword = signal(false);
  capsLock     = signal(false);
  vieneDelPortal = signal(false);

  readonly fondos = [
    'assets/images/fondos/1.jpg',
    'assets/images/fondos/2.jpg',
    'assets/images/fondos/3.jpg',
    'assets/images/fondos/4.jpg',
    'assets/images/fondos/5.jpg',
  ];
  currentFondo = signal(0);
  private interval: ReturnType<typeof setInterval> | null = null;
  private googleTimer: ReturnType<typeof setTimeout> | null = null;

  ngOnInit(): void {
    const params = this.route.snapshot.queryParamMap;

    if (params.get('expired') === '1') {
      this.error.set('Tu sesión expiró. Vuelve a iniciar sesión.');
    }

    const modo = params.get('modo') ?? this.route.snapshot.data['modo'];
    if (modo === 'registro') {
      this.modo.set('registro');
    }

    // Llegó desde el portal (p. ej. "Inscribirme"): recordar a dónde volver
    this.postAuth.recordarOrigen(params.get('volver'));
    this.vieneDelPortal.set(this.postAuth.vieneDelPortal());

    if (this.auth.isLoggedIn()) {
      this.loading.set(true);
      this.postAuth.continuar();
      return;
    }

    this.interval = setInterval(() => {
      this.currentFondo.update(i => (i + 1) % this.fondos.length);
    }, 5000);
  }

  ngOnDestroy(): void {
    if (this.interval) clearInterval(this.interval);
    if (this.googleTimer) clearTimeout(this.googleTimer);
  }

  ngAfterViewInit(): void {
    this.renderGoogleButton();
  }

  /**
   * El script de Google carga con async/defer: al llegar desde el portal (carga
   * completa de página) puede no estar listo todavía, así que se reintenta.
   */
  private renderGoogleButton(intentos = 0): void {
    const contenedor = document.getElementById('google-buttonDiv');

    if (typeof google !== 'undefined' && google.accounts && contenedor) {
      google.accounts.id.initialize({
        client_id: "272016198702-6nn2d1ibdeu73v2dicuh4n0i911ei6sa.apps.googleusercontent.com",
        callback: this.handleGoogleResponse.bind(this)
      });
      google.accounts.id.renderButton(
        contenedor,
        { theme: "outline", size: "large", width: 320, text: "continue_with" }
      );
      return;
    }

    if (intentos < 50) {
      this.googleTimer = setTimeout(() => this.renderGoogleButton(intentos + 1), 200);
    }
  }

  cambiarModo(modo: 'login' | 'registro'): void {
    this.modo.set(modo);
    this.error.set('');
  }

  handleGoogleResponse(response: any): void {
    this.loading.set(true);
    this.error.set('');

    this.auth.loginGoogle(response.credential).subscribe({
      next: (res: any) => {
        this.postAuth.continuar(!!res.require_profile_completion);
      },
      error: (err) => {
        this.loading.set(false);
        const msg = err?.error?.message ?? err?.error?.error ?? 'Error al autenticar con Google.';
        this.error.set(msg);
      }
    });
  }

  checkCapsLock(event: KeyboardEvent): void {
    this.capsLock.set(event.getModifierState('CapsLock'));
  }

  goToFondo(index: number): void {
    this.currentFondo.set(index);
    if (this.interval) clearInterval(this.interval);
    this.interval = setInterval(() => {
      this.currentFondo.update(i => (i + 1) % this.fondos.length);
    }, 5000);
  }

  submit(): void {
    if (this.modo() === 'registro') {
      this.registrar();
      return;
    }

    if (!this.email || !this.password) {
      this.error.set('Ingresa tu email y contraseña.');
      return;
    }

    this.loading.set(true);
    this.error.set('');

    this.auth.login(this.email, this.password).subscribe({
      next: () => {
        this.postAuth.continuar();
      },
      error: (err) => {
        this.loading.set(false);
        const msg = err?.error?.message ?? err?.error?.error ?? 'Credenciales incorrectas.';
        this.error.set(msg);
      },
    });
  }

  private registrar(): void {
    this.ci       = this.ci.trim();
    this.telefono = this.telefono.trim();

    if (!this.nombre || !this.apellido || !this.ci || !this.telefono || !this.email || !this.password) {
      this.error.set('Por favor completa todos los campos.');
      return;
    }
    if (this.password.length < 8) {
      this.error.set('La contraseña debe tener al menos 8 caracteres.');
      return;
    }

    this.loading.set(true);
    this.error.set('');

    // La cuenta nace como participante. El CI y celular son los mismos datos que
    // registra Caja y enlazan la cuenta con sus inscripciones.
    this.auth.register(this.nombre, this.apellido, this.email, this.password, this.password, this.apellidoMaterno.trim() || null, { ci: this.ci, telefono: this.telefono }).pipe(
      switchMap(() => this.auth.completeProfile(this.ci, this.telefono))
    ).subscribe({
      next: () => {
        this.postAuth.continuar();
      },
      error: (err) => {
        // La cuenta se creó pero falló guardar CI/celular: se piden en el paso siguiente
        if (this.auth.isLoggedIn()) {
          this.postAuth.continuar(true);
          return;
        }
        this.loading.set(false);
        const errores = err?.error?.errors;
        const primero = errores ? (Object.values(errores)[0] as string[])?.[0] : null;
        const msg = primero ?? err?.error?.message ?? err?.error?.error ?? 'No se pudo crear la cuenta.';
        this.error.set(msg);
      },
    });
  }
}
