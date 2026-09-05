export interface Rol {
  id: number;
  nombre: string;
}

export interface Usuario {
  id: number;
  nombre: string;
  apellido: string;
  email: string;
  tipo: string;
  activo: boolean;
  emailVerificado: boolean;
  rolId: number | null;
  rolNombre: string | null;
  permisos: string[] | null;
  createdAt: string | null;
}

export interface UsuarioListResponse {
  data: Usuario[];
  total: number;
}

export interface UsuarioListParams {
  pageIndex?: number;
  pageSize?: number;
  query?: string;
  refresh?: number;
  origen?: 'sistema' | 'portal';
}

export interface CreateUsuarioPayload {
  nombre: string;
  apellido: string;
  email: string;
  password: string;
  tipo?: string;
  rol_id?: number | null;
  activo: boolean;
}

export interface UpdateUsuarioPayload {
  nombre: string;
  apellido: string;
  email: string;
  password?: string | null;
  tipo?: string;
  rol_id?: number | null;
  activo: boolean;
}
