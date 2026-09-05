import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TipoBanco, TipoBancoListParams, TipoBancoListResponse, CreateTipoBancoPayload } from '../../domain/models/tipo-banco.model';

@Injectable({ providedIn: 'root' })
export class TipoBancoService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/tipos-banco';

  getAll(params: TipoBancoListParams = {}): Observable<TipoBancoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<TipoBancoListResponse>(this.base, { params: p });
  }

  getActivos(): Observable<TipoBanco[]> {
    return this.http.get<TipoBanco[]>(`${this.base}/activos`);
  }

  getById(id: number): Observable<TipoBanco> {
    return this.http.get<TipoBanco>(`${this.base}/${id}`);
  }

  create(data: CreateTipoBancoPayload): Observable<TipoBanco> {
    return this.http.post<TipoBanco>(this.base, data);
  }

  update(id: number, data: Partial<CreateTipoBancoPayload>): Observable<TipoBanco> {
    return this.http.put<TipoBanco>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
