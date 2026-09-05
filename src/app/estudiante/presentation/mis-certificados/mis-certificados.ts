import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { EstudianteService, CertificadoEstudiante } from '../../application/services/estudiante.service';

/** URL del portal público — cambiar en producción */
const PORTAL_URL = 'http://localhost:4201';

@Component({
  selector: 'app-mis-certificados',
  standalone: true,
  imports: [CommonModule, NgIcon, PageTitle],
  template: `
    <div class="p-6 md:p-10 space-y-8 animate-in fade-in duration-500 pb-20">
      <app-page-title title="Mis Certificados Oficiales" subtitle="Descarga tus certificados digitales acreditados en formato PDF"></app-page-title>

      @if (cargando()) {
        <div class="flex justify-center p-20">
          <div class="h-12 w-12 animate-spin rounded-full border-4 border-amber-500 border-t-transparent shadow-lg shadow-amber-500/20"></div>
        </div>
      } @else if (certificados().length === 0) {
        <div class="card p-16 text-center border border-default-200 rounded-3xl bg-white shadow-sm hover:shadow-xl transition-all">
          <div class="mx-auto h-24 w-24 bg-amber-50 text-amber-300 rounded-full flex items-center justify-center mb-6">
            <ng-icon name="lucideFileWarning" style="font-size: 56px;"></ng-icon>
          </div>
          <h3 class="text-xl font-bold text-default-900">Aún no tienes certificados</h3>
          <p class="text-sm text-default-500 max-w-sm mx-auto mt-2 leading-relaxed">Una vez completados tus cursos y generados tus certificados, aparecerán aquí listos para descargar.</p>
        </div>
      } @else {
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
          @for (cert of certificados(); track cert.id) {
            <div class="group card bg-white border border-default-200 hover:border-amber-300 rounded-[2rem] p-6 sm:p-8 space-y-6 shadow-sm hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
              
              <!-- Decoration -->
              <div class="absolute -right-12 -top-12 w-40 h-40 bg-gradient-to-br from-amber-50 to-transparent rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700 pointer-events-none"></div>

              <div class="space-y-4 relative z-10">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <span class="inline-flex items-center gap-1.5 text-xs font-black px-3 py-1 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 text-white shadow-md shadow-amber-500/20 tracking-wide">
                    <ng-icon name="lucideAward" style="font-size: 14px;"></ng-icon> CERTIFICADO OFICIAL
                  </span>
                  <span class="text-[10px] font-bold text-default-400 tracking-widest uppercase bg-default-100 px-2 py-0.5 rounded-md flex items-center gap-1.5 border border-default-200">
                    <ng-icon name="lucideScanLine" class="text-primary-500"></ng-icon> ID: {{ cert.codigo_verificacion }}
                  </span>
                </div>

                <h3 class="text-xl sm:text-2xl font-black text-default-900 leading-tight tracking-tight mt-2">
                  {{ cert.programa_en_certificado }}
                </h3>

                <div class="bg-default-50 p-3 sm:p-4 rounded-2xl border border-default-100 flex items-center gap-3 mt-4">
                  <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center shrink-0 shadow-inner">
                    <ng-icon name="lucideUser" style="font-size: 20px;"></ng-icon>
                  </div>
                  <div>
                    <p class="text-[10px] font-bold text-default-500 uppercase tracking-wider mb-0.5">Otorgado a</p>
                    <p class="text-sm font-bold text-default-900">{{ cert.nombre_en_certificado }}</p>
                  </div>
                </div>

                <p class="text-xs font-semibold text-default-500 flex items-center gap-2 pt-2">
                  <ng-icon name="lucideCalendarCheck" class="text-amber-500 text-base"></ng-icon>
                  Emitido el {{ cert.created_at | date:'longDate' }}
                </p>
              </div>

              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-5 border-t border-default-100 relative z-10">
                @if (cert.archivo_url) {
                  <a [href]="cert.archivo_url" target="_blank" download class="w-full sm:w-auto px-5 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-bold hover:from-amber-600 hover:to-orange-600 shadow-lg shadow-amber-500/25 hover:shadow-xl hover:shadow-amber-500/40 transition-all inline-flex items-center justify-center gap-2 active:scale-95 group/btn">
                    <ng-icon name="lucideDownload" style="font-size: 18px;" class="group-hover/btn:-translate-y-0.5 transition-transform"></ng-icon> Descargar PDF
                  </a>
                } @else {
                  <span class="text-sm font-bold text-default-400 italic flex items-center gap-2 bg-default-50 px-4 py-2 rounded-lg">
                    <ng-icon name="lucideLoader2" class="animate-spin"></ng-icon> PDF procesándose...
                  </span>
                }

                @if (cert.codigo_verificacion) {
                  <a [href]="portalUrl + '/verificar-certificado?codigo=' + cert.codigo_verificacion" target="_blank" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border-2 border-primary-100 text-primary-600 hover:border-primary-500 hover:bg-primary-50 text-sm font-bold transition-all inline-flex items-center justify-center gap-2 active:scale-95 group/qr">
                    <ng-icon name="lucideScanBarcode" class="group-hover/qr:scale-110 transition-transform"></ng-icon> Verificar QR
                  </a>
                }
              </div>
            </div>
          }
        </div>
      }
    </div>
  `
})
export class MisCertificadosComponent implements OnInit {
  private service = inject(EstudianteService);
  certificados = signal<CertificadoEstudiante[]>([]);
  cargando = signal(true);
  readonly portalUrl = PORTAL_URL;

  ngOnInit(): void {
    this.service.getMisCertificados().subscribe({
      next: res => {
        this.certificados.set(res.data);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false)
    });
  }
}
