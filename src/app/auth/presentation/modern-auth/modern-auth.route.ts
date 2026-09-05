import { Routes } from "@angular/router";
import { Login } from "./login/login";
import { Register } from "./register/register";
import { VerifyEmail } from "./verify-email/verify-email";
import { TwoSteps } from "./two-steps/two-steps";
import { Logout } from "./logout/logout";
import { ResetPassword } from "./reset-password/reset-password";
import { CreatePassword } from "./create-password/create-password";
import { guestGuard } from "../../../auth/guards/guest.guard";
import { authGuard } from "../../../auth/guards/auth.guard";
import { CompletarPerfil } from "./completar-perfil/completar-perfil";

export const MODERN_AUTH_ROUTES: Routes = [
  {
    path: 'auth-modern/login',
    component: Login,
    canActivate: [guestGuard],
    data: { title: 'Iniciar sesión' },
  },
  {
    path: 'auth-modern/register',
    component: Register,
    data: { title: 'Register' },
  },
  {
    path: 'auth-modern/verify-email',
    component: VerifyEmail,
    data: { title: 'Verify Email' },
  },
  {
    path: 'auth-modern/two-steps',
    component: TwoSteps,
    data: { title: 'Two Steps' },
  },
  {
    path: 'auth-modern/logout',
    component: Logout,
    data: { title: 'Logout' },
  },
  {
    path: 'auth-modern/reset-password',
    component: ResetPassword,
    data: { title: 'Reset Password' },
  },
  {
    path: 'auth-modern/create-password',
    component: CreatePassword,
    data: { title: 'Create Password' },
  },
  {
    path: 'auth/completar-perfil',
    component: CompletarPerfil,
    canActivate: [authGuard],
    data: { title: 'Completar Perfil' },
  }
]
