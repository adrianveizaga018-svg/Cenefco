import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface DashboardKpis {
  ingresos: number;
  inscripciones: number;
  leads_total: number;
  leads_sin_contactar: number;
  inversion_publicidad: number;
  roi: number | null;
  tareas_pendientes: number;
}

export interface DashboardGrafico {
  mes?: string;
  total?: number;
  estado?: string;
  canal?: string;
  nombre_programa?: string;
  total_ingresos?: number;
  total_inscritos?: number;
}

export interface DashboardData {
  periodo: { desde: string; hasta: string };
  kpis: DashboardKpis;
  graficos: {
    ingresos_por_mes: DashboardGrafico[];
    leads_por_estado: DashboardGrafico[];
    top_programas: DashboardGrafico[];
    inscripciones_por_canal: DashboardGrafico[];
  };
}

@Injectable({ providedIn: 'root' })
export class DashboardGerencialService {
  private http = inject(HttpClient);
  private api = '/api/v1';

  getDashboard(desde: string, hasta: string): Observable<DashboardData> {
    const params = new HttpParams().set('fecha_desde', desde).set('fecha_hasta', hasta);
    return this.http.get<DashboardData>(`${this.api}/dashboard-gerencia`, { params });
  }
}