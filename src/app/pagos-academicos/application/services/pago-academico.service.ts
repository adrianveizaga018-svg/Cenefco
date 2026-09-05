import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { PagoAcademico, PagoAcademicoListParams, PagoAcademicoListResponse, CreatePagoAcademicoPayload } from '../../domain/models/pago-academico.model';

@Injectable({ providedIn: 'root' })
export class PagoAcademicoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/pagos-academicos';
  getAll(params: PagoAcademicoListParams = {}): Observable<PagoAcademicoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    p = p.set('conInactivos', 'true');
    return this.http.get<PagoAcademicoListResponse>(this.baseUrl, { params: p });
  }
  getById(id: number): Observable<PagoAcademico> { return this.http.get<PagoAcademico>(`${this.baseUrl}/${id}`); }
  create(data: CreatePagoAcademicoPayload): Observable<PagoAcademico> { return this.http.post<PagoAcademico>(this.baseUrl, data); }

  update(id: number, data: Partial<CreatePagoAcademicoPayload>, comprobanteArchivo?: File | null): Observable<PagoAcademico> {
    if (!comprobanteArchivo) {
      return this.http.put<PagoAcademico>(`${this.baseUrl}/${id}`, data);
    }




    const fd = new FormData();
    Object.entries(data).forEach(([k, v]) => {
      if (v != null) fd.append(k, String(v));
    });
    fd.append('comprobante_archivo', comprobanteArchivo);
    fd.append('_method', 'PUT');
    return this.http.post<PagoAcademico>(`${this.baseUrl}/${id}`, fd);
  }

  delete(id: number): Observable<void> { return this.http.delete<void>(`${this.baseUrl}/${id}`); }
}
