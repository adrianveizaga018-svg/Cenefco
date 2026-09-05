import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { RevistaCientifica, RevistaCientificaListParams, RevistaCientificaListResponse, CreateRevistaCientificaPayload } from '../../domain/models/revista-cientifica.model';

@Injectable({ providedIn: 'root' })
export class RevistaCientificaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/revistas-cientificas';

  getAll(params: RevistaCientificaListParams = {}): Observable<RevistaCientificaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    p = p.set('conInactivos', 'true');
    return this.http.get<RevistaCientificaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<RevistaCientifica> {
    return this.http.get<RevistaCientifica>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateRevistaCientificaPayload): Observable<RevistaCientifica> {
    return this.http.post<RevistaCientifica>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateRevistaCientificaPayload>): Observable<RevistaCientifica> {
    return this.http.put<RevistaCientifica>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
