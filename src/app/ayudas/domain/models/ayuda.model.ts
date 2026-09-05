export interface Ayuda {
  id_ayuda:              number;
  id_us_reg:             number;
  num_ayuda:             number;
  id_us:                 number;
  gestion:               string | null;
  monto_pagado:          string | null;
  nro_recibo:            string | null;
  fecha_recibo:          string | null;
  observacion_pago:      string | null;
  id_categoriatipoayuda: number | null;
  estado:                number;
  fecha_reg:             string | null;
}

export interface AyudaListResponse { data: Ayuda[]; total: number; }
export interface AyudaListParams   { pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateAyudaPayload {
  id_ayuda:               number;
  num_ayuda:              number;
  id_us:                  number;
  gestion?:               string | null;
  monto_pagado?:          string | null;
  nro_recibo?:            string | null;
  fecha_recibo?:          string | null;
  observacion_pago?:      string | null;
  id_categoriatipoayuda?: number | null;
  estado?:                number;
}
