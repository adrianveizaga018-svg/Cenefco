export interface CitaAsesoria {
  id_cita_asesoria:  number;
  nombres:           string;
  apellidos:         string | null;
  email:             string | null;
  celular:           string | null;
  mensaje:           string | null;
  programa_interes:  string | null;
  fecha_preferida:   string | null;
  hora_preferida:    string | null;
  estado:            string;
  observacion:       string | null;
  fecha_solicitud:   string;
}

export interface CitaAsesoriaListResponse {
  data:  CitaAsesoria[];
  total: number;
}

export interface CitaAsesoriaListParams {
  pageIndex: number;
  pageSize:  number;
  query?:    string;
  estado?:   string;
  refresh?:  number;
}
