export interface CorreoEnviado {
  id: number;
  tipo: string;
  destinatario: string;
  asunto: string;
  referencia_tipo: string | null;
  referencia_id: number | null;
  estado: string;
  error: string | null;
  enviado_por: number | null;
  created_at: string | null;
}

export interface CorreoEnviadoListResponse {
  data: CorreoEnviado[];
  total: number;
}
