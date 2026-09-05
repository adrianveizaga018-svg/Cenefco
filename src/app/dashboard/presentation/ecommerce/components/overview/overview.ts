import { Component, Input, OnChanges } from '@angular/core';
import { NgIcon } from "@ng-icons/core";
import { RouterLink } from '@angular/router';
import { DashboardResumen } from '../../../../domain/models/dashboard.model';

type StatCard = {
  icon: string;
  bgClass: string;
  textClass: string;
  value: string;
  label: string;
  link: string;
};

@Component({
  selector: 'app-overview',
  imports: [NgIcon, RouterLink],
  templateUrl: './overview.html',
})
export class Overview implements OnChanges {
  @Input() resumen!: DashboardResumen;

  stats: StatCard[] = [];

  ngOnChanges(): void {
    if (!this.resumen) return;
    this.stats = [
      {
        icon: 'lucideClipboardCheck',
        bgClass: 'bg-primary/10',
        textClass: 'text-primary',
        value: String(this.resumen.total_inscripciones),
        label: 'Inscripciones Activas',
        link: '/cenefco/inscripciones',
      },
      {
        icon: 'lucideBookOpen',
        bgClass: 'bg-success/10',
        textClass: 'text-success',
        value: String(this.resumen.total_cursos),
        label: 'Programas / Cursos',
        link: '/cenefco/cursos',
      },
      {
        icon: 'lucideUserCheck',
        bgClass: 'bg-info/10',
        textClass: 'text-info',
        value: String(this.resumen.total_docentes),
        label: 'Docentes Activos',
        link: '/cenefco/docentes-perfil',
      },
      {
        icon: 'lucideArchive',
        bgClass: 'bg-indigo-500/10',
        textClass: 'text-indigo-500',
        value: String(this.resumen.cursos_migrados),
        label: 'Cursos Migrados',
        link: '/cenefco/cursos-migrados',
      },
      {
        icon: 'lucideUsers',
        bgClass: 'bg-violet-500/10',
        textClass: 'text-violet-500',
        value: String(this.resumen.participantes_migrados),
        label: 'Participantes Migrados',
        link: '/cenefco/cursos-migrados',
      },
      {
        icon: 'lucideMessageCircle',
        bgClass: 'bg-violet-500/10',
        textClass: 'text-violet-500',
        value: String(this.resumen.conversaciones_whatsapp),
        label: 'Conversaciones WhatsApp',
        link: '/cenefco/whatsapp-conversaciones',
      },
    ];
  }
}
