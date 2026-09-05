import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  DocumentoAcademico, DocumentoAcademicoListParams, DocumentoAcademicoListResponse,
  CreateDocumentoPayload, UpdateDocumentoPayload
} from '../../domain/models/documento-academico.model';

@Injectable({ providedIn: 'root' })
export class DocumentoAcademicoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/documentos-academicos';

  getAll(params: DocumentoAcademicoListParams = {}): Observable<DocumentoAcademicoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex    != null) p = p.set('pageIndex',    params.pageIndex);
    if (params.pageSize     != null) p = p.set('pageSize',     params.pageSize);
    if (params.conInactivos != null) p = p.set('conInactivos', params.conInactivos);
    return this.http.get<DocumentoAcademicoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<DocumentoAcademico> {
    return this.http.get<DocumentoAcademico>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateDocumentoPayload): Observable<DocumentoAcademico> {
    return this.http.post<DocumentoAcademico>(this.baseUrl, data);
  }

  update(id: number, data: UpdateDocumentoPayload): Observable<DocumentoAcademico> {
    return this.http.put<DocumentoAcademico>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
