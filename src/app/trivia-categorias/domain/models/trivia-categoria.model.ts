export interface TriviaCategoria {
  id:           number;
  nombre:       string;
  slug:         string;
  descripcion?: string | null;
  imagen_url?:  string | null;
  color?:       string | null;
  curso_id?:    number | null;
  orden:        number;
  activo:       boolean;
  created_at?:  string | null;
}
export interface TriviaCategoriaListResponse {
  data:  TriviaCategoria[];
  total: number;
}
export interface TriviaCategoriaListParams {
  query?:       string;
  pageIndex?:   number;
  pageSize?:    number;
  refresh?:     number;
  soloActivos?: boolean;
}
