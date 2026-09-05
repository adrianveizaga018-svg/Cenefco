import { Component, Input, inject } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from "@ng-icons/core";
import { AuthService } from '../../../auth/application/services/auth.service';

@Component({
  selector: 'app-page-title',
  imports: [RouterLink, NgIcon],
  templateUrl: './page-title.html',
  styles: ``
})
export class PageTitle {
    private auth = inject(AuthService);

    @Input() title: string = 'Welcome!';
    @Input() subtitle: string | null = null;
    @Input() showBreadcrumb?: boolean;

    get shouldShowBreadcrumb(): boolean {
      // Si se fuerza el valor por @Input, se respeta
      if (this.showBreadcrumb !== undefined) return this.showBreadcrumb;
      // Por defecto, se muestra siempre EXCEPTO a los estudiantes
      return !this.auth.isEstudiante();
    }
}
