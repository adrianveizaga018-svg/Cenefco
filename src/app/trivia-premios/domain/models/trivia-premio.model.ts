export interface TriviaPremio {
  id:            number;
  nombre:        string;
  descripcion?:  string | null;
  tipo:          'souvenir' | 'descuento' | 'otro';
  imagen_url?:   string | null;
  costo_puntos:  number;
  stock:         number | null;
  activo:        boolean;
  orden:         number;
}
export interface TriviaPremioListResponse {
  data:  TriviaPremio[];
  total: number;
}
export interface TriviaPremioListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
  refresh?:   number;
}
