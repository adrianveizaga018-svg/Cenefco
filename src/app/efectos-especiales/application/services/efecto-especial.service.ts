import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { EfectoEspecial, EfectoEspecialListParams, EfectoEspecialListResponse } from '../../domain/models/efecto-especial.model';

@Injectable({ providedIn: 'root' })
export class EfectoEspecialService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/efectos-especiales';

  getAll(p: EfectoEspecialListParams = {}): Observable<EfectoEspecialListResponse> {
    let params = new HttpParams();
    if (p.pageIndex != null) params = params.set('pageIndex', p.pageIndex);
    if (p.pageSize  != null) params = params.set('pageSize',  p.pageSize);
    if (p.query)              params = params.set('query',    p.query);
    return this.http.get<EfectoEspecialListResponse>(this.baseUrl, { params });
  }

  getById(id: number): Observable<EfectoEspecial> {
    return this.http.get<EfectoEspecial>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<EfectoEspecial>): Observable<EfectoEspecial> {
    return this.http.post<EfectoEspecial>(this.baseUrl, data);
  }

  update(id: number, data: Partial<EfectoEspecial>): Observable<EfectoEspecial> {
    return this.http.put<EfectoEspecial>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
