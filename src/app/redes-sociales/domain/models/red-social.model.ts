export interface RedSocial {
  id: number;
  red: string;
  nombre_display: string | null;
  url: string;
  icono_clase: string | null;
  pixel_id: string | null;
  mostrar_footer: boolean;
  mostrar_header: boolean;
  orden: number;
  activo: boolean;
}

export interface RedSocialListResponse {
  data: RedSocial[];
  total: number;
}

export interface RedSocialListParams {
  query?: string;
  pageIndex?: number;
  pageSize?: number;
}
