import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { EstudianteResena, Resena, ResenaListResponse, ResenaListParams } from '../../domain/models/resena.model';

@Injectable({ providedIn: 'root' })
export class ResenaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/resenas';

  getAll(params: ResenaListParams = {}): Observable<ResenaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex  != null) p = p.set('pageIndex',   params.pageIndex);
    if (params.pageSize   != null) p = p.set('pageSize',    params.pageSize);
    if (params.query)              p = p.set('query',        params.query);
    if (params.estado)             p = p.set('estado',       params.estado);
    if (params.programa_id != null) p = p.set('programa_id', params.programa_id);
    return this.http.get<ResenaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Resena> {
    return this.http.get<Resena>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<Resena>): Observable<Resena> {
    return this.http.post<Resena>(this.baseUrl, data);
  }

  update(id: number, data: Partial<Resena>): Observable<Resena> {
    return this.http.put<Resena>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  getEstudiantesPrograma(programaId: number): Observable<{ data: EstudianteResena[]; total: number }> {
    return this.http.get<{ data: EstudianteResena[]; total: number }>(
      `/api/v1/resenas-programa/${programaId}/estudiantes`
    );
  }
}
