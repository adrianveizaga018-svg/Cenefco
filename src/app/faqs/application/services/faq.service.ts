import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Faq, FaqListResponse, FaqListParams } from '../../domain/models/faq.model';

@Injectable({ providedIn: 'root' })
export class FaqService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/faqs';

  getAll(params: FaqListParams = {}): Observable<FaqListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<FaqListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Faq> {
    return this.http.get<Faq>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<Faq>): Observable<Faq> {
    return this.http.post<Faq>(this.baseUrl, data);
  }

  update(id: number, data: Partial<Faq>): Observable<Faq> {
    return this.http.put<Faq>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
