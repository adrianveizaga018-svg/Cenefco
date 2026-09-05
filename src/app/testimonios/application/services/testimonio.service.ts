import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Testimonio, TestimonioListParams, TestimonioListResponse, CreateTestimonioPayload } from '../../domain/models/testimonio.model';

@Injectable({ providedIn: 'root' })
export class TestimonioService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/testimonios';

  getAll(params: TestimonioListParams = {}): Observable<TestimonioListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<TestimonioListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Testimonio> {
    return this.http.get<Testimonio>(`${this.baseUrl}/${id}`);
  }

  getBySlug(slug: string): Observable<Testimonio> {
    return this.http.get<Testimonio>(`${this.baseUrl}/slug/${slug}`);
  }

  create(data: CreateTestimonioPayload): Observable<Testimonio> {
    return this.http.post<Testimonio>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateTestimonioPayload>): Observable<Testimonio> {
    return this.http.put<Testimonio>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
