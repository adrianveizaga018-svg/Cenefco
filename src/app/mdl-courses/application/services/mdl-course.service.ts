import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  MdlCourse, MdlCourseListParams, MdlCourseListResponse,
  CreateMdlCoursePayload, UpdateMdlCoursePayload
} from '../../domain/models/mdl-course.model';

@Injectable({ providedIn: 'root' })
export class MdlCourseService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/mdl-courses';

  getAll(params: MdlCourseListParams = {}): Observable<MdlCourseListResponse> {
    let p = new HttpParams();
    if (params.pageIndex  != null) p = p.set('pageIndex',  params.pageIndex);
    if (params.pageSize   != null) p = p.set('pageSize',   params.pageSize);
    if (params.id_docente != null) p = p.set('id_docente', params.id_docente);
    return this.http.get<MdlCourseListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<MdlCourse> {
    return this.http.get<MdlCourse>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateMdlCoursePayload): Observable<MdlCourse> {
    return this.http.post<MdlCourse>(this.baseUrl, data);
  }

  update(id: number, data: UpdateMdlCoursePayload): Observable<MdlCourse> {
    return this.http.put<MdlCourse>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
