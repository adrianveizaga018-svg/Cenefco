import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { WhatsappGrupo, WhatsappGrupoListParams, WhatsappGrupoListResponse, CreateWhatsappGrupoPayload } from '../../domain/models/whatsapp-grupo.model';

@Injectable({ providedIn: 'root' })
export class WhatsappGrupoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/whatsapp-grupos';

  getAll(params: WhatsappGrupoListParams = {}): Observable<WhatsappGrupoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    return this.http.get<WhatsappGrupoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<WhatsappGrupo> {
    return this.http.get<WhatsappGrupo>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateWhatsappGrupoPayload): Observable<WhatsappGrupo> {
    return this.http.post<WhatsappGrupo>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateWhatsappGrupoPayload>): Observable<WhatsappGrupo> {
    return this.http.put<WhatsappGrupo>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
