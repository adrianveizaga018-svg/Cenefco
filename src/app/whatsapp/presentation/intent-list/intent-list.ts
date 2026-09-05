import { Component, inject, signal, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgClass } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { WhatsappService } from '../../application/services/whatsapp.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { WhatsappBotBanner } from '../../components/whatsapp-bot-banner/whatsapp-bot-banner';
import { ToastService } from '../../../common/application/services/toast.service';
import { IntentRecord } from '../../domain/models/whatsapp.model';

@Component({
  selector: 'app-intent-list',
  standalone: true,
  imports: [NgClass, NgIcon, FormsModule, RouterLink, PageTitle, WhatsappBotBanner],
  templateUrl: './intent-list.html',
})
export class IntentList implements OnInit {
  private svc   = inject(WhatsappService);
  private toast = inject(ToastService);
  private cdr   = inject(ChangeDetectorRef);

  intents  = signal<IntentRecord[]>([]);
  loading  = signal(true);
  query    = '';
  dominio  = '';
  deleting = signal<number | null>(null);

  readonly DOMAINS = [
    { key: 'general',   label: 'General',   color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' },
    { key: 'academico', label: 'Académico',  color: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' },
    { key: 'contenido', label: 'Contenido',  color: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300' },
  ];

  ngOnInit() {
    this.load();
  }

  load() {
    this.loading.set(true);
    this.svc.getIntentList({ query: this.query, dominio: this.dominio }).subscribe({
      next: r => {
        this.intents.set(r.data);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.loading.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  onSearch() {
    this.load();
  }

  filterByDomain(d: string) {
    this.dominio = this.dominio === d ? '' : d;
    this.load();
  }

  toggle(intent: IntentRecord) {
    this.svc.toggleIntent(intent.id).subscribe({
      next: r => {
        intent.activo = r.activo;
        this.cdr.detectChanges();
      },
      error: () => this.toast.error('Error', 'No se pudo cambiar el estado.'),
    });
  }

  confirmDelete(intent: IntentRecord) {
    this.deleting.set(intent.id);
    this.cdr.detectChanges();
  }

  cancelDelete() {
    this.deleting.set(null);
    this.cdr.detectChanges();
  }

  doDelete(intent: IntentRecord) {
    this.svc.deleteIntent(intent.id).subscribe({
      next: () => {
        this.intents.set(this.intents().filter(i => i.id !== intent.id));
        this.deleting.set(null);
        this.toast.success('Eliminado', `Intent "${intent.nombre}" eliminado.`);
        this.cdr.detectChanges();
      },
      error: () => {
        this.deleting.set(null);
        this.toast.error('Error', 'No se pudo eliminar el intent.');
        this.cdr.detectChanges();
      },
    });
  }

  domainBadge(d: string): string {
    return this.DOMAINS.find(x => x.key === d)?.color ?? 'bg-default-100 text-default-600';
  }

  intentsByDomain(d: string): IntentRecord[] {
    return this.intents().filter(i => i.dominio === d);
  }
}
