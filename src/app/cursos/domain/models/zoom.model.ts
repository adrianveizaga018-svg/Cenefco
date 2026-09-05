export interface ZoomMeeting {
  id: number;
  tema: string;
  fecha: string;
  duracion_min: number;
  link_invitados: string;
  password: string;
}

export interface ZoomRecording {
  curso: string;
  fecha: string;
  duracion_min: number;
  tipo_archivo: string;
  tamanio_mb: number;
  link_descarga: string | null;
  link_play: string | null;
}

export interface CrearReunionPayload {
  tipo: 'unica' | 'multisesion';
  tema?: string;
  curso?: string;
  fecha_inicio: string;
  duracion_min?: number;
  n_sesiones?: number;
  dias_entre?: number;
}

export interface ZoomMeetingResult {
  id: number;
  tema: string;
  fecha: string;
  duracion_min: number;
  link_anfitrion: string;
  link_invitados: string;
  password: string;
}

export interface CrearReunionResponse {
  tipo: 'unica' | 'multisesion';
  reunion?: ZoomMeetingResult;
  sesiones?: ZoomMeetingResult[];
}
