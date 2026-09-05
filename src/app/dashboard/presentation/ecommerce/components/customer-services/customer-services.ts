import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from "@ng-icons/core";

type AccesoRapido = { icon: string; label: string; ruta: string; bgClass: string; textClass: string; };

@Component({
  selector: 'app-customer-services',
  imports: [NgIcon, RouterLink],
  templateUrl: './customer-services.html',
})
export class CustomerServices {
  accesos: AccesoRapido[] = [
    { icon: 'lucideClipboardCheck',  label: 'Inscripciones',     ruta: '/cenefco/inscripciones',          bgClass: 'bg-primary/10',     textClass: 'text-primary' },
{ icon: 'lucideBanknote',        label: 'Pagos',             ruta: '/cenefco/pagos-academicos',       bgClass: 'bg-success/10',     textClass: 'text-success' },
    { icon: 'lucideAward',           label: 'Certificados',      ruta: '/cenefco/lista-aprobados',        bgClass: 'bg-amber-500/10',   textClass: 'text-amber-500' },
    { icon: 'lucideScrollText',      label: 'Cartas',            ruta: '/cenefco/cartas-generadas',       bgClass: 'bg-violet-500/10',  textClass: 'text-violet-500' },
    { icon: 'lucideUserCheck',       label: 'Docentes',          ruta: '/cenefco/docentes-perfil',        bgClass: 'bg-teal-500/10',    textClass: 'text-teal-500' },
    { icon: 'lucideBookOpen',        label: 'Programas',         ruta: '/cenefco/cursos',                 bgClass: 'bg-orange-500/10',  textClass: 'text-orange-500' },
    { icon: 'lucideBarChart2',       label: 'Analytics',         ruta: '/cenefco/analytics',              bgClass: 'bg-indigo-500/10',  textClass: 'text-indigo-500' },
    { icon: 'lucideMessageCircle',   label: 'WhatsApp',          ruta: '/cenefco/whatsapp-conversaciones',bgClass: 'bg-[#25d366]/10',   textClass: 'text-[#25d366]' },
    { icon: 'lucideUsers',           label: 'Usuarios',          ruta: '/cenefco/usuarios',               bgClass: 'bg-rose-500/10',    textClass: 'text-rose-500' },
  ];
}
