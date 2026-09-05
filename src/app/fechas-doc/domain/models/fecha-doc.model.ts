export interface FechaDoc {
  id_fechadoc:  number;
  id_plandoc:   number;
  id_us_reg:    number;
  num_fechadoc: number;
  nro_doc:      string | null;
  tipo_documento: string | null;
  fecha_inicio: string | null;
  fecha_fin:    string | null;
  obligatorio:  number;
  estado:       number;
  fecha_reg:    string | null;
}
export interface FechaDocListResponse { data: FechaDoc[]; total: number; }
export interface FechaDocListParams   { id_plandoc?: number; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateFechaDocPayload {
  id_fechadoc:     number;
  id_plandoc:      number;
  nro_doc?:        string | null;
  tipo_documento?: string | null;
  fecha_inicio?:   string | null;
  fecha_fin?:      string | null;
  obligatorio?:    number;
  estado?:         number;
}
