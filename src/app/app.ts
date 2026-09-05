import { Component, DestroyRef, inject } from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { Event, NavigationEnd, Router, RouterOutlet } from '@angular/router';
import * as LucideIcons  from '@ng-icons/lucide';
import * as tablerIcons from '@ng-icons/tabler-icons';
import * as tablerIconsFill from '@ng-icons/tabler-icons/fill';
import {  provideIcons } from '@ng-icons/core';
import { TitleService } from './common/application/services/title.service';
import { ToastContainer } from './common/components/toast-container/toast-container';
import { PageLoader } from './common/components/page-loader/page-loader';
import { AuthService } from './auth/application/services/auth.service';
import { NotificacionesService } from './notificaciones/application/services/notificacion.service';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, ToastContainer, PageLoader],
  templateUrl: './app.html',
  styleUrl: './app.scss',
   providers: [provideIcons({...LucideIcons,...tablerIcons, ...tablerIconsFill})],
})

export class App {
  private router      = inject(Router);
  private titleService = inject(TitleService);
  private destroyRef  = inject(DestroyRef);
  private auth        = inject(AuthService);
  private notifSv     = inject(NotificacionesService);

  ngOnInit() {
    this.titleService.init();
    this.router.events
      .pipe(takeUntilDestroyed(this.destroyRef))
      .subscribe((event: Event) => {
        if (event instanceof NavigationEnd) {
          setTimeout(() => window.HSStaticMethods.autoInit(), 100);
        }
      });





    if (this.auth.isLoggedIn()) {
      this.notifSv.conectar();
    }
  }
}
