export interface ReporteCuota {
  id_fechapago: number;
  cuota_nro: string | null;
  cuota_monto: number;
  cuota_fecha_inicio: string | null;
  cuota_fecha_fin: string | null;
  tipo_tramite: string | null;
  monto_pagado: number;
  nro_pagos: number;
  ultima_fecha_pago: string | null;
  estado_verificacion: string | null;
  estado_cuota: 'pagada' | 'parcial' | 'vencida' | 'pendiente';
  dias_atraso: number;
}

export interface ReportePago {
  id_pago: number;
  fecha_deposito: string | null;
  monto_pagado: number;
  metodo_pago: string | null;
  nro_boleta_bancaria: string | null;
  estado_verificacion: string | null;
  es_anticipo: boolean;
  cuota_nro: string | null;
}

export type EstadoParticipante = 'al_dia' | 'en_mora' | 'completo' | 'sin_pagos' | 'sin_plan';

export interface ReporteParticipante {
  id_ins: number;
  id_us: number;
  id_imp: number;
  estudiante_nombre: string;
  estudiante_ci: string | null;
  estudiante_email: string | null;
  estudiante_celular: string | null;
  curso_nombre: string | null;
  periodo: string | null;
  gestion: string | null;
  total_cuotas: number;
  cuotas_pagadas: number;
  cuotas_vencidas: number;
  total_plan: number;
  total_pagado: number;
  total_anticipos: number;
  pendiente: number | null;
  plan_no_asignado: boolean;
  estado_general: EstadoParticipante;
  cuotas: ReporteCuota[];
  pagos: ReportePago[];
}

export interface ReporteCobrosTotales {
  total_plan: number;
  total_pagado: number;
  pendiente: number;
  al_dia: number;
  en_mora: number;
  completo: number;
  sin_pagos: number;
  sin_plan: number;
  total_participantes: number;
}

export interface ReporteCobrosResponse {
  participantes: ReporteParticipante[];
  totales: ReporteCobrosTotales;
}

export interface ReporteCobrosParams {
  id_imp?: number;
  periodo?: 'dia' | 'mes' | 'anio' | 'rango';
  fecha?: string;
  fecha_inicio?: string;
  fecha_fin?: string;
  con_inactivos?: boolean;
}

export interface CursoOpcion {
  id_imp: number;
  nombre: string;
  periodo: string;
  gestion: string;
  paralelo: string;
  docente: string;
  total_inscritos: number;
}
