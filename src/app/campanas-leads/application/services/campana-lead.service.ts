import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  CampanaLead,
  CampanaLeadListResponse,
  CreateCampanaLeadPayload,
  UpdateCampanaLeadPayload,
} from '../../domain/models/campana-lead.model';

@Injectable({ providedIn: 'root' })
export class CampanaLeadService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/campanas-leads';

  getAll(params: {
    pageIndex?: number;
    pageSize?: number;
    query?: string;
    sortKey?: string;
    sortOrder?: string;
    estado?: string;
  }): Observable<CampanaLeadListResponse> {
    let httpParams = new HttpParams();
    Object.entries(params).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        httpParams = httpParams.set(key, String(value));
      }
    });
    return this.http.get<CampanaLeadListResponse>(this.base, { params: httpParams });
  }

  getById(id: number): Observable<CampanaLead> {
    return this.http.get<CampanaLead>(`${this.base}/${id}`);
  }

  create(payload: CreateCampanaLeadPayload): Observable<CampanaLead> {
    return this.http.post<CampanaLead>(this.base, payload);
  }

  update(id: number, payload: UpdateCampanaLeadPayload): Observable<CampanaLead> {
    return this.http.put<CampanaLead>(`${this.base}/${id}`, payload);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
