export interface MdlUser {
  id: number;
  nombre_usuario?: string | null;
  nombre?: string | null;
  appaterno?: string | null;
  apmaterno?: string | null;
  ci?: string | null;
  email?: string | null;
  telefono?: string | null;
  celular?: string | null;
  direccion?: string | null;
  ciudad?: string | null;
  estado: number;
}

export interface MdlUserListResponse { data: MdlUser[]; total: number; }

export interface MdlUserListParams {
  pageIndex?: number;
  pageSize?:  number;
  query?: string;
  refresh?:   number;
}

export interface CreateMdlUserPayload {
  id: number;
  nombre_usuario?: string | null;
  nombre?: string | null;
  appaterno?: string | null;
  apmaterno?: string | null;
  ci?: string | null;
  email?: string | null;
  celular?: string | null;
  ciudad?: string | null;
  estado: number;
}

export interface UpdateMdlUserPayload {
  nombre_usuario?: string | null;
  nombre?: string | null;
  appaterno?: string | null;
  apmaterno?: string | null;
  ci?: string | null;
  email?: string | null;
  celular?: string | null;
  ciudad?: string | null;
  estado?: number;
}
