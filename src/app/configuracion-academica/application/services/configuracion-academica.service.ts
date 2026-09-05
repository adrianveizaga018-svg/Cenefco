import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { ConfiguracionAcademica, ConfiguracionAcademicaListParams, ConfiguracionAcademicaListResponse, CreateConfiguracionAcademicaPayload } from '../../domain/models/configuracion-academica.model';

@Injectable({ providedIn: 'root' })
export class ConfiguracionAcademicaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/configuracion-academica';

  getAll(params: ConfiguracionAcademicaListParams = {}): Observable<ConfiguracionAcademicaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.gestion)           p = p.set('gestion',   params.gestion);
    p = p.set('conInactivos', 'true');
    return this.http.get<ConfiguracionAcademicaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<ConfiguracionAcademica> {
    return this.http.get<ConfiguracionAcademica>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateConfiguracionAcademicaPayload): Observable<ConfiguracionAcademica> {
    return this.http.post<ConfiguracionAcademica>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateConfiguracionAcademicaPayload>): Observable<ConfiguracionAcademica> {
    return this.http.put<ConfiguracionAcademica>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
