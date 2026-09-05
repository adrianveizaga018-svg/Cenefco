export interface Testimonio {
  id:           number;
  nombre:       string;
  slug:         string | null;
  cargo:        string | null;
  empresa:      string | null;
  testimonio:   string;
  calificacion: number;
  foto_url:     string | null;
  foto_alt:     string | null;
  programa_id:  number | null;
  destacado:    boolean;
  orden:        number;
  estado:       string;
  created_at:   string | null;
}

export interface TestimonioListResponse { data: Testimonio[]; total: number; }
export interface TestimonioListParams { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateTestimonioPayload {
  nombre: string; cargo?: string | null; empresa?: string | null;
  testimonio: string; calificacion?: number; foto_url?: string | null;
  foto_alt?: string | null; programa_id?: number | null;
  destacado?: boolean; orden?: number; estado?: string;
}
