export interface Carta {
  id_carta:     number;
  id_us_reg:    number;
  num_carta:    number;
  id_us:        number | null;
  id_plan:      number | null;
  nombresenor:  string | null;
  nombretitulo: string | null;
  textocarta1:  string | null;
  textocarta2:  string | null;
  textocarta3:  string | null;
  estado:       number;
  fecha_reg:    string | null;
}

export interface CartaListResponse { data: Carta[]; total: number; }
export interface CartaListParams   { pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateCartaPayload {
  id_carta:      number;
  num_carta:     number;
  id_us?:        number | null;
  id_plan?:      number | null;
  nombresenor?:  string | null;
  nombretitulo?: string | null;
  textocarta1?:  string | null;
  textocarta2?:  string | null;
  textocarta3?:  string | null;
  estado?:       number;
}
