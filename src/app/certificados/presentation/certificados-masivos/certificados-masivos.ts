import { Component } from '@angular/core';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { NgIcon } from '@ng-icons/core';

@Component({
  selector: 'app-certificados-masivos',
  template: `
    <div class="flex flex-col h-full" style="height: calc(100vh - 64px);">
      <div class="card-header px-4 py-3 border-b border-default-200 flex items-center gap-2 shrink-0">
        <ng-icon name="lucideFileText" class="text-amber-600" style="font-size:20px"></ng-icon>
        <div>
          <h4 class="font-semibold text-sm">Generación Masiva PDF</h4>
          <p class="text-xs text-default-500">Carga un Excel con nombres y genera los certificados en PDF.</p>
        </div>
      </div>
      <iframe
        [src]="iframeUrl"
        class="flex-1 w-full border-0"
        title="Generación Masiva de Certificados PDF"
        allow="downloads"
        loading="lazy">
      </iframe>
    </div>
  `,
  styles: [`
    :host { display: flex; flex-direction: column; height: 100%; }
  `],
  standalone: true,
  imports: [NgIcon],
})
export class CertificadosMasivos {
  readonly iframeUrl: SafeResourceUrl;

  constructor(sanitizer: DomSanitizer) {
    // Se añade ?embedded=true para que Django oculte su navbar y footer
    this.iframeUrl = sanitizer.bypassSecurityTrustResourceUrl(
      'http://localhost:8001/?embedded=true'
    );
  }
}
