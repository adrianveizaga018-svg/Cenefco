import { CompromisoCobro } from '../../../compromisos-cobro/domain/models/compromiso-cobro.model';

export interface Inscripcion {
  id_ins:           number;
  id_us_reg:        number;
  fecha_ins:        string | null;
  id_us:            number;
  id_imp:           number;
  observacion_ins:  string | null;
  observacion:      string | null;
  periodo:          string | null;
  gestion:          string | null;
  estado:           number;
  fecha_reg:        string | null;
  estudiante_nombre: string | null;
  estudiante_ci:     string | null;
  estudiante_email:  string | null;
  estudiante_celular:string | null;
  materia_nombre:    string | null;
  materia_sigla:     string | null;
  programa_slug:     string | null;
  paralelo:          string | null;
  docente_nombre:    string | null;
  cuotas_pagadas:    number;
  total_pagado:      number;
  id_vendedor:       number | null;
  canal_venta:       string | null;
  imp_periodo:       string | null;
  documentos?:       Record<string, string> | null;
  campos_extra?:     Record<string, string> | null;
  campos_extra_resueltos?: CampoExtraResuelto[];
  curso_costo_monto: number | null;
}

export interface CampoExtraResuelto {
  nombre_campo: string;
  etiqueta:     string;
  tipo:         string;
  valor:        string;
}

export type ClaveRaiz = 'ci' | 'nombre' | 'apellido_paterno' | 'apellido_materno' | 'email' | 'telefono';

export interface CampoFormularioDefinicion {
  nombre_campo: string;
  etiqueta:     string;
  tipo:         string;
  requerido?:   boolean;
  opciones?:    string[];
  placeholder?: string;
  ayuda?:       string;
  clave_raiz?:  ClaveRaiz | null;
}

export interface InscripcionDetalle {
  compromiso_cobro: CompromisoCobro | null;
  inscripcion: Inscripcion & {
    est_appaterno:   string | null;
    est_apmaterno:   string | null;
    est_ci:          string | null;
    est_email:       string | null;
    est_celular:     string | null;
    est_ciudad:      string | null;
    est_titulo:      string | null;
    materia_horas:   string | null;
    materia_semestre:string | null;
    cupo:            string | null;
    nro_resolucion_hcu: string | null;
    docente_titulo:  string | null;
    docente_email:   string | null;
    curso_costo_monto: number | null;
  };
  pagos: PagoItem[];
  plan: PlanItem | null;
  todas_cuotas: CuotaItem[];
  documentos: DocumentoItem[];
  devoluciones: DevolucionItem[];
  docentes: DocenteItem[];
  documentos_estudiante: DocumentosEstudianteGrupo[];
  formulario_campos: CampoFormularioDefinicion[];
  resumen: {
    plan_no_asignado: boolean;
    total_pagado:     number;
    cuotas_pagadas:   number;
    cuotas_totales:   number;
    total_plan:       number;
    pendiente:        number | null;
    total_anticipos:  number;
  };
}

export interface DocumentosEstudianteGrupo {
  origen:    'inscripcion' | 'diplomado';
  origen_id: number;
  programa:  string | null;
  fecha:     string | null;
  archivos:  Record<string, string>;
}

export interface DocenteItem {
  id:               number;
  nombre_completo:  string;
  titulo_academico: string | null;
  especialidad:     string | null;
  foto_url:         string | null;
  email_publico:    string | null;
}

export interface PagoItem {
  id_pago:            number;
  pago_extra:         number;
  id_fechapago:       number | null;
  monto_pagado:       number;
  fecha_deposito:     string | null;
  nro_boleta_bancaria:string | null;
  observacion_pago:   string | null;
  pago_estado:        number;
  metodo_pago:        string | null;
  id_us_cajero:       number | null;
  cajero_nombre:      string | null;
  comprobante_archivo:string | null;
  estado_verificacion:string;
  nota_verificacion:  string | null;
  monto_descuento:    number | null;
  motivo_descuento:   string | null;
  tipo_banco_id:      number | null;
  tipo_banco_nombre:  string | null;
  cuota_nro:          string | null;
  cuota_monto:        number | null;
  tipo_tramite:       string | null;
  cuota_fecha_inicio: string | null;
  cuota_fecha_fin:    string | null;
  plan_titulo:        string | null;
  plan_convenio:      string | null;
  plan_nro_cuotas:    number | null;
  plan_costo:         number | null;
}

export interface DevolucionItem {
  id:              number;
  id_ins:          number;
  id_us:           number;
  monto:           number;
  motivo:          string;
  documento_url:   string | null;
  estado:          string;
  nota_respuesta:  string | null;
  fecha_respuesta: string | null;
  created_at:      string;
}

export interface PlanItem {
  id_plan:     number;
  titulo:      string;
  convenio:    string | null;
  nro_cuotas:  number;
  costo:       number | null;
  anio:        string | null;
}

export interface CuotaItem {
  id_fechapago:  number;
  nro_pago:      string | null;
  monto_a_pagar: number | null;
  tipo_tramite:  string | null;
  fecha_inicio:  string | null;
  fecha_fin:     string | null;
  obligatorio:   number;
}

export interface DocumentoItem {
  id_documento:          number;
  tipo_documento:        string | null;
  nro_doc:               string | null;
  doc_obligatorio:       number;
  documento_digital:     string | null;
  dejo_documento_fisico: number;
  observacion_doc:       string | null;
  estado:                number;
}

export interface InscripcionListResponse { data: Inscripcion[]; total: number; }
export interface InscripcionListParams   {
  query?: string; pageIndex?: number; pageSize?: number;
  refresh?: number; id_us?: number; id_imp?: number; programa_id?: number;
  periodo?: string; gestion?: string; conInactivos?: boolean;
}
export interface CreateInscripcionPayload {
  id_ins:           number;
  id_us:            number;
  id_imp:           number;
  id_plan?:         number | null;
  fecha_ins?:       string | null;
  observacion_ins?: string | null;
  periodo?:         string | null;
  gestion?:         string | null;
  estado?:          number;
  id_vendedor?:     number | null;
  canal_venta?:     'admin' | 'portal' | 'whatsapp' | 'referido';
  observacion?:     string | null;
  documentos?:      Record<string, string> | null;
  campos_extra?:    Record<string, string> | null;
}

export type CanalVenta = 'admin' | 'portal' | 'whatsapp' | 'referido';
export type MetodoPago = 'efectivo' | 'deposito_bancario' | 'pago_online' | 'qr';

export interface DocRequerido {
  id_fechadoc:     number;
  nro_doc:         string | null;
  tipo_documento:  string | null;
  obligatorio:     number;
  fecha_inicio:    string | null;
  fecha_fin:       string | null;
  presentado:      boolean;
  id_documento:    number | null;
  documento_digital: string | null;
  dejo_fisico:     number;
  fecha_entrega:   string | null;
  observacion:     string | null;
}

export interface DocumentosInscripcion {
  plan_id:    number | null;
  requeridos: DocRequerido[];
}
