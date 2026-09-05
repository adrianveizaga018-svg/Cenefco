export type TipoCampo = 'text' | 'email' | 'tel' | 'number' | 'date' | 'select' | 'textarea' | 'file' | 'checkbox';

export interface CampoFormulario {
  nombre_campo: string;
  etiqueta:     string;
  tipo:         TipoCampo;
  requerido?:   boolean;
  opciones?:    string[];
  placeholder?: string;
  ayuda?:       string;
}

export interface GeneradorFormulario {
  id:          number;
  nombre:      string;
  slug:        string;
  descripcion: string | null;
  campos:      CampoFormulario[];
  activo:      boolean;
  created_at:  string | null;
  updated_at:  string | null;
}

export interface GeneradorFormularioListResponse {
  data:  GeneradorFormulario[];
  total: number;
}

export interface GeneradorFormularioListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
  refresh?:   number;
}

export interface SaveFormularioPayload {
  nombre:       string;
  slug?:        string | null;
  descripcion?: string | null;
  campos?:      CampoFormulario[];
  activo?:      boolean;
}

export const TIPOS_CAMPO: { value: TipoCampo; label: string; icon: string }[] = [
  { value: 'text',     label: 'Texto corto',     icon: 'lucideType' },
  { value: 'email',    label: 'Correo',          icon: 'lucideMail' },
  { value: 'tel',      label: 'Teléfono',        icon: 'lucidePhone' },
  { value: 'number',   label: 'Número',          icon: 'lucideHash' },
  { value: 'date',     label: 'Fecha',           icon: 'lucideCalendar' },
  { value: 'textarea', label: 'Texto largo',     icon: 'lucideAlignLeft' },
  { value: 'select',   label: 'Lista opciones',  icon: 'lucideChevronDown' },
  { value: 'file',     label: 'Archivo / Foto',  icon: 'lucidePaperclip' },
  { value: 'checkbox', label: 'Casilla',         icon: 'lucideSquareCheck' },
];

export const NOMBRES_SUGERIDOS: { nombre_campo: string; etiqueta: string; tipo: TipoCampo }[] = [
  { nombre_campo: 'ci',               etiqueta: 'Carnet de Identidad',    tipo: 'text'     },
  { nombre_campo: 'nombre_completo',  etiqueta: 'Nombre completo',        tipo: 'text'     },
  { nombre_campo: 'nombres',          etiqueta: 'Nombres',                tipo: 'text'     },
  { nombre_campo: 'paterno',          etiqueta: 'Apellido Paterno',       tipo: 'text'     },
  { nombre_campo: 'materno',          etiqueta: 'Apellido Materno',       tipo: 'text'     },
  { nombre_campo: 'email',            etiqueta: 'Correo electrónico',     tipo: 'email'    },
  { nombre_campo: 'celular',          etiqueta: 'Celular / WhatsApp',     tipo: 'tel'      },
  { nombre_campo: 'telefono',         etiqueta: 'Teléfono',               tipo: 'tel'      },
  { nombre_campo: 'profesion',        etiqueta: 'Profesión / Carrera',    tipo: 'text'     },
  { nombre_campo: 'empresa',          etiqueta: 'Empresa / Institución',  tipo: 'text'     },
  { nombre_campo: 'cargo',            etiqueta: 'Cargo',                  tipo: 'text'     },
  { nombre_campo: 'ciudad',           etiqueta: 'Ciudad',                 tipo: 'text'     },
  { nombre_campo: 'grado_academico',  etiqueta: 'Grado Académico',        tipo: 'select'   },
  { nombre_campo: 'carnet_anverso',   etiqueta: 'Carnet Anverso',         tipo: 'file'     },
  { nombre_campo: 'carnet_reverso',   etiqueta: 'Carnet Reverso',         tipo: 'file'     },
  { nombre_campo: 'foto_perfil',      etiqueta: 'Foto de Perfil',         tipo: 'file'     },
  { nombre_campo: 'comprobante_pago', etiqueta: 'Comprobante de Pago',    tipo: 'file'     },
  { nombre_campo: 'cedula',           etiqueta: 'Cédula',                 tipo: 'text'     },
  { nombre_campo: 'observacion',      etiqueta: 'Observación',            tipo: 'textarea' },
  { nombre_campo: 'acepta_terminos',  etiqueta: 'Acepta términos y condiciones', tipo: 'checkbox' },
];
