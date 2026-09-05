export type TipoEvento =
  | 'inscripciones' | 'inicio_clases' | 'finalizacion'
  | 'evaluacion' | 'graduacion' | 'feriado' | 'otro';

export const TIPOS_EVENTO: { value: TipoEvento; label: string; color: string }[] = [
  { value: 'inscripciones', label: 'Inscripciones',          color: '#3b82f6' },
  { value: 'inicio_clases', label: 'Inicio de Clases',       color: '#10b981' },
  { value: 'finalizacion',  label: 'Finalización / Cierre',  color: '#f59e0b' },
  { value: 'evaluacion',    label: 'Evaluación / Examen',    color: '#8b5cf6' },
  { value: 'graduacion',    label: 'Graduación / Ceremonia', color: '#ec4899' },
  { value: 'feriado',       label: 'Feriado',                color: '#ef4444' },
  { value: 'otro',          label: 'Otro',                   color: '#6b7280' },
];

export interface CalendarioAcademico {
  id:              number;
  titulo:          string;
  descripcion:     string | null;
  tipo:            TipoEvento | null;
  color:           string | null;
  programa_id:     number | null;
  fecha_inicio:    string;
  fecha_fin:       string | null;
  todo_el_dia:     boolean;
  destacado:       boolean;
  publico:         boolean;
  created_at:      string | null;
  updated_at:      string | null;
  nombre_programa: string | null;
  vendedor_id:     number | null;
  vendedor_nombre: string | null;
  pagina:          string | null;
  duracion_dias:   number | null;
  costo_inflado:   number | null;
  descuento:       number | null;
  precio_vip:      number | null;
  observaciones:   string | null;
}

export interface CalendarioAcademicoListResponse { data: CalendarioAcademico[]; total: number; }
export interface CalendarioAcademicoListParams   { query?: string; tipo?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
