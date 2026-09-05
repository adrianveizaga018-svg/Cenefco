export interface Faq {
  id:           number;
  pregunta:     string;
  respuesta:    string;
  categoria:    string | null;
  programa_id:  number | null;
  orden:        number;
  activo:       boolean;
  created_at:   string | null;
}

export interface FaqListResponse { data: Faq[]; total: number; }
export interface FaqListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
