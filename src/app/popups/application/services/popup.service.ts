import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Popup, PopupListParams, PopupListResponse, CreatePopupPayload } from '../../domain/models/popup.model';

@Injectable({ providedIn: 'root' })
export class PopupService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/popups';

  getAll(params: PopupListParams = {}): Observable<PopupListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    return this.http.get<PopupListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Popup> {
    return this.http.get<Popup>(`${this.baseUrl}/${id}`);
  }

  create(data: CreatePopupPayload): Observable<Popup> {
    return this.http.post<Popup>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreatePopupPayload>): Observable<Popup> {
    return this.http.put<Popup>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
