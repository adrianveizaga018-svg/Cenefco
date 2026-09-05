export interface Vendedor {
  id:           number;
  nombre:       string;
  apellido:     string;
  ci:           string | null;
  telefono:     string | null;
  email:        string | null;
  foto:         string | null;
  pagina:       string | null;
  meta_ventas:  number | null;
  activo:       boolean;
  usuario_id:   number | null;
  usuario_nombre: string | null;
  created_at:   string | null;
  updated_at:   string | null;
  comision_estimada: number | null;
}

export interface VendedorListResponse {
  data:  Vendedor[];
  total: number;
}

export interface VendedorListParams {
  query?:     string;
  activo?:    string;
  pageIndex?: number;
  pageSize?:  number;
  refresh?:   number;
}

export interface VendedorComisionCurso {
  id_programa:        number;
  nombre_programa:    string;
  total_inscritos:    number;
  categoria_nombre:   string | null;
  comision_monto:     number;
  comision_estimada:  number;
}

export interface VendedorComisionDetalle {
  vendedor_id:          number;
  vendedor_nombre:      string;
  total_comision:       number;
  cursos:               VendedorComisionCurso[];
}

export interface CreateVendedorPayload {
  nombre:      string;
  apellido:    string;
  ci?:         string | null;
  telefono?:   string | null;
  email?:      string | null;
  foto?:       string | null;
  pagina?:     string | null;
  meta_ventas?: number | null;
  activo?:     boolean;
  usuario_id?: number | null;
}
