export interface Role {
  id: number;
  nombre: string;
  descripcion: string | null;
  permisos: string[];
  activo: boolean;
  restringidoAVendedor?: boolean;
  created_at?: string;
}

export interface RolePayload {
  nombre: string;
  descripcion: string | null;
  activo: boolean;
  permisos: string[];
  restringido_a_vendedor?: boolean;
}

export interface Permiso {
  id: number;
  codigo: string;
  descripcion: string;
  modulo: string;
}

export interface GrupoPermisos {
  modulo: string;
  icon: string;
  permisos: { key: string; label: string }[];
}

const MODULOS_EXCLUIDOS = new Set<string>(['transparencia', 'tramites', 'decretos', 'informes']);

export const MODULO_ICONS: Record<string, string> = {
  usuarios: 'lucideUserCog',
  programas: 'lucideBookOpen',
  inscripciones: 'lucideClipboardList',
  notas: 'lucideGraduationCap',
  pagos: 'lucideCreditCard',
  certificados: 'lucideAward',
  docentes: 'lucideUsers',
  contenido: 'lucideFileText',
  eventos: 'lucideCalendar',
  contacto: 'lucideMail',
  configuracion: 'lucideSettings',
  reportes: 'lucideBarChart2',
  ventas: 'lucideShoppingCart',
  gastos: 'lucideWallet',
  empleados: 'lucideUserCog',
  planillas: 'lucideBanknote',
  honorarios: 'lucideBanknote',
  horarios: 'lucideCalendarClock',
  planes: 'lucideListChecks',
  moodle: 'lucideMonitor',
  whatsapp: 'lucideMessageCircle',
  zoom: 'lucideVideo',
  web: 'lucideGlobe',
  documentos: 'lucideFileText',
  historial: 'lucideHistory',
  devoluciones: 'lucideRotateCcw',
  diplomados: 'lucideGraduationCap',
  estudiantes: 'lucideUsers',
  secretarias: 'lucideBuilding2',
  sugerencias: 'lucideMessageSquare',
  asesorias: 'lucideHeadphones',
  biblioteca: 'lucideLibrary',
  cartas: 'lucideMailOpen',
  'cursos_migrados': 'lucideBookOpen',
  'cert-config': 'lucideAward',
  'cert-solicitudes': 'lucideAward',
  decretos: 'lucideGavel',
  informes: 'lucideBarChart2',
  preinscripciones: 'lucideClipboardList',
  tramites: 'lucideFolderOpen',
};

const MODULO_LABELS: Record<string, string> = {
  web: 'Sitio Web',
  'cert-config': 'Configuración de Certificados',
  'cert-solicitudes': 'Solicitudes de Certificados',
  cursos_migrados: 'Cursos Migrados',
};

export function labelDeModulo(modulo: string): string {
  if (MODULO_LABELS[modulo]) return MODULO_LABELS[modulo];
  return modulo
    .replace(/[_-]+/g, ' ')
    .split(' ')
    .map(w => w.charAt(0).toUpperCase() + w.slice(1))
    .join(' ');
}

export function agruparPermisos(catalogo: Permiso[]): GrupoPermisos[] {
  const porModulo = new Map<string, Permiso[]>();

  for (const p of catalogo) {
    if (p.codigo === '*' || MODULOS_EXCLUIDOS.has(p.modulo)) continue;
    if (!porModulo.has(p.modulo)) porModulo.set(p.modulo, []);
    porModulo.get(p.modulo)!.push(p);
  }

  return Array.from(porModulo.entries())
    .map(([modulo, permisos]) => ({
      modulo: labelDeModulo(modulo),
      icon: MODULO_ICONS[modulo] ?? 'lucideFolder',
      permisos: permisos.map(p => ({ key: p.codigo, label: p.descripcion })),
    }))
    .sort((a, b) => a.modulo.localeCompare(b.modulo));
}

const COLOR_PALETTE = [
  'bg-blue-100 text-blue-700',
  'bg-emerald-100 text-emerald-700',
  'bg-orange-100 text-orange-700',
  'bg-purple-100 text-purple-700',
  'bg-rose-100 text-rose-700',
  'bg-amber-100 text-amber-700',
  'bg-cyan-100 text-cyan-700',
  'bg-indigo-100 text-indigo-700',
];

export function agruparPermisosAsignados(catalogo: Permiso[], asignados: string[]): { modulo: string; icon: string; color: string; labels: string[] }[] {
  return agruparPermisos(catalogo)
    .map((g, i) => ({
      modulo: g.modulo,
      icon: g.icon,
      color: COLOR_PALETTE[i % COLOR_PALETTE.length],
      labels: g.permisos.filter(p => asignados.includes(p.key)).map(p => p.label),
    }))
    .filter(g => g.labels.length > 0);
}
