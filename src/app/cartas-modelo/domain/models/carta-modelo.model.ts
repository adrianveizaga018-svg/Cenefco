export interface CartaModelo {
  id_cartamod:                  number;
  id_us_reg:                    number;
  num_cartamod:                 number;
  nombremodelo:                 string;
  textocarta:                   string | null;
  textocarta1:                  string | null;
  textocarta3:                  string | null;
  texto_carta:                  string | null;
  usar_encabezado_pie_estandar: number;
  estado:                       number;
  fecha_reg:                    string | null;
}

export interface CartaModeloListResponse { data: CartaModelo[]; total: number; }
export interface CartaModeloListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateCartaModeloPayload {
  id_cartamod:                   number;
  num_cartamod:                  number;
  nombremodelo:                  string;
  textocarta?:                   string | null;
  textocarta1?:                  string | null;
  textocarta3?:                  string | null;
  texto_carta?:                  string | null;
  usar_encabezado_pie_estandar?: number;
  estado?:                       number;
}
