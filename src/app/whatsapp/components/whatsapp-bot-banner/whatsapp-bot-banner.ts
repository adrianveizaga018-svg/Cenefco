import { Component, ElementRef, OnDestroy, OnInit, ViewChild, ViewEncapsulation } from '@angular/core';

@Component({
  selector: 'app-whatsapp-bot-banner',
  standalone: true,
  templateUrl: './whatsapp-bot-banner.html',
  encapsulation: ViewEncapsulation.None,
})
export class WhatsappBotBanner implements OnInit, OnDestroy {
  @ViewChild('bannerCanvas', { static: true }) canvasRef!: ElementRef<HTMLCanvasElement>;

  private granimInstance: any = null;

  ngOnInit(): void {
    import('granim').then(({ default: Granim }) => {
      this.granimInstance = new Granim({
        element: this.canvasRef.nativeElement,
        direction: 'diagonal',
        isPausedWhenNotInView: true,
        states: {
          'default-state': {
            gradients: [
              ['#07435B', '#128AA2'],
              ['#128AA2', '#A9F9FF'],
              ['#A9F9FF', '#128AA2'],
              ['#128AA2', '#07435B'],
            ],
            transitionSpeed: 3000,
          },
        },
      });
    });
  }

  ngOnDestroy(): void {
    this.granimInstance?.destroy();
  }
}
