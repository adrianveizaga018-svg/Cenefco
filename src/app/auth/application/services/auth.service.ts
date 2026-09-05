import { Injectable, inject, signal } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { tap } from 'rxjs/operators';
import { Observable } from 'rxjs';
import { extractErrorMessage } from '../../../utils/http-error';
import { NotificacionesService } from '../../../notificaciones/application/services/notificacion.service';

export interface AuthUser {
  id: number;
  nombre: string;
  apellido: string;
  email: string;
  rolId: number | null;
  rolNombre: string | null;
  permisos: string[];
  activo: boolean;
}

interface LoginResponse {
  token: string;
  user: AuthUser;
  expires_at: string | null;
  require_profile_completion?: boolean;
}

const TOKEN_KEY      = 'cenefco_token';
const USER_KEY       = 'cenefco_user';
const EXPIRES_AT_KEY = 'cenefco_token_expires_at';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private http    = inject(HttpClient);
  private router  = inject(Router);
  private notifSv = inject(NotificacionesService);

  private expiryTimer: ReturnType<typeof setTimeout> | null = null;

  currentUser = signal<AuthUser | null>(this.loadUser());

  constructor() {
    this.scheduleExpiry(localStorage.getItem(EXPIRES_AT_KEY));
  }

  login(email: string, password: string): Observable<LoginResponse> {
    return this.http.post<LoginResponse>('/api/auth/login', { email, password }).pipe(
      tap(res => {
        localStorage.setItem(TOKEN_KEY, res.token);
        localStorage.setItem(USER_KEY, JSON.stringify(res.user));
        if (res.expires_at) {
          localStorage.setItem(EXPIRES_AT_KEY, res.expires_at);
        } else {
          localStorage.removeItem(EXPIRES_AT_KEY);
        }
        this.currentUser.set(res.user);
        this.notifSv.conectar();
        this.scheduleExpiry(res.expires_at);
      })
    );
  }

  loginGoogle(idToken: string): Observable<LoginResponse> {
    return this.http.post<LoginResponse>('/api/auth/google', { id_token: idToken }).pipe(
      tap(res => {
        localStorage.setItem(TOKEN_KEY, res.token);
        localStorage.setItem(USER_KEY, JSON.stringify(res.user));
        if (res.expires_at) {
          localStorage.setItem(EXPIRES_AT_KEY, res.expires_at);
        } else {
          localStorage.removeItem(EXPIRES_AT_KEY);
        }
        this.currentUser.set(res.user);
        this.notifSv.conectar();
        this.scheduleExpiry(res.expires_at);
      })
    );
  }

  completeProfile(ci: string, telefono: string): Observable<any> {
    return this.http.post<any>('/api/auth/complete-profile', { ci, telefono });
  }

  register(nombre: string, apellido: string, email: string, password: string, passwordConfirmation: string): Observable<LoginResponse> {
    return this.http.post<LoginResponse>('/api/auth/register-publico', { nombre, apellido, email, password, password_confirmation: passwordConfirmation }).pipe(
      tap(res => {
        localStorage.setItem(TOKEN_KEY, res.token);
        localStorage.setItem(USER_KEY, JSON.stringify(res.user));
        if (res.expires_at) {
          localStorage.setItem(EXPIRES_AT_KEY, res.expires_at);
        } else {
          localStorage.removeItem(EXPIRES_AT_KEY);
        }
        this.currentUser.set(res.user);
        this.notifSv.conectar();
        this.scheduleExpiry(res.expires_at);
      })
    );
  }

  logout(): void {
    this.clearSession();
    this.http.post('/api/auth/logout', {}).subscribe({ error: (err: HttpErrorResponse) => {} });
    this.router.navigate(['/auth-modern/login']);
  }

  private expireSession(): void {
    this.clearSession();
    this.router.navigate(['/auth-modern/login'], { queryParams: { expired: '1' } });
  }

  private clearSession(): void {
    if (this.expiryTimer) {
      clearTimeout(this.expiryTimer);
      this.expiryTimer = null;
    }
    this.notifSv.desconectar();
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    localStorage.removeItem(EXPIRES_AT_KEY);
    this.currentUser.set(null);
  }

  private scheduleExpiry(expiresAt: string | null): void {
    if (this.expiryTimer) {
      clearTimeout(this.expiryTimer);
      this.expiryTimer = null;
    }
    if (!expiresAt || !this.getToken()) {
      return;
    }

    const msRestantes = new Date(expiresAt).getTime() - Date.now();
    if (msRestantes <= 0) {
      this.expireSession();
      return;
    }
    this.expiryTimer = setTimeout(() => this.expireSession(), msRestantes);
  }

  getMe(): Observable<AuthUser> {
    return this.http.get<AuthUser>('/api/auth/me').pipe(
      tap(user => {
        localStorage.setItem(USER_KEY, JSON.stringify(user));
        this.currentUser.set(user);
        this.notifSv.conectar();
      })
    );
  }

  getToken(): string | null {
    return localStorage.getItem(TOKEN_KEY);
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }

  hasPermission(codigo: string): boolean {
    const permisos = this.currentUser()?.permisos ?? [];
    return permisos.includes('*') || permisos.includes(codigo);
  }

  isAdmin(): boolean {
    return (this.currentUser()?.permisos ?? []).includes('*');
  }

  isEstudiante(): boolean {
    // Roles que van al área de estudiante (NO al dashboard de administración).
    // participante = rol por defecto del registro público
    // estudiante   = rol explícito de estudiante CENEFCO
    // ciudadano    = usuario del portal web
    const ROLES_ESTUDIANTE = ['estudiante', 'participante', 'ciudadano'];
    const user = this.currentUser();
    if (!user) return false;
    const r = (user.rolNombre ?? '').toLowerCase();
    return ROLES_ESTUDIANTE.includes(r);
  }

  forgotPassword(email: string): Observable<{ message: string }> {
    return this.http.post<{ message: string }>('/api/auth/forgot-password', { email });
  }

  resetPassword(token: string, email: string, password: string, password_confirmation: string): Observable<{ message: string }> {
    return this.http.post<{ message: string }>('/api/auth/reset-password', { token, email, password, password_confirmation });
  }

  private loadUser(): AuthUser | null {
    try {
      const raw = localStorage.getItem(USER_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch {
      return null;
    }
  }
}
