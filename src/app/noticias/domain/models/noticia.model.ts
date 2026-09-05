export interface Noticia {
  id:                  number;
  titulo:              string;
  slug:                string;
  entradilla?:         string;
  contenido?:          string;
  estado:              string;
  destacada:           boolean;
  fecha_publicacion?:  string;
  categoria_id?:       number;
}
export interface NoticiaListResponse {
  data:  Noticia[];
  total: number;
}
export interface NoticiaListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
