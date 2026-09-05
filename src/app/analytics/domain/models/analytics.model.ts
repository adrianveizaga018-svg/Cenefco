export interface AnalyticsKpis {
  total_visitas:     number;
  sesiones_unicas:   number;
  duracion_promedio: number | null;
  pagina_top:        string | null;
}

export interface PorDia       { fecha:       string; visitas: number; }
export interface PorHora      { hora:        number; visitas: number; }
export interface TopPagina    { ruta:        string; visitas: number; }
export interface PorPais      { pais:        string; visitas: number; }
export interface PorDispositivo { dispositivo: string; visitas: number; }
export interface PorNavegador { navegador:   string; visitas: number; }
export interface PorSO        { so:          string; visitas: number; }

export interface AnalyticsStats {
  kpis:            AnalyticsKpis;
  top_paginas:     TopPagina[];
  por_dia:         PorDia[];
  por_hora:        PorHora[];
  por_pais:        PorPais[];
  por_dispositivo: PorDispositivo[];
  por_navegador:   PorNavegador[];
  por_so:          PorSO[];
}
