export interface EventoFoto {
  id:           number;
  evento_id:    number;
  imagen_url:   string;
  descripcion?: string;
  orden:        number;
}
export interface EventoFotoListResponse {
  data:  EventoFoto[];
  total: number;
}
export interface EventoFotoListParams {
  query?:     string;
  evento_id?: number;
  pageIndex?: number;
  pageSize?:  number;
}
