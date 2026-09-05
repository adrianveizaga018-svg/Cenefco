export type Prioridad = 'baja' | 'media' | 'alta' | 'critica';
export type Destinatario = 'usuario' | 'rol' | 'todos';

export interface Notificacion {
  id:            number;
  tipo:          string;
  titulo:        string;
  mensaje:       string | null;
  prioridad:     Prioridad;
  destinatario:  Destinatario;
  icono:         string | null;
  color:         string | null;
  url_accion:    string | null;
  imagen_url:    string | null;
  imagen_nombre: string | null;
  leida:         boolean;
  leida_at:      string | null;
  expires_at:    string | null;
  created_at:    string;
}

export interface NotificacionGrupo {
  tipo:  string;
  items: Notificacion[];
  count: number;
}

export interface NoLeidasResponse {
  count: number;
  items: Notificacion[];
}

export interface ListResponse<T = Notificacion> {
  data:  T[];
  total: number;
}

export interface ResumenEnviado {
  tipo:           string;
  titulo:         string;
  mensaje:        string;
  prioridad:      Prioridad;
  destinatario:   Destinatario;
  rol_destino:    string | null;
  imagen_url:     string | null;
  imagen_nombre:  string | null;
  total_enviadas: number;
  total_leidas:   number;
  expires_at:     string | null;
  created_at:     string;
}

export type TipoComunicado = 'comunicado' | 'actividad';

export interface EnviarComunicadoPayload {
  titulo:        string;
  mensaje:       string;
  tipo?:         TipoComunicado;
  prioridad:     Prioridad;
  destinatario:  Destinatario;
  rol_destino?:  string;
  usuario_ids?:  number[];
  expires_at?:   string;
  imagen?:       File;
}

export interface NotificacionPreferencia {
  id:         number;
  usuario_id: number;
  tipo:       string;
  activa:     boolean;
}

export const TIPOS_NOTIFICACION: { tipo: string; label: string; grupo: string }[] = [
  { tipo: 'pago_vencido',           label: 'Pago vencido',              grupo: 'Pagos y cuotas' },
  { tipo: 'cuota_proxima',          label: 'Cuota próxima a vencer',    grupo: 'Pagos y cuotas' },
  { tipo: 'inscripcion_registrada', label: 'Inscripción registrada',    grupo: 'Académico' },
  { tipo: 'nota_publicada',         label: 'Nota publicada',            grupo: 'Académico' },
  { tipo: 'certificado_listo',      label: 'Certificado listo',         grupo: 'Académico' },
  { tipo: 'comunicado',             label: 'Comunicados',               grupo: 'Sistema' },
  { tipo: 'actividad',              label: 'Actividades',               grupo: 'Sistema' },
  { tipo: 'stock_bajo',             label: 'Stock bajo',                grupo: 'Sistema' },
  { tipo: 'aprobacion_pendiente',   label: 'Aprobación pendiente',      grupo: 'Sistema' },
];

export function prioridadConfig(p: Prioridad): { color: string; bg: string; label: string } {
  return {
    critica: { color: '#ef4444', bg: '#fef2f2', label: 'Crítica' },
    alta:    { color: '#f97316', bg: '#fff7ed', label: 'Alta' },
    media:   { color: '#3b82f6', bg: '#eff6ff', label: 'Media' },
    baja:    { color: '#9ca3af', bg: '#f9fafb', label: 'Baja' },
  }[p];
}

export function tipoBadgeClass(tipo: string): string {
  const mapa: Record<string, string> = {
    comunicado:             'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
    actividad:               'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-300',
    pago_vencido:            'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
    cuota_proxima:           'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
    pago_registrado:         'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
    pago_observado:          'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
    inscripcion_registrada:  'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-300',
    nota_publicada:          'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300',
    certificado_listo:       'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
    stock_bajo:              'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
    aprobacion_pendiente:    'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300',
  };
  return mapa[tipo] ?? 'bg-default-100 text-default-600 dark:bg-default-500/15 dark:text-default-300';
}

export function tipoLabel(tipo: string): string {
  return TIPOS_NOTIFICACION.find(t => t.tipo === tipo)?.label ?? tipo.replace(/_/g, ' ');
}

export function tiempoRelativo(fecha: string): string {
  const diff = Date.now() - new Date(fecha).getTime();
  const min  = Math.floor(diff / 60000);
  if (min < 1)   return 'ahora mismo';
  if (min < 60)  return `hace ${min} min`;
  const h = Math.floor(min / 60);
  if (h < 24)    return `hace ${h}h`;
  const d = Math.floor(h / 24);
  if (d < 7)     return `hace ${d}d`;
  return new Date(fecha).toLocaleDateString('es-BO');
}
