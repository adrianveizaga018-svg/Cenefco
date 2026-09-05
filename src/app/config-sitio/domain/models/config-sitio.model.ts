export interface ConfigSitioItem {
  id: number;
  clave: string;
  valor: string | null;
  tipo: string;
  grupo: string | null;
  etiqueta: string | null;
  descripcion: string | null;
  es_publica: boolean;
}

export interface ConfigSitioResponse {
  data: ConfigSitioItem[];
}

export type ConfigSitioGrupo = {
  grupo: string;
  etiqueta: string;
  items: ConfigSitioItem[];
};

export const GRUPOS_ETIQUETAS: Record<string, string> = {
  identidad: 'Identidad',
  institucional: 'Información Institucional',
  contacto: 'Contacto',
  seo: 'SEO y Analytics',
  inscripciones: 'Inscripciones',
  apariencia: 'Apariencia',
};
