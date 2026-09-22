import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { PlanAcademico, PlanAcademicoListParams, PlanAcademicoListResponse, CreatePlanAcademicoPayload } from '../../domain/models/plan-academico.model';

@Injectable({ providedIn: 'root' })
export class PlanAcademicoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/planes-academicos';

  getAll(params: PlanAcademicoListParams = {}): Observable<PlanAcademicoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    p = p.set('conInactivos', 'true');
    return this.http.get<PlanAcademicoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<PlanAcademico> {
    return this.http.get<PlanAcademico>(`${this.baseUrl}/${id}`);
  }

  create(data: CreatePlanAcademicoPayload): Observable<PlanAcademico> {
    return this.http.post<PlanAcademico>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreatePlanAcademicoPayload>): Observable<PlanAcademico> {
    return this.http.put<PlanAcademico>(`${this.baseUrl}/${id}`, data);
  }

  updateWithFormData(id: number, data: FormData): Observable<PlanAcademico> {
    return this.http.post<PlanAcademico>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  // Cuotas de un plan
  getCuotas(idPlan: number): Observable<any> {
    return this.http.get<any>('/api/v1/fechas-pago', { params: { id_plan: idPlan, pageSize: 100 } });
  }

  createCuota(data: any): Observable<any> {
    return this.http.post<any>('/api/v1/fechas-pago', data);
  }

  updateCuota(id: number, data: any): Observable<any> {
    return this.http.put<any>(`/api/v1/fechas-pago/${id}`, data);
  }

  deleteCuota(id: number): Observable<any> {
    return this.http.delete<any>(`/api/v1/fechas-pago/${id}`);
  }

  generarLoteCuotas(data: {
    id_plan: number;
    nro_cuotas: number;
    monto_total: number;
    fecha_inicio: string;
    intervalo: string;
    tipo_tramite?: string;
  }): Observable<any> {
    return this.http.post<any>('/api/v1/fechas-pago/generar-lote', data);
  }

  updateModoPlan(id: number, modo_fechas: string): Observable<any> {
    const fd = new FormData();
    fd.append('_method', 'PUT');
    fd.append('modo_fechas', modo_fechas);
    return this.http.post<any>(`/api/v1/planes-academicos/${id}`, fd);
  }
}
