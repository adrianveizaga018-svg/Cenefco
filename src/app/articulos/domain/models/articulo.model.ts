export interface EtiquetaSimple {
  id:     number;
  nombre: string;
  slug:   string;
  color:  string | null;
}

export interface Articulo {
  id_art:               number;
  titulo:               string;
  slug:                 string | null;
  entradilla:           string | null;
  contenido:            string | null;
  imagen_principal_url: string | null;
  imagen_alt:           string | null;
  destacada:            boolean;
  fecha_publicacion:    string | null;
  estado_web:           'borrador' | 'publicado' | 'archivado';
  estado:               number;
  vistas:               number;
  meta_titulo:          string | null;
  meta_descripcion:     string | null;
  etiquetas:            EtiquetaSimple[];
  fecha_reg:            string | null;
}

export interface ArticuloListResponse { data: Articulo[]; total: number; }
export interface ArticuloListParams   { query?: string; estado_web?: string; pageIndex?: number; pageSize?: number; refresh?: number; }

export interface CreateArticuloPayload {
  titulo:               string;
  slug?:                string | null;
  entradilla?:          string | null;
  contenido?:           string | null;
  imagen_principal_url?: string | null;
  imagen_alt?:          string | null;
  destacada?:           boolean;
  fecha_publicacion?:   string | null;
  estado_web?:          string;
  meta_titulo?:         string | null;
  meta_descripcion?:    string | null;
  estado?:              number;
  etiquetas?:           number[];
}
