export interface Boletin {
  id_boletin:          number;
  id_us_reg:           number;
  num_boletin:         number;
  titulo_boletin:      string;
  titulo_pagina:       string | null;
  descripcion_boletin: string | null;
  estado:              number;
  fecha_reg:           string | null;
  imagen_url:          string | null;
}

export interface BoletinListResponse { data: Boletin[]; total: number; }
export interface BoletinListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateBoletinPayload {
  id_boletin:           number;
  num_boletin:          number;
  titulo_boletin:       string;
  titulo_pagina?:       string | null;
  descripcion_boletin?: string | null;
  estado?:              number;
  imagen_url?:          string | null;
}
