import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  Banner,
  BannerListParams,
  BannerListResponse,
  CreateBannerPayload,
} from '../../domain/models/banner.model';

@Injectable({ providedIn: 'root' })
export class BannerService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/banners-portal';

  getAll(params: BannerListParams = {}): Observable<BannerListResponse> {
    let httpParams = new HttpParams();
    if (params.pageIndex != null) httpParams = httpParams.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) httpParams = httpParams.set('pageSize',  params.pageSize);
    if (params.query)             httpParams = httpParams.set('query',     params.query);
    return this.http.get<BannerListResponse>(this.baseUrl, { params: httpParams });
  }

  getById(id: number): Observable<Banner> {
    return this.http.get<Banner>(`${this.baseUrl}/${id}`);
  }

  getBySlug(slug: string): Observable<Banner> {
    return this.http.get<Banner>(`${this.baseUrl}/slug/${slug}`);
  }

  create(data: CreateBannerPayload): Observable<Banner> {
    return this.http.post<Banner>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateBannerPayload>): Observable<Banner> {
    return this.http.put<Banner>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
