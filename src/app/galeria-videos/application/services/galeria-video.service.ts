import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { GaleriaVideo, GaleriaVideoListParams, GaleriaVideoListResponse, CreateGaleriaVideoPayload } from '../../domain/models/galeria-video.model';

@Injectable({ providedIn: 'root' })
export class GaleriaVideoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/galeria-videos';

  getAll(params: GaleriaVideoListParams = {}): Observable<GaleriaVideoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<GaleriaVideoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<GaleriaVideo> {
    return this.http.get<GaleriaVideo>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateGaleriaVideoPayload): Observable<GaleriaVideo> {
    return this.http.post<GaleriaVideo>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateGaleriaVideoPayload>): Observable<GaleriaVideo> {
    return this.http.put<GaleriaVideo>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
