import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { RedSocial, RedSocialListResponse, RedSocialListParams } from '../../domain/models/red-social.model';

@Injectable({ providedIn: 'root' })
export class RedSocialService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/redes-sociales';

  getAll(params: RedSocialListParams = {}): Observable<RedSocialListResponse> {
    return this.http.get<RedSocialListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }

  getById(id: number): Observable<RedSocial> {
    return this.http.get<RedSocial>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<RedSocial>): Observable<RedSocial> {
    return this.http.post<RedSocial>(this.baseUrl, data);
  }

  update(id: number, data: Partial<RedSocial>): Observable<RedSocial> {
    return this.http.put<RedSocial>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
