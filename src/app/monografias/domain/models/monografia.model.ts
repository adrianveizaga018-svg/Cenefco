export interface Monografia {
  id_monografia:          number;
  id_us_reg:              number;
  num_monografia:         number;
  titulo_monografia:      string;
  descripcion_monografia: string | null;
  fecha_publicacion:      string | null;
  autor:                  string | null;
  archivo:                string | null;
  estado:                 number;
  fecha_reg:              string | null;
}

export interface MonografiaListResponse { data: Monografia[]; total: number; }
export interface MonografiaListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateMonografiaPayload {
  id_monografia:           number;
  num_monografia:          number;
  titulo_monografia:       string;
  descripcion_monografia?: string | null;
  fecha_publicacion?:      string | null;
  autor?:                  string | null;
  archivo?:                string | null;
  estado?:                 number;
}
