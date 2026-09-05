export type TipoEfecto = 'nieve' | 'confetti' | 'hojas' | 'estrellas';

export const TIPOS_EFECTO: { value: TipoEfecto; label: string; emoji: string }[] = [
  { value: 'nieve',     label: 'Nieve',         emoji: '❄️' },
  { value: 'confetti',  label: 'Confetti',       emoji: '🎊' },
  { value: 'hojas',     label: 'Hojas de otoño', emoji: '🍂' },
  { value: 'estrellas', label: 'Estrellas',      emoji: '✨' },
];

export interface EfectoEspecial {
  id:               number;
  nombre:           string;
  tipo_efecto:      TipoEfecto;
  color_primario:   string | null;
  color_secundario: string | null;
  fecha_inicio:     string;
  fecha_fin:        string;
  intensidad:       number;
  activo:           boolean;
  created_at:       string | null;
}

export interface EfectoEspecialListResponse { data: EfectoEspecial[]; total: number; }
export interface EfectoEspecialListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
