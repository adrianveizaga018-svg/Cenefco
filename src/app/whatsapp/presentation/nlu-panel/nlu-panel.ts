import { Component, inject, signal, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgClass, DecimalPipe } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { WhatsappService } from '../../application/services/whatsapp.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { WhatsappBotBanner } from '../../components/whatsapp-bot-banner/whatsapp-bot-banner';
import { NluIntent, NluTestResult, NluContextRef } from '../../domain/models/whatsapp.model';

@Component({
  selector: 'app-nlu-panel',
  standalone: true,
  imports: [NgClass, NgIcon, DecimalPipe, FormsModule, RouterLink, PageTitle, WhatsappBotBanner],
  templateUrl: './nlu-panel.html',
})
export class NluPanel implements OnInit {
  private svc = inject(WhatsappService);
  private cdr = inject(ChangeDetectorRef);

  intents     = signal<NluIntent[]>([]);
  loading     = signal(true);
  expandedSlug = signal<string | null>(null);

  testText      = '';
  testContexts  = '';
  testing       = signal(false);
  testResult    = signal<NluTestResult | null>(null);

  readonly LAYERS = [
    {
      icon: 'lucideZap',
      color: 'text-amber-500',
      bg: 'bg-amber-50 dark:bg-amber-900/20',
      title: 'Events',
      desc: 'Disparan un intent sin que el usuario escriba nada.',
      code: "events: ['WELCOME']",
    },
    {
      icon: 'lucideArrowRightToLine',
      color: 'text-blue-500',
      bg: 'bg-blue-50 dark:bg-blue-900/20',
      title: 'Input Contexts',
      desc: 'El intent solo es elegible si estos contextos están activos.',
      code: "inputContexts: ['inscripcion-flow']",
    },
    {
      icon: 'lucideArrowRightFromLine',
      color: 'text-violet-500',
      bg: 'bg-violet-50 dark:bg-violet-900/20',
      title: 'Output Contexts',
      desc: 'Se activan después del match. Habilitan intents de seguimiento.',
      code: "outputContexts: [{ name: 'inscripcion-flow', lifespan: 3 }]",
    },
    {
      icon: 'lucideMessageSquare',
      color: 'text-green-500',
      bg: 'bg-green-50 dark:bg-green-900/20',
      title: 'Training Phrases',
      desc: 'Frases de ejemplo. Jaccard similarity detecta variaciones.',
      code: "'como me inscribo', 'quiero inscribirme', ...",
    },
    {
      icon: 'lucideBotMessageSquare',
      color: 'text-rose-500',
      bg: 'bg-rose-50 dark:bg-rose-900/20',
      title: 'Responses',
      desc: 'Variantes de respuesta directa. Se selecciona una aleatoriamente.',
      code: "responses: ['Hola! 😊', 'Bienvenido! 👋', ...]",
    },
  ];

  ngOnInit() {
    this.svc.getIntents().subscribe({
      next: r => {
        this.intents.set(r.intents);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.loading.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  toggleExpand(slug: string) {
    this.expandedSlug.set(this.expandedSlug() === slug ? null : slug);
    this.cdr.detectChanges();
  }

  runTest() {
    if (!this.testText.trim()) return;
    this.testing.set(true);
    this.testResult.set(null);
    this.cdr.detectChanges();

    const ctx: NluContextRef[] = this.testContexts.trim()
      ? this.testContexts.split(',')
          .map(s => s.trim())
          .filter(Boolean)
          .map(name => ({ name, lifespan: 5 }))
      : [];

    this.svc.testNlu(this.testText.trim(), ctx).subscribe({
      next: r => {
        this.testResult.set(r);
        this.testing.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.testing.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  clearTest() {
    this.testText     = '';
    this.testContexts = '';
    this.testResult.set(null);
    this.cdr.detectChanges();
  }

  domainBadge(domain: string): string {
    return {
      general:   'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
      academico: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
      contenido: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
    }[domain] ?? 'bg-default-100 text-default-600';
  }

  confidenceBadge(label: string): string {
    return {
      'ALTA':     'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
      'MEDIA':    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
      'BAJA':     'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
      'MUY BAJA': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
    }[label] ?? 'bg-default-100 text-default-600';
  }

  sourceBadge(source: string): string {
    return source === 'speech'
      ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300'
      : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
  }

  confidenceBar(conf: number): number {
    return Math.round(conf * 100);
  }

  intentsByDomain(domain: string): NluIntent[] {
    return this.intents().filter(i => i.domain === domain);
  }

  get domains(): { key: string; label: string; icon: string; color: string }[] {
    return [
      { key: 'general',   label: 'General',   icon: 'lucideGlobe',      color: 'text-blue-500' },
      { key: 'academico', label: 'Académico',  icon: 'lucideGraduationCap', color: 'text-green-500' },
      { key: 'contenido', label: 'Contenido',  icon: 'lucideNewspaper',  color: 'text-orange-500' },
    ];
  }
}
