export interface CertVerificacion {
  id: number;
  certificado_id: number | null;
  codigo_consultado: string;
  resultado: string;
  ip_origen: string | null;
  user_agent: string | null;
  pais: string | null;
  created_at: string | null;
}

export interface CertVerificacionListResponse {
  data: CertVerificacion[];
  total: number;
}
