export interface GaleriaCategoria {
  id:          number;
  nombre:      string;
  slug:        string | null;
  descripcion: string | null;
  orden:       number;
  activo:      boolean;
  created_at:  string | null;
}

export interface GaleriaCategoriaListResponse { data: GaleriaCategoria[]; total: number; }
export interface GaleriaCategoriaListParams { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateGaleriaCategoriaPayload {
  nombre: string; slug?: string | null; descripcion?: string | null;
  orden?: number; activo?: boolean;
}
