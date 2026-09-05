export type EstadoInscripcionDiplomado = 'pendiente' | 'revisado' | 'aceptado' | 'rechazado' | 'contactado' | 'inscrito';

export interface InscripcionDiplomado {
  id:                        number;
  programa_id:               number | null;
  nombre_programa:           string | null;
  nombre:                    string;
  apellido_paterno:          string | null;
  apellido_materno:          string | null;
  nombre_completo:           string;
  fecha_nacimiento:          string | null;
  email:                     string;
  ci:                        string | null;
  expedido_id:               number | null;
  expedido_nombre:           string | null;
  telefono_grupo_inscritos:  string | null;
  archivo_ci:                string | null;
  archivo_titulo:            string | null;
  archivo_cv:                string | null;
  archivo_foto_3x3:          string | null;
  ciudad_residencia_id:      number | null;
  ciudad_residencia_nombre:  string | null;
  provincia_especificar:     string | null;
  medio_pago:                string | null;
  medio_pago_id:             number | null;
  medio_pago_nombre:         string | null;
  monto_pagado:              number | null;
  archivo_comprobante_pago:  string | null;
  sugerencia_curso:          string | null;
  recomendar_docente:        boolean;
  detalle_docente:           string | null;
  estado:                    EstadoInscripcionDiplomado;
  notificado:                boolean;
  origen:                    string | null;
  ip_origen:                 string | null;
  created_at:                string | null;
  updated_at:                string | null;
}

export interface InscripcionDiplomadoListResponse {
  data:  InscripcionDiplomado[];
  total: number;
}

export interface InscripcionDiplomadoListParams {
  query?:       string;
  pageIndex?:   number;
  pageSize?:    number;
  estado?:      string;
  programa_id?: number;
  refresh?:     number;
}

export interface UpdateInscripcionDiplomadoPayload {
  estado?:                    EstadoInscripcionDiplomado;
  notificado?:                boolean;
  nombre?:                    string;
  apellido_paterno?:          string | null;
  apellido_materno?:          string | null;
  fecha_nacimiento?:          string | null;
  email?:                     string;
  ci?:                        string | null;
  expedido_id?:               number | null;
  telefono_grupo_inscritos?:  string | null;
  archivo_ci?:                string | null;
  archivo_titulo?:            string | null;
  archivo_cv?:                string | null;
  archivo_foto_3x3?:          string | null;
  ciudad_residencia_id?:      number | null;
  provincia_especificar?:     string | null;
  medio_pago?:                string | null;
  medio_pago_id?:             number | null;
  monto_pagado?:              number | null;
  archivo_comprobante_pago?:  string | null;
  sugerencia_curso?:          string | null;
  recomendar_docente?:        boolean;
  detalle_docente?:           string | null;
  programa_id?:               number | null;
}

export interface ConvertirInscripcionDiplomadoPayload {
  id_imp?:  number;
  id_plan?: number;
}

export interface ConvertirInscripcionDiplomadoResult {
  mensaje:        string;
  inscripcion_id: number;
  usuario_id:     number;
  usuario_nuevo:  boolean;
  usuario:        { id_us: number; nombre: string; appaterno: string | null; ci: string | null; email: string | null } | null;
  imparticion:    { id_imp: number; periodo: string | null; gestion: string | null } | null;
}
