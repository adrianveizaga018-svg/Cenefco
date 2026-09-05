export interface CategoriaNoticia {
  id:     number;
  nombre: string;
  slug:   string;
}
export interface CategoriaNoticiaListResponse {
  data:  CategoriaNoticia[];
  total: number;
}
export interface CategoriaNoticiaListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
