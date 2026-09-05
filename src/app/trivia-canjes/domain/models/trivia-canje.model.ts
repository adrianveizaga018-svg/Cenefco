export interface TriviaCanje {
  id:                number;
  codigo:            string;
  estado:            'pendiente' | 'entregado' | 'cancelado';
  costo_puntos:      number;
  nota?:             string | null;
  fecha_resolucion?: string | null;
  created_at:        string;
  usuario_id:        number;
  usuario_nombre:    string;
  usuario_email?:    string | null;
  premio_id:         number;
  premio_nombre:     string;
  premio_tipo:       string;
}
export interface TriviaCanjeListResponse {
  data:  TriviaCanje[];
  total: number;
}
export interface TriviaCanjeListParams {
  query?:     string;
  estado?:    string;
  pageIndex?: number;
  pageSize?:  number;
  refresh?:   number;
}
