export interface UsuarioMoodle {
  id_usmoodle: number;
  id_us: number;
  id_moodle: number;
  moodle_id_user?: string | null;
  estado: number;
  fecha_reg?: string;
}

export interface UsuarioMoodleListResponse { data: UsuarioMoodle[]; total: number; }

export interface UsuarioMoodleListParams {
  pageIndex?: number;
  pageSize?:  number;
  id_us?: number;
  id_moodle?: number;
  refresh?:   number;
}

export interface CreateUsuarioMoodlePayload {
  id_usmoodle: number;
  id_us: number;
  id_moodle: number;
  moodle_id_user?: string | null;
  estado: number;
}

export interface UpdateUsuarioMoodlePayload {
  id_moodle?: number;
  moodle_id_user?: string | null;
  estado?: number;
}
