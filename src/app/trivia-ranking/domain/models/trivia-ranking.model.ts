export interface TriviaRankingItem {
  posicion:          number;
  usuario_id:        number;
  nombre:            string;
  avatar_url?:       string | null;
  puntaje_total:     number;
  partidas_jugadas:  number;
  partidas_ganadas:  number;
}

export interface TriviaRankingResponse {
  data: TriviaRankingItem[];
}
