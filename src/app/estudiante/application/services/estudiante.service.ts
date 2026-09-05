import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface EstudianteDashboardResponse {
  usuario: {
    id: number;
    nombre: string;
    email: string;
    ci: string | null;
  };
  resumen: {
    cursos_activos: number;
    certificados: number;
    clases_grabadas: number;
  };
}

export interface GrabacionZoom {
  id: number;
  tema: string;
  play_url: string;
  duracion: number;
  recording_start: string;
}

export interface CursoEstudiante {
  id_ins: number;
  fecha_ins: string;
  estado_inscripcion: string;
  id_imp: number;
  nombre_materia: string;
  titulo_personalizado: string | null;
  imparte_fecha_inicio: string;
  imparte_fecha_fin: string;
  horas_academicas: number;
  nombre_docente: string;
  grabaciones: GrabacionZoom[];
}

export interface CertificadoEstudiante {
  id: number;
  nombre_en_certificado: string;
  programa_en_certificado: string;
  codigo_verificacion: string;
  qr_url: string;
  archivo_url: string | null;
  estado: string;
  created_at: string;
}

export interface CuotaEstudiante {
  id_fechapago: number;
  nro_pago: number;
  monto_a_pagar: number;
  fecha_limite: string | null;
  pagado: boolean;
  monto_pagado: number | null;
  fecha_pago: string | null;
  metodo_pago: string | null;
}

export interface PlanPagoEstudiante {
  id_ins: number;
  nombre_programa: string;
  programa_slug: string | null;
  periodo: string | null;
  gestion: string | null;
  fecha_ins: string;
  fecha_inicio: string | null;
  fecha_fin: string | null;
  canal_venta: string | null;
  total_plan: number;
  total_pagado: number;
  saldo: number;
  estado_pago: 'pagado' | 'parcial' | 'pendiente';
  cuotas: CuotaEstudiante[];
  nro_pagos: number;
}

@Injectable({ providedIn: 'root' })
export class EstudianteService {
  private http = inject(HttpClient);
  private apiUrl = '/api/v1/estudiante';

  getDashboard(): Observable<EstudianteDashboardResponse> {
    return this.http.get<EstudianteDashboardResponse>(`${this.apiUrl}/dashboard`);
  }

  getMisCursos(): Observable<{ data: CursoEstudiante[] }> {
    return this.http.get<{ data: CursoEstudiante[] }>(`${this.apiUrl}/mis-cursos`);
  }

  getMisCertificados(): Observable<{ data: CertificadoEstudiante[] }> {
    return this.http.get<{ data: CertificadoEstudiante[] }>(`${this.apiUrl}/mis-certificados`);
  }

  getMisPagos(): Observable<{ data: PlanPagoEstudiante[] }> {
    return this.http.get<{ data: PlanPagoEstudiante[] }>(`${this.apiUrl}/mis-pagos`);
  }
}
