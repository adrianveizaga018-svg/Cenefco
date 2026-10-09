export interface EstudianteResumen {
  id_us:     number;
  nombre:    string | null;
  appaterno: string | null;
  apmaterno: string | null;
  ci:        string | null;
  celular:   string | null;
  email:     string | null;
}

export interface CuentaPlataforma {
  id:       number;
  email:    string;
  activo:   boolean;
  /** Ya creó su contraseña con el enlace del correo o entró con Google */
  activada: boolean;
  rol:      string | null;
  creada:   string | null;
}

export interface InscripcionFicha {
  id_ins:           number;
  id_imp:           number;
  id_programa:      number | null;
  programa:         string;
  version:          string | null;
  gestion:          number | string | null;
  fecha_ins:        string | null;
  activa:           boolean;
  canal:            string | null;
  total_pagado:     number;
  /** null = sin plan asignado (no equivale a pagado) */
  pendiente:        number | null;
  plan_no_asignado: boolean;
  pago_completo:    boolean;
}

export interface CertificadoFicha {
  id:                  number;
  imparte_id:          number | null;
  codigo_verificacion: string;
  archivo_url:         string | null;
  estado:              string;
  condicion:           string | null;
  created_at:          string;
  programa:            string | null;
}

export interface FichaEstudiante {
  estudiante:    EstudianteResumen & { expedido: number | null; genero: number | null; fecha_reg: string | null };
  cuenta:        CuentaPlataforma | null;
  inscripciones: InscripcionFicha[];
  certificados:  CertificadoFicha[];
}

export interface OpcionesCertificado {
  puede_emitir:     boolean;
  /** Por qué no se puede emitir (deuda, sin plantillas asignadas, ya emitido…) */
  motivo:           string | null;
  pendiente:        number | null;
  plan_no_asignado: boolean;
  plantillas:       { id: number; nombre: string }[];
  emitidos:         { id: number; codigo_verificacion: string; archivo_url: string | null; created_at: string }[];
}
