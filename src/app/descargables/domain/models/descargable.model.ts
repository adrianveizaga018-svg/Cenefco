export interface Descargable {
  id:                 number;
  nombre:             string;
  tipo:               string | null;
  archivo_url:        string;
  imagen_portada_url: string | null;
  programa_id:        number | null;
  requiere_datos:     boolean;
  descargas:          number;
  orden:              number;
  activo:             boolean;
  created_at:         string | null;
}

export interface DescargableListResponse { data: Descargable[]; total: number; }
export interface DescargableListParams { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateDescargablePayload {
  nombre: string; tipo?: string | null; archivo_url: string;
  imagen_portada_url?: string | null; programa_id?: number | null;
  requiere_datos?: boolean; orden?: number; activo?: boolean;
}
