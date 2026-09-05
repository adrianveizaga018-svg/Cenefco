export interface Moodle {
  id_moodle: number;
  titulo_moodle: string;
  cp_moodle_servidor?: string | null;
  cp_moodle_base_datos?: string | null;
  cp_moodle_usuario_bd?: string | null;
  cp_moodle_contrasena?: string | null;
  cp_url_campus?: string | null;
  estado: number;
}

export interface MoodleListResponse { data: Moodle[]; total: number; }

export interface MoodleListParams {
  pageIndex?: number;
  pageSize?: number;
  query?: string;
  refresh?: number;
}

export interface CreateMoodlePayload {
  id_moodle: number;
  titulo_moodle: string;
  cp_moodle_servidor?: string | null;
  cp_moodle_base_datos?: string | null;
  cp_moodle_usuario_bd?: string | null;
  cp_moodle_contrasena?: string | null;
  cp_url_campus?: string | null;
  estado: number;
}

export interface UpdateMoodlePayload {
  titulo_moodle?: string;
  cp_moodle_servidor?: string | null;
  cp_moodle_base_datos?: string | null;
  cp_moodle_usuario_bd?: string | null;
  cp_moodle_contrasena?: string | null;
  cp_url_campus?: string | null;
  estado?: number;
}
