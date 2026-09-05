export interface EnvioCertificado {
  id:             number;
  id_ins:         number;
  ciudad_destino: string;
  fecha_envio:    string;
  imagen_guia:    string;
  aclaraciones:   string | null;
  costo:          number | null;
  id_us_reg:      number | null;
  created_at:     string | null;
}

export interface CreateEnvioCertificadoPayload {
  id_ins:         number;
  ciudad_destino: string;
  fecha_envio:    string;
  imagen_guia:    File;
  aclaraciones?:  string | null;
  costo?:         number | null;
}
