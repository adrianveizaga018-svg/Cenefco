import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Planilla, PlanillaListResponse, GenerarPlanillaPayload, PlanillaPreviewResponse } from '../../domain/models/planilla.model';

@Injectable({ providedIn: 'root' })
export class PlanillaService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/planillas';

  preview(anio: number, mes: number): Observable<PlanillaPreviewResponse> {
    const params = new HttpParams().set('anio', anio).set('mes', mes);
    return this.http.get<PlanillaPreviewResponse>(`${this.base}/preview`, { params });
  }

  getAll(params: { pageIndex?: number; pageSize?: number }): Observable<PlanillaListResponse> {
    let httpParams = new HttpParams();
    Object.entries(params).forEach(([key, value]) => {
      if (value !== null && value !== undefined) {
        httpParams = httpParams.set(key, String(value));
      }
    });
    return this.http.get<PlanillaListResponse>(this.base, { params: httpParams });
  }

  getById(id: number): Observable<Planilla> {
    return this.http.get<Planilla>(`${this.base}/${id}`);
  }

  generar(payload: GenerarPlanillaPayload): Observable<Planilla> {
    return this.http.post<Planilla>(`${this.base}/generar`, payload);
  }

  exportPdf(id: number): Observable<Blob> {
    return this.http.get(`${this.base}/${id}/pdf`, { responseType: 'blob' });
  }

  exportExcel(id: number): Observable<Blob> {
    return this.http.get(`${this.base}/${id}/excel`, { responseType: 'blob' });
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
