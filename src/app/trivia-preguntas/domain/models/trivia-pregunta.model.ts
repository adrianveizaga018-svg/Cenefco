export interface TriviaOpcion {
  id?:          number;
  texto:        string;
  es_correcta:  boolean;
  orden?:       number;
}
export interface TriviaPregunta {
  id:                       number;
  categoria_id:             number;
  nivel_id:                 number;
  enunciado:                string;
  imagen_url?:              string | null;
  tiempo_limite_segundos:   number;
  activo:                   boolean;
  created_at?:              string | null;
  opciones:                 TriviaOpcion[];
}
export interface TriviaPreguntaListResponse {
  data:  TriviaPregunta[];
  total: number;
}
export interface TriviaPreguntaListParams {
  query?:        string;
  pageIndex?:    number;
  pageSize?:     number;
  refresh?:      number;
  categoria_id?: number | null;
  nivel_id?:     number | null;
}
