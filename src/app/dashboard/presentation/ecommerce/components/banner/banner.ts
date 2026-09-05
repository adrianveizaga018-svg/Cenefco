import { Component, inject } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { AuthService } from '../../../../../auth/application/services/auth.service';

@Component({
  selector: 'app-banner',
  imports: [RouterLink, NgIcon],
  templateUrl: './banner.html',
})
export class Banner {
  private auth = inject(AuthService);

  get isAdmin(): boolean {
    return this.auth.isAdmin();
  }

  quickLinks = [
    { icon: 'lucideClipboardCheck', label: 'Inscripciones',    ruta: '/cenefco/inscripciones' },
    { icon: 'lucideBanknote',       label: 'Cursos',            ruta: '/cenefco/cursos' },
    { icon: 'lucideUserCheck',      label: 'Docentes',         ruta: '/cenefco/docentes-perfil' },
    { icon: 'lucideMessageCircle',  label: 'WhatsApp',         ruta: '/cenefco/whatsapp-conversaciones' },
  ];
}
