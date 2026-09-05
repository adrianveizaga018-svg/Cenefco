export interface SpeechVentas {
  id:             number;
  titulo:         string;
  categoria:      string | null;
  contenido:      string;
  palabras_clave: string | null;
  activo:         boolean;
  orden:          number;
  created_at:     string;
  updated_at:     string;
}

export interface SpeechVentasListResponse {
  data:  SpeechVentas[];
  total: number;
}

export interface SpeechVentasParams {
  pageIndex?:  number;
  pageSize?:   number;
  query?:      string;
  categoria?:  string;
  activo?:     string;
}
