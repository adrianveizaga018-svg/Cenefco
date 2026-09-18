import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnInit, computed, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from "@ng-icons/core";
import { CertConfigProgramaService } from '../../../../../cert-config-programas/application/services/cert-config-programa.service';
import { AuthService } from '../../../../../auth/application/services/auth.service';

type MenuItem = {
  type: 'title' | 'item' | 'accordion';
  label: string;
  icon?: string;
  color?: string;
  link?: string;
  target?: string;
  badge?: number;
  permission?: string;
  children?: MenuItem[];
};

@Component({
  selector: 'app-sidebar-menu',
  imports: [NgIcon, RouterLink, CommonModule],
  templateUrl: './sidebar-menu.html',
  styles: ``
})
export class SidebarMenu implements OnInit {

  allMenus: MenuItem[] = [

    { type: 'title', label: 'General' },
    {
      type: 'item',
      label: 'Dashboard',
      icon: 'lucideLayoutDashboard',
      color: 'text-violet-500',
      link: '/dashboards/cenefco',
    },
    {
      type: 'item',
      label: 'Dashboard Gerencial',
      icon: 'lucideBarChart3',
      color: 'text-emerald-500',
      link: '/gerencia/dashboard',
      permission: 'reportes.ver',
    },
    {
      type: 'item',
      label: 'Analytics del Portal',
      icon: 'lucideBarChart2',
      color: 'text-indigo-500',
      link: '/cenefco/analytics',
      permission: 'reportes.ver',
    },

    { type: 'title', label: 'Oferta Académica' },
    {
      type: 'accordion',
      label: 'Programas',
      icon: 'lucideBookOpen',
      color: 'text-blue-500',
      children: [
        { type: 'item', label: 'Programas / Cursos',   icon: 'lucideBookOpen',      color: 'text-blue-500',   link: '/cenefco/cursos', permission: 'programas.ver' },
        { type: 'item', label: 'Versiones / Imparticiones', icon: 'lucideCalendarRange', color: 'text-blue-400', link: '/cenefco/programas-academicos', permission: 'programas.ver' },
        { type: 'item', label: 'Planes de Pago',       icon: 'lucideWallet',        color: 'text-emerald-500', link: '/cenefco/planes-academicos', permission: 'programas.ver' },
        { type: 'item', label: 'Áreas',                icon: 'lucideShapes',        color: 'text-violet-500', link: '/cenefco/areas', permission: 'programas.ver' },
        { type: 'item', label: 'Categorías / Tipos',   icon: 'lucideLayoutGrid',    color: 'text-sky-500',    link: '/cenefco/categorias-programa', permission: 'programas.ver' },
        { type: 'item', label: 'Cursos Migrados',       icon: 'lucideArchive',       color: 'text-indigo-500', link: '/cenefco/cursos-migrados', permission: 'cursos_migrados.ver' },
        { type: 'item', label: 'Importar JSON',          icon: 'lucideUpload',        color: 'text-teal-500',   link: '/cenefco/curso-migrado-importar', permission: 'cursos_migrados.crear' },
      ]
    },

    { type: 'title', label: 'Estructura Académica' },
    {
      type: 'accordion',
      label: 'Currícula',
      icon: 'lucideClipboardList',
      color: 'text-indigo-500',
      children: [
        { type: 'item', label: 'Calendario Académico',   icon: 'lucideCalendarRange', color: 'text-indigo-400', link: '/cenefco/calendario-academico', permission: 'programas.ver' },
      ]
    },
    {
      type: 'accordion',
      label: 'Control Académico',
      icon: 'lucideClipboardCheck',
      color: 'text-emerald-600',
      children: [
        { type: 'item', label: 'Cursos Activos', link: '/cenefco/control-academico', permission: 'programas.ver' },
        { type: 'item', label: 'Catálogo de Requisitos', link: '/cenefco/catalogo-requisitos', permission: 'programas.editar' },
      ]
    },
    {
      type: 'accordion',
      label: 'Docencia',
      icon: 'lucidePresentation',
      color: 'text-cyan-600',
      children: [
        { type: 'item', label: 'Docentes',               icon: 'lucideUserCheck',     color: 'text-cyan-600',  link: '/cenefco/docentes-perfil', permission: 'docentes.ver' },
        { type: 'item', label: 'Convenios',              icon: 'lucideHandshake',     color: 'text-cyan-500',  link: '/cenefco/convenios', permission: 'programas.ver' },
      ]
    },

    { type: 'title', label: 'Inscripciones y Pagos' },
    {
      type: 'item',
      label: 'Caja / Inscripción',
      icon: 'lucideMonitorSmartphone',
      color: 'text-amber-500',
      link: '/caja/inscripcion',
      permission: 'inscripciones.crear',
    },
    { type: 'item', label: 'Inscripciones',              icon: 'lucideClipboardCheck', color: 'text-teal-600',    link: '/cenefco/inscripciones', permission: 'inscripciones.ver' },
    { type: 'item', label: 'Directorio de Archivos',     icon: 'lucideFolderOpen',     color: 'text-amber-600',   link: '/cenefco/directorio-archivos', permission: 'directorio-archivos.ver' },
    {
      type: 'accordion',
      label: 'Pagos',
      icon: 'lucideCreditCard',
      color: 'text-emerald-600',
      children: [
        { type: 'item', label: 'Vendedores',              icon: 'lucideUserRoundCog',   color: 'text-orange-500',  link: '/cenefco/vendedores', permission: 'ventas.ver' },
        { type: 'item', label: 'Comisiones',              icon: 'lucideBadgeDollarSign', color: 'text-orange-600', link: '/cenefco/comisiones', permission: 'comisiones.ver' },
        { type: 'item', label: 'Compromisos de Cobro',    icon: 'lucideCalendarClock',  color: 'text-teal-500',   link: '/cenefco/compromisos-cobro', permission: 'compromisos-cobro.ver' },
        { type: 'item', label: 'Tipos de Banco',          icon: 'lucideLandmark',       color: 'text-emerald-500', link: '/cenefco/tipos-banco', permission: 'pagos.ver' },
        { type: 'item', label: 'Sueldos Docentes',        icon: 'lucideWallet',         color: 'text-violet-600',  link: '/cenefco/sueldos-docentes', permission: 'docentes.ver' },
      ]
    },

    { type: 'title', label: 'Gastos y Planillas' },
    {
      type: 'accordion',
      label: 'Nómina y Gastos',
      icon: 'lucideCreditCard',
      color: 'text-emerald-600',
      children: [
        { type: 'item', label: 'Dashboard de Gastos',     icon: 'lucideLayoutDashboard', color: 'text-emerald-600', link: '/cenefco/dashboard-gastos', permission: 'gastos.ver' },
        { type: 'item', label: 'Gastos',                  icon: 'lucideReceipt',        color: 'text-red-500',     link: '/cenefco/gastos', permission: 'gastos.ver' },
        { type: 'item', label: 'Gastos Recurrentes',      icon: 'lucideCalendarClock',  color: 'text-red-400',     link: '/cenefco/gastos-recurrentes', permission: 'gastos.ver' },
        { type: 'item', label: 'Empleados de Planta',     icon: 'lucideUsers',          color: 'text-cyan-600',    link: '/cenefco/empleados', permission: 'empleados.ver' },
        { type: 'item', label: 'Ajustes de Sueldo',       icon: 'lucideMinusCircle',    color: 'text-red-500',     link: '/cenefco/ajustes-sueldo', permission: 'empleados.editar' },
        { type: 'item', label: 'Generar Planilla',        icon: 'lucidePlayCircle',     color: 'text-cyan-500',    link: '/cenefco/generar-planilla', permission: 'planillas.crear' },
        { type: 'item', label: 'Historial de Planillas',  icon: 'lucideHistory',        color: 'text-cyan-700',    link: '/cenefco/planillas', permission: 'planillas.ver' },
        { type: 'item', label: 'Honorarios del Mes',      icon: 'lucideCalendarCheck',  color: 'text-violet-500',  link: '/cenefco/honorarios-mes', permission: 'honorarios.ver' },
        { type: 'item', label: 'Configurar Honorarios',   icon: 'lucideSettings2',      color: 'text-violet-400',  link: '/cenefco/config-honorarios', permission: 'honorarios.editar' },
        { type: 'item', label: 'Campañas Publicitarias',  icon: 'lucideMegaphone',      color: 'text-pink-600',    link: '/cenefco/campanas-publicidad', permission: 'campanas.ver' },
        { type: 'item', label: 'Reporte de Campañas',     icon: 'lucideBarChart3',      color: 'text-pink-500',    link: '/cenefco/campanas-reporte', permission: 'campanas.ver' },
        { type: 'item', label: 'Campañas de Leads',       icon: 'lucideUsers',          color: 'text-pink-400',    link: '/cenefco/campanas-leads', permission: 'leads.ver' },
      ]
    },

    { type: 'title', label: 'Certificados' },
    { type: 'item', label: 'Generación Masiva PDF',      icon: 'lucideFileText',      color: 'text-amber-500',   link: '/cenefco/certificados-masivos', permission: 'certificados.crear' },
    { type: 'item', label: 'Generar Certificados (Firma)', icon: 'lucideAward',       color: 'text-amber-600',   link: '/cenefco/certificados', permission: 'certificados.crear' },
    {
      type: 'accordion',
      label: 'Configuración',
      icon: 'lucideImage',
      color: 'text-amber-500',
      children: [
        { type: 'item', label: 'Plantillas',             icon: 'lucideImage',         color: 'text-amber-400',   link: '/cenefco/cert-plantillas', permission: 'certificados.ver' },
        { type: 'item', label: 'Certificados Post-Inscripción', icon: 'lucideSettings2', color: 'text-amber-500', link: '/cenefco/cert-config-programas', permission: 'cert-config.ver' },
        { type: 'item', label: 'Solicitudes de Certificados', icon: 'lucideInbox',     color: 'text-amber-600',   link: '/cenefco/cert-solicitudes', permission: 'cert-solicitudes.ver' },
      ]
    },

    { type: 'title', label: 'Comunicación' },
    { type: 'item', label: 'Mensajes de Contacto',       icon: 'lucideMail',          color: 'text-rose-500',    link: '/cenefco/mensajes-contacto', permission: 'contacto.ver' },
    {
      type: 'accordion',
      label: 'WhatsApp',
      icon: 'lucideMessageCircle',
      color: 'text-[#25d366]',
      children: [
        { type: 'item', label: 'Logs del Bot',            icon: 'lucideTerminal',      color: 'text-violet-500',  link: '/cenefco/whatsapp-estado', permission: 'whatsapp.ver' },
        { type: 'item', label: 'Cuentas',               icon: 'lucideSmartphone',    color: 'text-[#25d366]',   link: '/cenefco/whatsapp-cuentas', permission: 'whatsapp.ver' },
        { type: 'item', label: 'Conversaciones',         icon: 'lucideMessageCircle', color: 'text-[#25d366]',   link: '/cenefco/whatsapp-conversaciones', permission: 'whatsapp.ver' },
        { type: 'item', label: 'Asesores',               icon: 'lucideUsers',         color: 'text-[#25d366]',   link: '/cenefco/asesores', permission: 'whatsapp.ver' },
        { type: 'item', label: 'Speech de Ventas',       icon: 'lucideBrainCircuit',  color: 'text-violet-500',  link: '/cenefco/speeches-ventas', permission: 'ventas.ver' },
        { type: 'item', label: 'Motor NLU',              icon: 'lucideCpu',           color: 'text-violet-500',  link: '/cenefco/whatsapp-nlu', permission: 'whatsapp.ver' },
        { type: 'item', label: 'Intents NLU',            icon: 'lucideListTree',      color: 'text-violet-500',  link: '/cenefco/intents', permission: 'whatsapp.ver' },
      ]
    },
    {
      type: 'accordion',
      label: 'Zoom',
      icon: 'lucideVideo',
      color: 'text-blue-600',
      children: [
        { type: 'item', label: 'Cuentas',                icon: 'lucideKeyRound',      color: 'text-blue-500',    link: '/cenefco/zoom-cuentas', permission: 'zoom.ver' },
        { type: 'item', label: 'Reuniones',              icon: 'lucideCalendarDays',  color: 'text-blue-600',    link: '/cenefco/zoom-reuniones', permission: 'zoom.ver' },
        { type: 'item', label: 'Grabaciones',            icon: 'lucideFilm',          color: 'text-blue-700',    link: '/cenefco/zoom-grabaciones', permission: 'zoom.ver' },
      ]
    },

    { type: 'title', label: 'Contenido Web' },
    {
      type: 'accordion',
      label: 'Contenido',
      icon: 'lucideFileText',
      color: 'text-orange-500',
      children: [
        { type: 'item', label: 'Menús',   icon: 'lucideMenu', color: 'text-orange-500', link: '/cenefco/menus', permission: 'contenido.ver' },
        { type: 'item', label: 'Banners',                icon: 'lucideImage',         color: 'text-orange-400',  link: '/cenefco/banners', permission: 'web.ver' },
        { type: 'item', label: 'Testimonios',            icon: 'lucideQuote',         color: 'text-orange-500',  link: '/cenefco/testimonios', permission: 'contenido.ver' },
        { type: 'item', label: 'Galería de Videos',      icon: 'lucideVideo',         color: 'text-orange-600',  link: '/cenefco/galeria-videos', permission: 'contenido.ver' },
        { type: 'item', label: 'Categorías de Galería',  icon: 'lucideFolderOpen',    color: 'text-orange-400',  link: '/cenefco/galeria-categorias', permission: 'contenido.ver' },
        { type: 'item', label: 'Descargables',           icon: 'lucideDownload',      color: 'text-orange-500',  link: '/cenefco/descargables', permission: 'contenido.ver' },
        { type: 'item', label: 'Popups',                 icon: 'lucideMonitor',       color: 'text-orange-400',  link: '/cenefco/popups', permission: 'web.ver' },
        { type: 'item', label: 'Efectos Especiales',     icon: 'lucideSparkles',      color: 'text-yellow-500',  link: '/cenefco/efectos-especiales', permission: 'configuracion.ver' },
        { type: 'item', label: 'Boletines',               icon: 'lucideNewspaper',     color: 'text-orange-500',  link: '/cenefco/boletines', permission: 'noticias.ver' },
        { type: 'item', label: 'Eventos',                icon: 'lucideCalendarDays',  color: 'text-orange-600',  link: '/cenefco/eventos', permission: 'eventos.ver' },
        { type: 'item', label: 'Tipos de Evento',        icon: 'lucideTag',           color: 'text-orange-400',  link: '/cenefco/tipos-evento', permission: 'eventos.ver' },
        { type: 'item', label: 'Galería de Fotos',       icon: 'lucideCamera',        color: 'text-orange-400',  link: '/cenefco/fotos', permission: 'contenido.ver' },
        { type: 'item', label: 'FAQs',                   icon: 'lucideHelpCircle',    color: 'text-orange-500',  link: '/cenefco/faqs', permission: 'contenido.ver' },
        { type: 'item', label: 'Reseñas',                icon: 'lucideStar',          color: 'text-yellow-500',  link: '/cenefco/resenas', permission: 'contenido.ver' },
        { type: 'item', label: 'Artículos',              icon: 'lucideFileText',      color: 'text-orange-500',  link: '/cenefco/articulos', permission: 'noticias.ver' },
        { type: 'item', label: 'Etiquetas',              icon: 'lucideTag',           color: 'text-orange-400',  link: '/cenefco/etiquetas', permission: 'noticias.ver' }
      ]
    },
    {
      type: 'accordion',
      label: 'Juego de Trivia',
      icon: 'lucideGamepad2',
      color: 'text-violet-500',
      children: [
        { type: 'item', label: 'Categorías',  icon: 'lucideLayoutGrid', color: 'text-violet-500', link: '/cenefco/trivia-categorias', permission: 'trivia.ver' },
        { type: 'item', label: 'Niveles',     icon: 'lucideLayers',     color: 'text-violet-400', link: '/cenefco/trivia-niveles', permission: 'trivia.ver' },
        { type: 'item', label: 'Preguntas',   icon: 'lucideHelpCircle', color: 'text-violet-600', link: '/cenefco/trivia-preguntas', permission: 'trivia.ver' },
        { type: 'item', label: 'Ranking',     icon: 'lucideTrophy',     color: 'text-amber-500',  link: '/cenefco/trivia-ranking', permission: 'trivia.ver' },
        { type: 'item', label: 'Premios',     icon: 'lucideGift',       color: 'text-pink-500',   link: '/cenefco/trivia-premios', permission: 'trivia.ver' },
        { type: 'item', label: 'Canjes',      icon: 'lucideTicket',     color: 'text-emerald-500', link: '/cenefco/trivia-canjes', permission: 'trivia.ver' },
      ]
    },
    {
      type: 'accordion',
      label: 'Institucional',
      icon: 'lucideBuilding',
      color: 'text-slate-500',
      children: [
        { type: 'item', label: 'Autoridades',             icon: 'lucideUserStar',      color: 'text-slate-600',   link: '/cenefco/autoridades', permission: 'contenido.ver' },
        { type: 'item', label: 'Secretarías / Áreas',    icon: 'lucideBuilding2',     color: 'text-slate-500',   link: '/cenefco/secretarias', permission: 'secretarias.ver' },
        { type: 'item', label: 'Historia Institucional',  icon: 'lucideScroll',        color: 'text-slate-400',   link: '/cenefco/historia-institucional', permission: 'contenido.ver' },
        { type: 'item', label: 'Cifras Institucionales',  icon: 'lucideBarChart2',     color: 'text-slate-500',   link: '/cenefco/cifras-institucionales', permission: 'contenido.ver' },
        { type: 'item', label: 'Hitos Institucionales',   icon: 'lucideFlag',          color: 'text-slate-400',   link: '/cenefco/hitos-institucionales', permission: 'contenido.ver' },
        { type: 'item', label: 'Acreditaciones',          icon: 'lucideAward',         color: 'text-amber-500',   link: '/cenefco/acreditaciones', permission: 'contenido.ver' },
        { type: 'item', label: 'Redes Sociales',          icon: 'lucideShare2',        color: 'text-slate-500',   link: '/cenefco/redes-sociales', permission: 'web.ver' },
      ]
    },
    { type: 'item', label: 'Config. del Sitio',          icon: 'lucideGlobe',         color: 'text-slate-600',   link: '/cenefco/config-sitio', permission: 'configuracion.ver' },
    {
      type: 'accordion',
      label: 'Repositorio',
      icon: 'lucideLibrary',
      color: 'text-purple-500',
      children: [
        { type: 'item', label: 'Tesis',                  icon: 'lucideGraduationCap', color: 'text-purple-500',  link: '/cenefco/tesis', permission: 'biblioteca.ver' },
        { type: 'item', label: 'Monografías',            icon: 'lucideBookOpen',      color: 'text-purple-400',  link: '/cenefco/monografias', permission: 'biblioteca.ver' },
        { type: 'item', label: 'Revistas',               icon: 'lucideNewspaper',     color: 'text-purple-500',  link: '/cenefco/revistas', permission: 'biblioteca.ver' },
        { type: 'item', label: 'Revistas Científicas',   icon: 'lucideFlaskConical',  color: 'text-purple-600',  link: '/cenefco/revistas-cientificas', permission: 'biblioteca.ver' },
      ]
    },
    { type: 'title', label: 'Sistema' },
    {
      type: 'accordion',
      label: 'Administración',
      icon: 'lucideSettings',
      color: 'text-gray-500',
      children: [
        { type: 'item', label: 'Usuarios',               icon: 'lucideUsers',         color: 'text-gray-500',    link: '/cenefco/usuarios', permission: 'usuarios.ver' },
        { type: 'item', label: 'Usuarios del Portal',    icon: 'lucideUserRound',     color: 'text-gray-500',    link: '/cenefco/usuarios-portal', permission: 'usuarios.ver' },
        { type: 'item', label: 'Nuevo Usuario',          icon: 'lucideUserPlus',      color: 'text-gray-400',    link: '/cenefco/usuario-create', permission: 'usuarios.crear' },
        { type: 'item', label: 'Roles',                  icon: 'lucideUserCog',       color: 'text-gray-500',    link: '/cenefco/roles', permission: 'usuarios.ver' },
        { type: 'item', label: 'Permisos',               icon: 'lucideShieldCheck',   color: 'text-gray-400',    link: '/cenefco/permisos', permission: 'usuarios.ver' },
        { type: 'item', label: 'Configuraciones',        icon: 'lucideSettings2',     color: 'text-gray-500',    link: '/cenefco/configuraciones', permission: 'configuracion.ver' },
        { type: 'item', label: 'Notificaciones',         icon: 'lucideBell',          color: 'text-gray-400',    link: '/cenefco/notificaciones-sistema' },
        { type: 'item', label: 'Enviar Comunicado',      icon: 'lucideSend',          color: 'text-gray-400',    link: '/cenefco/notificaciones/enviar', permission: 'usuarios.ver' },
        { type: 'item', label: 'Comunicados Enviados',   icon: 'lucideSendHorizonal', color: 'text-gray-400',    link: '/cenefco/notificaciones/enviados', permission: 'usuarios.ver' },
      ]
    },

    { type: 'title', label: 'Área del Estudiante' },
    { type: 'item', label: 'Mi Panel',                   icon: 'lucideLayoutDashboard', color: 'text-primary-500', link: '/estudiante/dashboard', permission: 'estudiante.dashboard' },
    { type: 'item', label: 'Catálogo de Cursos',         icon: 'lucideStore',           color: 'text-rose-500',    link: '/estudiante/catalogo',  permission: 'estudiante.oferta' },
    { type: 'item', label: 'Mis Cursos',                 icon: 'lucideBookOpen',        color: 'text-blue-500',    link: '/estudiante/mis-cursos', permission: 'estudiante.cursos' },
    { type: 'item', label: 'Mis Certificados',           icon: 'lucideAward',           color: 'text-amber-500',   link: '/estudiante/mis-certificados', permission: 'estudiante.certificados' },
    { type: 'item', label: 'Mis Pagos',                  icon: 'lucideCreditCard',      color: 'text-emerald-500', link: '/estudiante/mis-pagos', permission: 'estudiante.pagos' },
  ]

  private certConfigService = inject(CertConfigProgramaService);
  private cdr = inject(ChangeDetectorRef);
  private auth = inject(AuthService);

  constructor(private router: Router) {}

  readonly menus = computed(() => this.collapseEmptyTitles(this.filterVisible(this.allMenus)));

  private filterVisible(items: MenuItem[]): MenuItem[] {
    const isEstudiante = this.auth.isEstudiante();
    const visible: MenuItem[] = [];
    for (const item of items) {
      if (item.type === 'item') {
        const isEstudianteItem = item.link?.startsWith('/estudiante/');
        if (isEstudiante) {
          if (isEstudianteItem) {
            visible.push(item);
          }
          continue;
        }
        if (!item.permission || this.auth.hasPermission(item.permission)) {
          visible.push(item);
        }
        continue;
      }
      if (item.type === 'accordion') {
        if (isEstudiante) continue;
        const children = this.filterVisible(item.children ?? []);
        if (children.length > 0) {
          visible.push({ ...item, children });
        }
        continue;
      }
      if (isEstudiante && item.label !== 'Área del Estudiante') continue;
      visible.push(item);
    }
    return visible;
  }

  private collapseEmptyTitles(items: MenuItem[]): MenuItem[] {
    const result: MenuItem[] = [];
    for (let i = 0; i < items.length; i++) {
      const item = items[i];
      if (item.type === 'title') {
        const next = items[i + 1];
        if (!next || next.type === 'title') continue;
      }
      result.push(item);
    }
    return result;
  }

  ngOnInit(): void {
    this.certConfigService.getPendientesCount().subscribe({
      next: (res) => {
        const item = this.allMenus
          .flatMap((m) => m.children ?? [])
          .find((child) => child.link === '/cenefco/cert-solicitudes');
        if (item && res.pendientes > 0) {
          item.badge = res.pendientes;
        }
        this.cdr.detectChanges();
      },
      error: () => {},
    });
  }

  isItemActive(item: any): boolean {
    const url = this.router.url;
    if (item.link && url === item.link) return true;
    if (item.children) {
      return item.children.some((child: any) => this.isItemActive(child));
    }
    return false;
  }
}
