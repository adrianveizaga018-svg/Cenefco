export interface DashboardResumen {
  total_inscripciones:   number;
  total_cursos:          number;
  total_docentes:        number;
  cursos_migrados:       number;
  participantes_migrados: number;
  conversaciones_whatsapp: number;
}

export interface DashboardStats {
  resumen: DashboardResumen;
}
