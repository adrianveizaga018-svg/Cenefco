import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Nivel, NivelListParams, NivelListResponse, CreateNivelPayload } from '../../domain/models/nivel.model';

@Injectable({ providedIn: 'root' })
export class NivelService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/catalogo-academico/niveles';

  getAll(params: NivelListParams = {}): Observable<NivelListResponse> {
    let p = new HttpParams().set('conInactivos', 'true');
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<NivelListResponse>(this.base, { params: p });
  }

  getById(id: number): Observable<Nivel> {
    return this.http.get<Nivel>(`${this.base}/${id}`);
  }

  create(data: CreateNivelPayload): Observable<Nivel> {
    return this.http.post<Nivel>(this.base, data);
  }

  update(id: number, data: Partial<CreateNivelPayload>): Observable<Nivel> {
    return this.http.put<Nivel>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
