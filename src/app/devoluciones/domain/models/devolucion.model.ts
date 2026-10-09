export type EstadoDevolucion = 'pendiente' | 'aprobada' | 'rechazada' | 'cancelada';

export interface DevolucionPanel {
  id:                     number;
  id_ins:                 number;
  id_us:                  number;
  monto:                  number;
  motivo:                 string | null;
  estado:                 EstadoDevolucion;
  nota_respuesta:         string | null;
  fecha_respuesta:        string | null;
  documento_url:          string | null;
  created_at:             string;
  programa:               string | null;
  registrado_por_nombre:  string | null;
  estudiante:             string | null;
  ci:                     string | null;
  celular:                string | null;
}

export interface DevolucionesResponse {
  data:            DevolucionPanel[];
  pendientes:      number;
  monto_pendiente: number;
}
