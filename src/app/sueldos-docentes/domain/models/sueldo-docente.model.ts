export interface SueldoDocente {
  id:               number;
  id_us:            number;
  id_imp:           number | null;
  id_programa:      number | null;
  concepto:         string;
  periodo:          string | null;
  gestion:          number | null;
  monto_total:      number;
  observacion:      string | null;
  archivo_pdf:      string | null;
  estado:           number;
  created_at:       string | null;
  docente_nombre:   string | null;
  docente_ci:       string | null;
  docente_celular:  string | null;
  docente_foto:     string | null;
  nombre_curso:     string | null;
  total_pagado:     number;
  saldo_pendiente:  number;
  estado_pago:      'pagado' | 'parcial' | 'pendiente';
}

export interface PagoSueldo {
  id:                   number;
  id_sueldo:            number;
  monto_pagado:         number | string;
  fecha_pago:           string;
  nro_comprobante:      string | null;
  comprobante_archivo:  string | null;
  observacion:          string | null;
  estado:               number;
  created_at:           string | null;
}

export interface SueldoDocenteDetalle extends SueldoDocente {
  pagos: PagoSueldo[];
}

export interface SueldoDocenteListResponse {
  data:  SueldoDocente[];
  total: number;
}

export interface DocenteOption {
  id_us:           number;
  nombre_completo: string;
  ci:              string | null;
  fuente:          'legacy' | 'perfil';
}

export interface ImparticionOption {
  id_imp:      number;
  nombre_curso: string;
  periodo:     string | null;
  gestion:     number | null;
}

export interface SueldoDocenteParams {
  pageIndex?:   number;
  pageSize?:    number;
  query?:       string;
  periodo?:     string;
  gestion?:     number;
  estado_pago?: string;
}
