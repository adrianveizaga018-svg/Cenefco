export type TipoCampo =
  | 'text' | 'email' | 'number' | 'date' | 'textarea'
  | 'select' | 'boolean' | 'file_pdf' | 'file_image';

export const TIPOS_CAMPO: { value: TipoCampo; label: string }[] = [
  { value: 'text',       label: 'Texto corto'       },
  { value: 'email',      label: 'Correo electrónico' },
  { value: 'number',     label: 'Número'             },
  { value: 'date',       label: 'Fecha'              },
  { value: 'textarea',   label: 'Texto largo'        },
  { value: 'select',     label: 'Lista desplegable'  },
  { value: 'boolean',    label: 'Casilla (Sí/No)'    },
  { value: 'file_pdf',   label: 'Archivo PDF'        },
  { value: 'file_image', label: 'Imagen'             },
];

export interface CategoriaCampo {
  id:           number;
  categoria_id: number;
  nombre_campo: string;
  etiqueta:     string;
  tipo_campo:   TipoCampo;
  requerido:    boolean;
  paso:         number;
  orden:        number;
  activo:       boolean;
  ayuda:        string | null;
  opciones:     (string | Record<string, unknown>)[] | null;
  validacion:   Record<string, unknown> | null;
}

export interface CreateCategoriaCampoPayload {
  nombre_campo: string;
  etiqueta:     string;
  tipo_campo:   TipoCampo;
  requerido:    boolean;
  orden?:       number;
  activo?:      boolean;
  ayuda?:       string | null;
  opciones?:    string[] | null;
  validacion?:  Record<string, unknown> | null;
}

export interface CategoriaCampoListResponse {
  data:  CategoriaCampo[];
  total: number;
}

export interface CategoriaProgramaItem {
  id:                number;
  tipo_programa_id:  number | null;
  nombre:            string;
  slug:              string;
  descripcion:       string | null;
  imagen_url:        string | null;
  imagen_alt:        string | null;
  icono:             string | null;
  color:             string | null;
  orden:             number;
  activo:            boolean;
  meta_titulo:       string | null;
  meta_descripcion:  string | null;
  comision_monto:    number;
  created_at:        string | null;
}

export interface CategoriaProgramaListResponse { data: CategoriaProgramaItem[]; total: number; }
export interface CategoriaProgramaListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
