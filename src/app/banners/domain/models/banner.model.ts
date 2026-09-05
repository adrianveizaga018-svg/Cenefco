export interface Banner {
  id: number;
  titulo: string | null;
  slug: string | null;
  descripcion: string | null;
  imagen_url: string;
  enlace_url: string | null;
  enlace_texto: string | null;
  enlace_url_2: string | null;
  enlace_texto_2: string | null;
  fecha_inicio: string | null;
  fecha_fin: string | null;
  activo: boolean;
  orden: number;
}

export interface BannerListResponse {
  data: Banner[];
  total: number;
}

export interface BannerListParams {
  pageIndex?: number;
  pageSize?: number;
  query?: string;
  refresh?: number;
}

export interface CreateBannerPayload {
  titulo?: string | null;
  descripcion?: string | null;
  imagen_url: string;
  enlace_url?: string | null;
  enlace_texto?: string | null;
  enlace_url_2?: string | null;
  enlace_texto_2?: string | null;
  fecha_inicio?: string | null;
  fecha_fin?: string | null;
  activo?: boolean;
  orden?: number;
}
