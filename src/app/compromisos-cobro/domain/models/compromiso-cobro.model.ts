export type EstadoCompromisoCobro = 'pendiente' | 'cumplido' | 'incumplido' | 'cancelado';
export type MotivoReprogramacion = 'pidio_mas_tiempo' | 'no_respondio' | 'promete_pagar_pronto' | 'otro';

export interface CompromisoCobro {
  id:                     number;
  id_ins:                 number;
  id_us:                  number;
  id_imp:                 number;
  fecha_compromiso:       string;
  hora_compromiso:        string | null;
  monto_comprometido:     number | null;
  observacion:            string | null;
  estado:                 EstadoCompromisoCobro;
  veces_reprogramado:     number;
  registrado_por:         number;
  notificado_at:          string | null;
  vencido_notificado_at:  string | null;
  created_at:             string | null;
  updated_at:             string | null;
  estudiante_nombre:      string | null;
  estudiante_ci:          string | null;
  curso_nombre:           string | null;
  registrado_por_nombre:  string | null;
}

export interface CompromisoCobroListResponse { data: CompromisoCobro[]; total: number; }

export type TipoEventoCompromisoCobro = 'creado' | 'reprogramado' | 'cumplido' | 'incumplido' | 'cancelado';

export interface CompromisoCobroLog {
  id:                     number;
  compromiso_cobro_id:    number;
  tipo_evento:            TipoEventoCompromisoCobro;
  fecha_anterior:         string | null;
  fecha_nueva:            string | null;
  motivo:                 MotivoReprogramacion | null;
  observacion:            string | null;
  registrado_por:         number;
  registrado_por_nombre:  string | null;
  created_at:             string | null;
}

export interface CompromisoCobroListParams {
  pageIndex?: number;
  pageSize?:  number;
  query?:     string;
  estado?:    EstadoCompromisoCobro;
}

export interface ResumenCompromisosCobro {
  vencidos: number;
  hoy:      number;
}

export interface CreateCompromisoCobroPayload {
  id_ins:              number;
  fecha_compromiso:    string;
  hora_compromiso?:    string | null;
  monto_comprometido?: number | null;
  observacion?:        string | null;
}

export interface ReprogramarCompromisoCobroPayload {
  nueva_fecha: string;
  nueva_hora?: string | null;
  motivo:      MotivoReprogramacion;
  observacion?: string | null;
}

export interface ObservacionCompromisoCobroPayload {
  observacion?: string | null;
}

export const MOTIVOS_REPROGRAMACION: { value: MotivoReprogramacion; label: string }[] = [
  { value: 'pidio_mas_tiempo',      label: 'Pidió más tiempo' },
  { value: 'no_respondio',          label: 'No respondió' },
  { value: 'promete_pagar_pronto',  label: 'Promete pagar pronto' },
  { value: 'otro',                  label: 'Otro' },
];

export const ESTADO_COMPROMISO_LABELS: Record<EstadoCompromisoCobro, string> = {
  pendiente:  'Pendiente',
  cumplido:   'Cumplido',
  incumplido: 'Incumplido',
  cancelado:  'Cancelado',
};

export const ESTADO_COMPROMISO_CLASES: Record<EstadoCompromisoCobro, string> = {
  pendiente:  'bg-amber-100 text-amber-700',
  cumplido:   'bg-success/10 text-success',
  incumplido: 'bg-danger/10 text-danger',
  cancelado:  'bg-default-100 text-default-500',
};
