import { Component, inject, signal, OnInit, OnDestroy, ChangeDetectorRef } from '@angular/core';
import { NgClass } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { WhatsappService } from '../../application/services/whatsapp.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type UIState = 'checking' | 'stopped' | 'starting' | 'connecting' | 'qr' | 'connected' | 'stopping' | 'error';

@Component({
  selector: 'app-whatsapp-cuentas',
  imports: [NgClass, NgIcon, PageTitle],
  templateUrl: './whatsapp-cuentas.html',
  styles: ``
})
export class WhatsappCuentas implements OnInit, OnDestroy {
  private svc   = inject(WhatsappService);
  private toast = inject(ToastService);
  private cdr   = inject(ChangeDetectorRef);

  uiState     = signal<UIState>('checking');
  qrDataUrl   = signal<string | null>(null);
  processPid  = signal<string | null>(null);
  showLogs    = signal(false);
  logsContent = signal('');

  private pollInterval: ReturnType<typeof setInterval> | null = null;
  private qrRefreshInterval: ReturnType<typeof setInterval> | null = null;
  private errorCount    = 0;
  private connectingFired = false;

  ngOnInit() {
    this.poll();
    this.pollInterval = setInterval(() => this.poll(), 4000);
  }

  ngOnDestroy() {
    if (this.pollInterval)      clearInterval(this.pollInterval);
    if (this.qrRefreshInterval) clearInterval(this.qrRefreshInterval);
  }

  private poll() {
    const cur = this.uiState();
    if (cur === 'starting' || cur === 'stopping') return;

    this.svc.botProcessStatus().subscribe({
      next: proc => {
        this.errorCount = 0;

        if (!proc.running) {
          this.processPid.set(null);
          this.qrDataUrl.set(null);
          this.connectingFired = false;
          this.stopQrRefresh();
          this.uiState.set('stopped');
          this.cdr.detectChanges();
          return;
        }

        this.processPid.set(proc.pid);

        this.svc.baileysStatus().subscribe({
          next: ws => {
            if (ws.state === 'open') {
              this.connectingFired = false;
              this.uiState.set('connected');
              this.qrDataUrl.set(null);
              this.stopQrRefresh();

            } else if (ws.hasQr) {
              this.connectingFired = false;
              if (cur !== 'qr') {
                this.loadQr();
                this.startQrRefresh();
              }
              this.uiState.set('qr');

            } else if (ws.state === 'close') {

              if (!this.connectingFired) {
                this.connectingFired = true;
                this.uiState.set('connecting');
                this.svc.botConnectActive().subscribe({
                  next: () => {},
                  error: () => { this.connectingFired = false; },
                });

                setTimeout(() => { this.connectingFired = false; }, 15000);
              } else {
                this.uiState.set('connecting');
              }

            } else {

              this.uiState.set('connecting');
            }

            this.cdr.detectChanges();
          },
          error: () => {
            this.uiState.set('connecting');
            this.cdr.detectChanges();
          },
        });
      },
      error: () => {
        this.errorCount++;
        if (this.errorCount >= 3) {
          this.stopQrRefresh();
          this.connectingFired = false;
          this.uiState.set('error');
          this.cdr.detectChanges();
        }
      },
    });
  }

  private startQrRefresh() {
    if (this.qrRefreshInterval) return;
    this.qrRefreshInterval = setInterval(() => {
      if (this.uiState() === 'qr') this.loadQr();
    }, 18000);
  }

  private stopQrRefresh() {
    if (this.qrRefreshInterval) {
      clearInterval(this.qrRefreshInterval);
      this.qrRefreshInterval = null;
    }
  }

  private loadQr() {
    this.svc.baileysQr().subscribe({
      next: r  => { this.qrDataUrl.set(r.qr); this.cdr.detectChanges(); },
      error: () => {},
    });
  }

  iniciar() {
    this.uiState.set('starting');
    this.cdr.detectChanges();
    this.svc.botStart().subscribe({
      next: () => {
        setTimeout(() => {
          this.connectingFired = false;
          this.uiState.set('connecting');
          this.poll();
          this.cdr.detectChanges();
        }, 2000);
      },
      error: err => {
        if (err?.status === 501) {
          this.toast.warning(
            'Inicio manual requerido',
            'Ejecutá "npm start" en la carpeta whatsapp-service, o "pm2 start cenefco-whatsapp" en el VPS.'
          );
        } else {
          this.toast.error('Error', err?.error?.message ?? 'No se pudo iniciar el bot.');
        }
        this.uiState.set('stopped');
        this.cdr.detectChanges();
      },
    });
  }

  detener() {
    this.uiState.set('stopping');
    this.cdr.detectChanges();
    this.svc.botStop().subscribe({
      next: () => {
        this.qrDataUrl.set(null);
        this.connectingFired = false;
        this.stopQrRefresh();
        this.uiState.set('stopped');
        this.toast.success('Detenido', 'El bot fue detenido correctamente.');
        this.cdr.detectChanges();
      },
      error: err => {
        if (err?.status === 501) {
          this.toast.warning('Detención manual', 'Ejecutá "pm2 stop cenefco-whatsapp" en el VPS.');
        } else {
          this.toast.error('Error', err?.error?.message ?? 'No se pudo detener el bot.');
        }
        this.uiState.set('checking');
        this.poll();
        this.cdr.detectChanges();
      },
    });
  }

  desconectarWhatsApp() {
    Swal.fire({
      title: '¿Desvincular WhatsApp?',
      text: 'Se cerrará la sesión. Tendrás que escanear el QR nuevamente para reconectar.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, desvincular',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.svc.botDisconnect().subscribe({
        next: () => {
          this.qrDataUrl.set(null);

          this.connectingFired = true;
          this.stopQrRefresh();
          this.uiState.set('connecting');
          this.toast.success('Desvinculado', 'WhatsApp desvinculado. Generando QR para reconectar...');
          this.cdr.detectChanges();

          setTimeout(() => { this.connectingFired = false; }, 10000);
        },
        error: () => this.toast.error('Error', 'No se pudo desvincular WhatsApp.'),
      });
    });
  }

  refrescarQr() { this.loadQr(); }

  reintentar() {
    this.errorCount = 0;
    this.connectingFired = false;
    this.uiState.set('checking');
    this.cdr.detectChanges();
    this.poll();
  }

  flushingCache = signal(false);

  flushCache() {
    this.flushingCache.set(true);
    this.cdr.detectChanges();
    this.svc.botFlushCache().subscribe({
      next: () => {
        this.toast.success('Caché actualizada', 'Los intents y settings del bot fueron recargados.');
        this.flushingCache.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo recargar la caché. ¿El bot está corriendo?');
        this.flushingCache.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  toggleLogs() {
    this.showLogs.update(v => !v);
    if (this.showLogs()) {
      this.svc.botLogs().subscribe({ next: r => { this.logsContent.set(r.logs); this.cdr.detectChanges(); } });
    }
  }
}
