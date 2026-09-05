export interface ReglamentoPrograma {
  id_programa:                number;
  personalizado:              boolean;
  bienvenida:                 string | null;
  bienvenida_personalizado:   boolean;
  reglas_asistencia:          string | null;
  reglas_asistencia_personalizado: boolean;
  reglas_evaluacion:          string | null;
  reglas_evaluacion_personalizado: boolean;
  reglas_pagos:               string | null;
  reglas_pagos_personalizado: boolean;
  reglas_conducta:            string | null;
  reglas_conducta_personalizado: boolean;
  reglas_plataformas:         string | null;
  reglas_plataformas_personalizado: boolean;
  reglas_derechos:            string | null;
  reglas_derechos_personalizado: boolean;
}

export type ReglamentoCampo =
  | 'bienvenida'
  | 'reglas_asistencia'
  | 'reglas_evaluacion'
  | 'reglas_pagos'
  | 'reglas_conducta'
  | 'reglas_plataformas'
  | 'reglas_derechos';
