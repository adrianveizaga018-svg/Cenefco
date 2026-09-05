export type TipoCampo = 'text' | 'email' | 'tel' | 'number' | 'date' | 'select' | 'textarea' | 'file' | 'checkbox';

export interface ValidacionCampo {
  min?:         number | null;
  max?:         number | null;
  min_length?:  number | null;
  max_length?:  number | null;
  patron?:      string | null;
  extensiones?: string[] | null;
  tamano_max_mb?: number | null;
}

export interface CampoFormulario {
  nombre_campo: string;
  etiqueta:     string;
  tipo:         TipoCampo;
  requerido?:   boolean;
  opciones?:    string[];
  placeholder?: string;
  ayuda?:       string;
  validacion?:  ValidacionCampo | null;
}

export interface Formulario {
  id:          number;
  nombre:      string;
  slug:        string;
  descripcion: string | null;
  campos:      CampoFormulario[];
  activo:      boolean;
  created_at:  string | null;
  updated_at:  string | null;
}

export interface FormularioListResponse {
  data:  Formulario[];
  total: number;
}

export interface FormularioListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}

export interface CreateFormularioPayload {
  nombre:      string;
  slug?:       string | null;
  descripcion?: string | null;
  campos?:     CampoFormulario[];
  activo?:     boolean;
}
