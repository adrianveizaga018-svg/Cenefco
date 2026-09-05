import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, of, switchMap } from 'rxjs';
import { Permiso, PermisoListParams, PermisoListResponse, CreatePermisoPayload } from '../../domain/models/permiso.model';

@Injectable({ providedIn: 'root' })
export class PermisoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/permisos';

  getAll(params: PermisoListParams = {}): Observable<PermisoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<PermisoListResponse>(this.baseUrl, { params: p });
  }

  getAllUnpaged(): Observable<Permiso[]> {
    return this.fetchPagina(1, []);
  }

  private fetchPagina(pageIndex: number, acumulado: Permiso[]): Observable<Permiso[]> {
    return this.getAll({ pageIndex, pageSize: 100 }).pipe(
      switchMap(res => {
        const todos = [...acumulado, ...res.data];
        return todos.length < res.total ? this.fetchPagina(pageIndex + 1, todos) : of(todos);
      }),
    );
  }

  getById(id: number): Observable<Permiso> {
    return this.http.get<Permiso>(`${this.baseUrl}/${id}`);
  }

  create(data: CreatePermisoPayload): Observable<Permiso> {
    return this.http.post<Permiso>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreatePermisoPayload>): Observable<Permiso> {
    return this.http.put<Permiso>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
