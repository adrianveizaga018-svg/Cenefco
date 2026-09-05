export interface RevistaCientifica {
  id_revistacientifica:          number;
  id_us_reg:                     number;
  num_revistacientifica:         number;
  titulo_revistacientifica:      string;
  descripcion_revistacientifica: string | null;
  fecha_publicacion:             string | null;
  archivo:                       string | null;
  estado:                        number;
  fecha_reg:                     string | null;
}

export interface RevistaCientificaListResponse { data: RevistaCientifica[]; total: number; }
export interface RevistaCientificaListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateRevistaCientificaPayload {
  id_revistacientifica:           number;
  num_revistacientifica:          number;
  titulo_revistacientifica:       string;
  descripcion_revistacientifica?: string | null;
  fecha_publicacion?:             string | null;
  archivo?:                       string | null;
  estado?:                        number;
}
