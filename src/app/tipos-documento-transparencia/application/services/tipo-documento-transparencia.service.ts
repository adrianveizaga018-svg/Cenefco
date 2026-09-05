import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TipoDocumentoTransparencia, TipoDocumentoTransparenciaListResponse, TipoDocumentoTransparenciaListParams } from '../../domain/models/tipo-documento-transparencia.model';

@Injectable({ providedIn: 'root' })
export class TipoDocumentoTransparenciaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/tipos-documento-transparencia';

  getAll(params: TipoDocumentoTransparenciaListParams = {}): Observable<TipoDocumentoTransparenciaListResponse> {
    return this.http.get<TipoDocumentoTransparenciaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<TipoDocumentoTransparencia> {
    return this.http.get<TipoDocumentoTransparencia>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<TipoDocumentoTransparencia>): Observable<TipoDocumentoTransparencia> {
    return this.http.post<TipoDocumentoTransparencia>(this.baseUrl, data);
  }
  update(id: number, data: Partial<TipoDocumentoTransparencia>): Observable<TipoDocumentoTransparencia> {
    return this.http.put<TipoDocumentoTransparencia>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
