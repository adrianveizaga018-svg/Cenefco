export interface DocumentoAcademico {
  id_documento: number;
  id_us: number;
  id_us_reg?: number;
  num_documento?: number;
  id_fechapago?: number | null;
  id_fechadoc?: number | null;
  fecha_dejo_fisico?: string | null;
  dejo_documento_fisico?: number | null;
  documento_digital?: string | null;
  observacion_doc?: string | null;
  estado: number;
  fecha_reg?: string;
}

export interface DocumentoAcademicoListResponse { data: DocumentoAcademico[]; total: number; }

export interface DocumentoAcademicoListParams {
  pageIndex?: number;
  pageSize?: number;
  conInactivos?: number;
}

export interface CreateDocumentoPayload {
  id_documento: number;
  id_us: number;
  id_fechapago?: number | null;
  id_fechadoc?: number | null;
  fecha_dejo_fisico?: string | null;
  dejo_documento_fisico?: number | null;
  documento_digital?: string | null;
  observacion_doc?: string | null;
  estado: number;
}

export interface UpdateDocumentoPayload {
  id_fechapago?: number | null;
  id_fechadoc?: number | null;
  fecha_dejo_fisico?: string | null;
  dejo_documento_fisico?: number | null;
  documento_digital?: string | null;
  observacion_doc?: string | null;
  estado?: number;
}
