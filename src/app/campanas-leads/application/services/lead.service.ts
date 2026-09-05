import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  ImportarLeadsResult,
  Lead,
  LeadListResponse,
  CreateLeadPayload,
  UpdateLeadPayload,
} from '../../domain/models/campana-lead.model';

@Injectable({ providedIn: 'root' })
export class LeadService {
  private http = inject(HttpClient);
  private base(campanaLeadId: number) {
    return `/api/v1/campanas-leads/${campanaLeadId}/leads`;
  }

  getAll(campanaLeadId: number, params: {
    pageIndex?: number;
    pageSize?: number;
    query?: string;
    sortKey?: string;
    sortOrder?: string;
  }): Observable<LeadListResponse> {
    let httpParams = new HttpParams();
    Object.entries(params).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        httpParams = httpParams.set(key, String(value));
      }
    });
    return this.http.get<LeadListResponse>(this.base(campanaLeadId), { params: httpParams });
  }

  getById(campanaLeadId: number, id: number): Observable<Lead> {
    return this.http.get<Lead>(`${this.base(campanaLeadId)}/${id}`);
  }

  create(campanaLeadId: number, payload: CreateLeadPayload): Observable<Lead> {
    return this.http.post<Lead>(this.base(campanaLeadId), payload);
  }

  update(campanaLeadId: number, id: number, payload: UpdateLeadPayload): Observable<Lead> {
    return this.http.put<Lead>(`${this.base(campanaLeadId)}/${id}`, payload);
  }

  delete(campanaLeadId: number, id: number): Observable<void> {
    return this.http.delete<void>(`${this.base(campanaLeadId)}/${id}`);
  }

  importarExcel(campanaLeadId: number, archivo: File): Observable<ImportarLeadsResult> {
    const form = new FormData();
    form.append('archivo', archivo);
    return this.http.post<ImportarLeadsResult>(`${this.base(campanaLeadId)}/importar-excel`, form);
  }
}
