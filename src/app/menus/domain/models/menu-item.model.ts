export interface MenuItem {
  id: number;
  menu_id: number;
  parent_id: number | null;
  etiqueta: string;
  url: string | null;
  orden: number;
  icono: string | null;
  activo: boolean;
  abrir_nueva_ventana: boolean;
}

export interface MenuItemListResponse {
  data: MenuItem[];
  total: number;
}

export interface CreateMenuItemPayload {
  menu_id: number;
  parent_id?: number | null;
  etiqueta: string;
  url?: string | null;
  orden?: number;
  icono?: string | null;
  activo?: boolean;
  abrir_nueva_ventana?: boolean;
}

export interface MenuItemListParams {
  menu_id?: number;
  pageIndex?: number;
  pageSize?: number;
  query?: string;
}
