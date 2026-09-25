import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface UsuarioAcademico {
  id_us:     number;
  nombre:    string;
  appaterno: string | null;
  ci:        string | null;
  email?:    string | null;
  celular?:  string | null;
}

export interface ImparticionOption {
  id_imp:         number;
  periodo:        string | null;
  gestion:        string | null;
  materia_nombre: string | null;
  paralelo:       string | null;
  id_mat:         number | null;
  id_programa:    number | null;
  docente_nombre: string | null;
}

export interface PlanAcademicoOption {
  id_plan:     number;
  titulo:      string;
  convenio:    string | null;
  nro_cuotas:  number;
  costo:       string | null;
  estado?:     any;
}

/**
 * Servicio transversal para obtener catálogos académicos usados como opciones
 * en formularios de varios módulos (inscripciones, pagos, cartas generadas, etc.).
 */
@Injectable({ providedIn: 'root' })
export class AcademicoService {
  private http = inject(HttpClient);

  getUsuariosAcademicos(params: { pageSize?: number; pageIndex?: number; conInactivos?: boolean; query?: string } = {}): Observable<{ data: UsuarioAcademico[] }> {
    const httpParams: Record<string, string> = {
      pageSize:  String(params.pageSize  ?? 300),
      pageIndex: String(params.pageIndex ?? 1),
      conInactivos: params.conInactivos ? 'true' : 'false',
    };
    if (params.query) httpParams['query'] = params.query;
    return this.http.get<{ data: UsuarioAcademico[] }>('/api/v1/usuarios-academicos', { params: httpParams });
  }

  getImparticiones(params: { pageSize?: number; pageIndex?: number; conInactivos?: boolean } = {}): Observable<{ data: ImparticionOption[] }> {
    return this.http.get<{ data: ImparticionOption[] }>('/api/v1/imparticiones', {
      params: {
        pageSize:     String(params.pageSize  ?? 200),
        pageIndex:    String(params.pageIndex ?? 1),
        conInactivos: params.conInactivos ? 'true' : 'false',
      },
    });
  }

  getPlanesByMateria(idMat: number, params: { pageSize?: number } = {}): Observable<{ data: PlanAcademicoOption[] }> {
    return this.http.get<{ data: PlanAcademicoOption[] }>('/api/v1/planes-academicos', {
      params: {
        id_mat:   idMat.toString(),
        pageSize: String(params.pageSize ?? 50),
      },
    });
  }

  getUsuarioAcademicoById(id: number): Observable<UsuarioAcademico> {
    return this.http.get<UsuarioAcademico>(`/api/v1/usuarios-academicos/${id}`);
  }

  updateUsuarioAcademico(id: number, data: Partial<UsuarioAcademico> & { apmaterno?: string | null; email?: string | null; celular?: string | null }): Observable<UsuarioAcademico> {
    return this.http.put<UsuarioAcademico>(`/api/v1/usuarios-academicos/${id}`, data);
  }

  getPlanesAcademicos(params: { pageSize?: number; soloValidos?: boolean } = {}): Observable<any> {
    const p: Record<string, string> = { pageSize: String(params.pageSize ?? 200) };
    if (params.soloValidos) p['solo_validos'] = '1';
    return this.http.get<any>('/api/v1/planes-academicos', { params: p });
  }

  getCatalogoTareas(): Observable<any[]> {
    return this.http.get<any[]>('/api/v1/catalogo-tareas');
  }

  getMediosPago(params: { pageSize?: number } = {}): Observable<{ data: { id: number; nombre: string }[] }> {
    return this.http.get<{ data: { id: number; nombre: string }[] }>('/api/v1/medios-pago', {
      params: { pageSize: String(params.pageSize ?? 100) },
    });
  }

  /** Imparticiones que tienen este plan habilitado (para validar fechas de cuotas). */
  getImparticionesByPlan(idPlan: number): Observable<{ id_imp: number; imparte_fecha_inicio: string | null; imparte_fecha_fin: string | null; nombre_programa: string | null }[]> {
    return this.http.get<{ id_imp: number; imparte_fecha_inicio: string | null; imparte_fecha_fin: string | null; nombre_programa: string | null }[]>(
      `/api/v1/planes-academicos/${idPlan}/imparticiones`
    );
  }

  /** Cuotas (FechaPago templates) de un plan específico, para validación de fechas al crear curso. */
  getCuotasPlan(idPlan: number): Observable<{ data: { id_fechapago: number; nro_pago: string | null; tipo_tramite: string | null; monto_a_pagar: number; fecha_fin: string | null; dias_desde_inscripcion: number | null }[] }> {
    return this.http.get<any>('/api/v1/fechas-pago', {
      params: { id_plan: idPlan, pageSize: '50', pageIndex: '1' },
    });
  }
}
