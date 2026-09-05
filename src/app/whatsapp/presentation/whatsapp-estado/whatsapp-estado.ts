import { Component, inject, signal, OnInit, OnDestroy, ChangeDetectorRef } from '@angular/core';
import { NgClass } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { WhatsappService } from '../../application/services/whatsapp.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ChatbotStats } from '../../domain/models/whatsapp.model';

@Component({
  selector: 'app-whatsapp-estado',
  imports: [NgClass, NgIcon, PageTitle],
  templateUrl: './whatsapp-estado.html',
  styles: ``
})
export class WhatsappEstado implements OnInit, OnDestroy {
  private svc = inject(WhatsappService);
  private cdr = inject(ChangeDetectorRef);

  stats         = signal<ChatbotStats | null>(null);
  loadingStats  = signal(false);
  botState      = signal<string>('close');
  serviceOnline = signal<boolean | null>(null);

  private pollInterval: ReturnType<typeof setInterval> | null = null;

  ngOnInit() {
    this.cargarTodo();
    this.pollInterval = setInterval(() => this.cargarTodo(), 15000);
  }

  ngOnDestroy() {
    if (this.pollInterval) clearInterval(this.pollInterval);
  }

  cargarTodo() {

    this.svc.botProcessStatus().subscribe({
      next: proc => {
        if (!proc.running) {
          this.serviceOnline.set(false);
          this.botState.set('close');
          this.stats.set(null);
          this.cdr.detectChanges();
          return;
        }

        this.serviceOnline.set(true);
        this.cdr.detectChanges();

        this.svc.baileysStatus().subscribe({
          next: s => { this.botState.set(s.state); this.cdr.detectChanges(); },
          error: () => { this.botState.set('close'); this.cdr.detectChanges(); },
        });

        this.loadingStats.set(true);
        this.svc.getChatbotStats().subscribe({
          next: s => {
            this.stats.set(s);
            this.loadingStats.set(false);
            this.cdr.detectChanges();
          },
          error: () => {
            this.loadingStats.set(false);
            this.cdr.detectChanges();
          },
        });
      },
      error: () => {

        this.serviceOnline.set(false);
        this.botState.set('close');
        this.cdr.detectChanges();
      },
    });
  }

  get stateLabel(): string {
    const s = this.botState();
    if (s === 'open')       return 'Conectado';
    if (s === 'connecting') return 'Conectando';
    if (s === 'qr')         return 'Esperando QR';
    return 'Desconectado';
  }

  get stateBadgeClass(): string {
    const s = this.botState();
    if (s === 'open')       return 'bg-success/15 text-success';
    if (s === 'connecting') return 'bg-warning/15 text-warning';
    if (s === 'qr')         return 'bg-primary/15 text-primary';
    return 'bg-default-100 text-default-500';
  }

  get dotClass(): string {
    const s = this.botState();
    if (s === 'open')       return 'bg-success animate-pulse';
    if (s === 'connecting') return 'bg-warning animate-pulse';
    if (s === 'qr')         return 'bg-primary animate-pulse';
    return 'bg-default-300';
  }
}
