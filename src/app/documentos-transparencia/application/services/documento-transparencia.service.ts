import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { DocumentoTransparencia, DocumentoTransparenciaListResponse, DocumentoTransparenciaListParams } from '../../domain/models/documento-transparencia.model';

@Injectable({ providedIn: 'root' })
export class DocumentoTransparenciaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/documentos-transparencia';

  getAll(params: DocumentoTransparenciaListParams = {}): Observable<DocumentoTransparenciaListResponse> {
    return this.http.get<DocumentoTransparenciaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<DocumentoTransparencia> {
    return this.http.get<DocumentoTransparencia>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<DocumentoTransparencia>): Observable<DocumentoTransparencia> {
    return this.http.post<DocumentoTransparencia>(this.baseUrl, data);
  }
  update(id: number, data: Partial<DocumentoTransparencia>): Observable<DocumentoTransparencia> {
    return this.http.put<DocumentoTransparencia>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
