export interface MensajeContacto {
  id: number;
  secretaria_destino_id: number | null;
  nombre_remitente: string;
  email_remitente: string;
  telefono_remitente: string | null;
  asunto: string;
  mensaje: string;
  estado: string;
  respuesta: string | null;
  respondido_por: number | null;
  respondido_at: string | null;
  ip_origen: string | null;
  created_at: string | null;
}

export interface MensajeContactoListResponse {
  data: MensajeContacto[];
  total: number;
}

export interface MensajeContactoListParams {
  pageIndex?: number;
  pageSize?: number;
  query?: string;
  refresh?: number;
}

export interface ResponderMensajePayload {
  respuesta: string;
  estado?: string;
}
