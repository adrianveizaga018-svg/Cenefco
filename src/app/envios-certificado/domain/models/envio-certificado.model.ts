export const DEPARTAMENTOS_BOLIVIA = [
  'La Paz', 'Cochabamba', 'Santa Cruz', 'Oruro',
  'Potosí', 'Chuquisaca', 'Tarija', 'Beni', 'Pando',
] as const;

export type DepartamentoBolivia = typeof DEPARTAMENTOS_BOLIVIA[number];
export type EstadoEnvio = 'pendiente' | 'enviado' | 'entregado';

export interface EnvioCertificado {
  id:                 number;
  id_ins:             number;
  departamento:       string;
  ciudad_destino:     string;
  fecha_envio:        string;
  imagen_guia:        string;
  aclaraciones:       string | null;
  estado:             EstadoEnvio;
  agencia:            string | null;
  nro_seguimiento:    string | null;
  fecha_entrega:      string | null;
  notificado:         boolean;
  costo:              number | null;
  id_us_reg:          number | null;
  enviado_por:        number | null;
  enviado_at:         string | null;
  created_at:         string | null;
  // Enriquecidos en dashboard
  estudiante_nombre?: string | null;
  estudiante_ci?:     string | null;
  estudiante_celular?: string | null;
  programa_nombre?:   string | null;
}

export interface CreateEnvioPayload {
  id_ins:          number;
  departamento?:   string;   // opcional al crear desde inscripcion-detail legacy
  ciudad_destino:  string;
  fecha_envio:     string;
  imagen_guia?:    File | null;
  aclaraciones?:   string | null;
  costo?:          number | null;
}

export interface UpdateEnvioPayload {
  estado?:          EstadoEnvio;
  agencia?:         string | null;
  nro_seguimiento?: string | null;
  fecha_entrega?:   string | null;
  aclaraciones?:    string | null;
  notificado?:      boolean;
}

export interface EnvioDashboardFiltros {
  estado?:       string;
  departamento?: string;
  ci?:           string;
  fecha_desde?:  string;
  fecha_hasta?:  string;
  page?:         number;
  per_page?:     number;
}
