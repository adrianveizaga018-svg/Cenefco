export interface Evento {
  id: number;
  titulo: string;
  slug: string | null;
  tipo: string | null;
  modalidad: string | null;
  descripcion: string | null;
  imagen_url: string | null;
  lugar: string | null;
  fecha_inicio: string | null;
  fecha_fin: string | null;
  todo_el_dia: boolean;
  estado: string;
  url_transmision: string | null;
  gratuito: boolean;
  precio: string | null;
  cupo_maximo: number | null;
  destacado: boolean;
  created_at: string | null;
}

export interface EventoListResponse {
  data: Evento[];
  total: number;
}

export interface EventoListParams {
  pageIndex?: number;
  pageSize?: number;
  query?: string;
  refresh?: number;
}

export interface CreateEventoPayload {
  titulo: string;
  tipo?: string | null;
  modalidad?: string;
  descripcion?: string | null;
  imagen_url?: string | null;
  lugar?: string | null;
  fecha_inicio: string;
  fecha_fin?: string | null;
  todo_el_dia?: boolean;
  estado?: string;
  url_transmision?: string | null;
  gratuito?: boolean;
  precio?: string | null;
  cupo_maximo?: number | null;
}
