export interface MdlCourse {
  id: number;
  fullname?: string | null;
  shortname?: string | null;
  id_docente?: number | null;
  category?: number | null;
  sigla?: string | null;
  paralelo?: string | null;
  cupo?: string | null;
  gestion?: string | null;
  titulo_personalizado?: string | null;
  imparte_fecha_inicio?: string | null;
  imparte_fecha_fin?: string | null;
  estado: number;
}

export interface MdlCourseListResponse { data: MdlCourse[]; total: number; }

export interface MdlCourseListParams {
  pageIndex?: number;
  pageSize?:  number;
  id_docente?: number;
  refresh?:   number;
}

export interface CreateMdlCoursePayload {
  id: number;
  fullname?: string | null;
  shortname?: string | null;
  id_docente?: number | null;
  sigla?: string | null;
  paralelo?: string | null;
  cupo?: string | null;
  gestion?: string | null;
  titulo_personalizado?: string | null;
  imparte_fecha_inicio?: string | null;
  imparte_fecha_fin?: string | null;
  estado: number;
}

export interface UpdateMdlCoursePayload {
  fullname?: string | null;
  shortname?: string | null;
  id_docente?: number | null;
  sigla?: string | null;
  paralelo?: string | null;
  cupo?: string | null;
  gestion?: string | null;
  titulo_personalizado?: string | null;
  imparte_fecha_inicio?: string | null;
  imparte_fecha_fin?: string | null;
  estado?: number;
}
