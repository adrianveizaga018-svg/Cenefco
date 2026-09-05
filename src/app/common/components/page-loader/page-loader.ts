import { Component, OnInit, signal } from '@angular/core';

const LOGOS = [
  'assets/images/cenefco/cenefco-logo.png',
  'assets/images/cenefco/azul.png',
  'assets/images/cenefco/naranja.png',
  'assets/images/cenefco/rosado.png',
  'assets/images/cenefco/mostasa.png',
  'assets/images/cenefco/verde.png',
];

@Component({
  selector: 'app-page-loader',
  standalone: true,
  template: `
    @if (visible()) {
      <div class="fixed inset-0 z-[99999] flex items-center justify-center bg-white dark:bg-zinc-900">
        <div class="relative size-[120px]">

          <div class="absolute inset-0 rounded-full border border-transparent animate-spin"
               style="border-left-color: var(--color-primary); border-right-color: var(--color-primary); animation-duration: 1.5s; animation-timing-function: linear;">
          </div>

          <div class="absolute inset-0 flex items-center justify-center">
            <div class="size-20 rounded-full flex items-center justify-center p-2.5 box-border"
                 style="background-color: rgba(0,0,0,0.75);">
              <img [src]="logo" alt="cenefco" class="w-full h-full object-contain">
            </div>
          </div>

        </div>
      </div>
    }
  `,
})
export class PageLoader implements OnInit {
  visible = signal(true);
  logo    = LOGOS[0];

  ngOnInit(): void {
    this.logo = LOGOS[Math.floor(Math.random() * LOGOS.length)];
    setTimeout(() => this.visible.set(false), 500);
  }
}
