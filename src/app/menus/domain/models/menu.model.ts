export interface Menu {
  id: number;
  nombre: string;
  descripcion: string | null;
  activo: boolean;
}

export interface MenuListResponse {
  data: Menu[];
  total: number;
}

export interface CreateMenuPayload {
  nombre: string;
  descripcion?: string | null;
  activo?: boolean;
}

export interface MenuListParams {
  pageIndex?: number;
  pageSize?: number;
  query?: string;
}
