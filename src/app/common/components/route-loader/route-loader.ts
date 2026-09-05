import { Component, OnInit, OnDestroy, signal, inject, ChangeDetectorRef } from '@angular/core';
import { Router, NavigationStart, NavigationEnd, NavigationCancel, NavigationError } from '@angular/router';
import { Subscription } from 'rxjs';

const MIN_SHOW_MS = 450;

@Component({
  selector: 'app-route-loader',
  template: `
    @if (visible()) {
      <div class="fixed top-0 left-0 right-0 z-[9999] h-1 bg-primary/20 overflow-hidden">
        <div
          class="h-full bg-primary transition-all duration-300 ease-out"
          [style.width.%]="progress()"
        ></div>
      </div>

      <div class="fixed inset-0 z-[9998] bg-white/50 dark:bg-zinc-900/50 backdrop-blur-[1px] flex items-center justify-center pointer-events-none">
        <div class="bg-card shadow-lg rounded-2xl px-6 py-4 flex items-center gap-3 border border-default-100">
          <div class="relative size-6">
            <div class="absolute inset-0 rounded-full border-2 border-primary/20"></div>
            <div class="absolute inset-0 rounded-full border-2 border-transparent border-t-primary animate-spin"></div>
          </div>
          <span class="text-sm font-medium text-default-600">Cargando módulo...</span>
        </div>
      </div>
    }
  `,
  styles: ``
})
export class RouteLoader implements OnInit, OnDestroy {
  private router = inject(Router);
  private cdr    = inject(ChangeDetectorRef);

  visible  = signal(false);
  progress = signal(0);

  private sub!: Subscription;
  private hideTimer: ReturnType<typeof setTimeout> | null = null;
  private progressTimer: ReturnType<typeof setTimeout> | null = null;
  private showAt = 0;

  ngOnInit(): void {
    this.sub = this.router.events.subscribe(event => {
      if (event instanceof NavigationStart) {
        this.showLoader();
      } else if (
        event instanceof NavigationEnd   ||
        event instanceof NavigationCancel ||
        event instanceof NavigationError
      ) {
        this.hideLoader();
      }
    });
  }

  private showLoader(): void {
    if (this.hideTimer) { clearTimeout(this.hideTimer); this.hideTimer = null; }
    this.showAt = Date.now();
    this.progress.set(20);
    this.visible.set(true);
    this.cdr.detectChanges();

    this.progressTimer = setTimeout(() => { this.progress.set(60); this.cdr.detectChanges(); }, 150);
    setTimeout(() => { this.progress.set(80); this.cdr.detectChanges(); }, 350);
  }

  private hideLoader(): void {
    if (this.progressTimer) { clearTimeout(this.progressTimer); }
    const elapsed   = Date.now() - this.showAt;
    const remaining = Math.max(0, MIN_SHOW_MS - elapsed);

    this.hideTimer = setTimeout(() => {
      this.progress.set(100);
      this.cdr.detectChanges();
      setTimeout(() => {
        this.visible.set(false);
        this.progress.set(0);
        this.cdr.detectChanges();
      }, 200);
    }, remaining);
  }

  ngOnDestroy(): void {
    this.sub?.unsubscribe();
    if (this.hideTimer)     clearTimeout(this.hideTimer);
    if (this.progressTimer) clearTimeout(this.progressTimer);
  }
}
