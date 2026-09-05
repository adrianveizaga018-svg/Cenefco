export interface ZoomReunion {
  id: string; tema: string; fecha: string;
  duracion_min: number; link_invitados: string; password: string;
}
export interface ZoomGrabacion {
  curso: string; fecha: string; duracion_min: number;
  tipo_archivo: string; tamanio_mb: number;
  link_descarga: string | null; link_play: string | null;
}
export interface ZoomCuenta {
  id: number; nombre: string; descripcion: string | null;
  account_id: string; timezone: string;
  predeterminada: boolean; activa: boolean; created_at: string | null;
}
export interface CreateZoomCuentaPayload {
  nombre: string; account_id: string; client_id: string;
  client_secret: string; timezone?: string; descripcion?: string | null; activa?: boolean;
}
export interface CreateReunionPayload {
  cuenta_id?: number; tipo: 'unica' | 'multisesion';
  tema?: string; curso?: string; fecha_inicio: string;
  duracion_min?: number; n_sesiones?: number; dias_entre?: number;
}
export interface ReunionesResponse  { meetings: ZoomReunion[]; }
export interface GrabacionesResponse { recordings: ZoomGrabacion[]; }
export interface CuentasResponse { data: ZoomCuenta[]; total: number; }
export interface CrearReunionResponse {
  tipo: 'unica' | 'multisesion';
  reunion?: ZoomReunion; sesiones?: ZoomReunion[];
}
