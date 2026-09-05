export interface CartaGenerada {
  id_cartagen:                  number;
  id_us_reg:                    number;
  num_carta:                    number;
  id_us:                        number | null;
  id_cartamod:                  number | null;
  textocarta:                   string | null;
  textocarta1:                  string | null;
  textocarta3:                  string | null;
  usar_encabezado_pie_estandar: number;
  cp_nro_contrato:              number | null;
  cp_gestion_contrato:          string | null;
  estado:                       number;
  fecha_reg:                    string | null;
}

export interface CartaGeneradaListResponse { data: CartaGenerada[]; total: number; }
export interface CartaGeneradaListParams   { pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateCartaGeneradaPayload {
  id_cartagen:                   number;
  num_carta:                     number;
  id_us?:                        number | null;
  id_cartamod?:                  number | null;
  textocarta?:                   string | null;
  textocarta1?:                  string | null;
  textocarta3?:                  string | null;
  usar_encabezado_pie_estandar?: number;
  cp_nro_contrato?:              number | null;
  cp_gestion_contrato?:          string | null;
  estado?:                       number;
}
