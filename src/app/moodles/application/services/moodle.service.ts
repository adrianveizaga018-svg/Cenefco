import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  Moodle, MoodleListParams, MoodleListResponse,
  CreateMoodlePayload, UpdateMoodlePayload
} from '../../domain/models/moodle.model';

@Injectable({ providedIn: 'root' })
export class MoodleService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/moodles';

  getAll(params: MoodleListParams = {}): Observable<MoodleListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query     != null && params.query !== '') p = p.set('query', params.query);
    return this.http.get<MoodleListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Moodle> {
    return this.http.get<Moodle>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateMoodlePayload): Observable<Moodle> {
    return this.http.post<Moodle>(this.baseUrl, data);
  }

  update(id: number, data: UpdateMoodlePayload): Observable<Moodle> {
    return this.http.put<Moodle>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
