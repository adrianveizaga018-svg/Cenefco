import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  MdlUser, MdlUserListParams, MdlUserListResponse,
  CreateMdlUserPayload, UpdateMdlUserPayload
} from '../../domain/models/mdl-user.model';

@Injectable({ providedIn: 'root' })
export class MdlUserService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/mdl-users';

  getAll(params: MdlUserListParams = {}): Observable<MdlUserListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query     != null) p = p.set('query',     params.query);
    return this.http.get<MdlUserListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<MdlUser> {
    return this.http.get<MdlUser>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateMdlUserPayload): Observable<MdlUser> {
    return this.http.post<MdlUser>(this.baseUrl, data);
  }

  update(id: number, data: UpdateMdlUserPayload): Observable<MdlUser> {
    return this.http.put<MdlUser>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
