export interface CertConfigItem {
  id: number;
  config_id: number;
  plantilla_id: number | null;
  nombre_cert: string;
  precio: number;
  es_gratuito: boolean;
  orden: number;
  activo: boolean;
}

export interface CertConfigPrograma {
  id: number;
  programa_id: number;
  activo: boolean;
  titulo: string | null;
  descripcion: string | null;
  created_at: string | null;
  updated_at: string | null;
  items: CertConfigItem[];
  nombre_programa: string | null;
}

export interface CertSolicitud {
  id: number;
  config_item_id: number;
  inscripcion_id: number | null;
  usuario_ci: string;
  usuario_nombre: string;
  usuario_email: string | null;
  es_gratuito: boolean;
  estado: 'generado' | 'pendiente_pago' | 'pendiente_revision' | 'rechazado' | 'no_participa';
  comprobante_url: string | null;
  monto_pagado: number | null;
  nota_admin: string | null;
  certificado_id: number | null;
  certificado_url: string | null;
  created_at: string | null;
  nombre_cert: string | null;
  nombre_programa: string | null;
}

export interface CertSolicitudListResponse {
  data: CertSolicitud[];
  total: number;
}

export interface UpsertCertConfigPayload {
  programa_id: number;
  activo: boolean;
  titulo?: string | null;
  descripcion?: string | null;
}

export interface CertConfigItemPayload {
  plantilla_id?: number | null;
  nombre_cert: string;
  precio: number;
  es_gratuito: boolean;
  orden: number;
  activo?: boolean;
}
