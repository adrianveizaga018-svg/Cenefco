import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Convenio, ConvenioDetalle, ConvenioListParams, ConvenioListResponse, ConvenioOption, CreateConvenioPayload } from '../../domain/models/convenio.model';

type CreatePayload = CreateConvenioPayload & { logo_file?: File | null; documento_file?: File | null };
type UpdatePayload = Partial<CreateConvenioPayload> & { logo_file?: File | null; documento_file?: File | null; quitar_logo?: string; quitar_documento?: string };

@Injectable({ providedIn: 'root' })
export class ConvenioService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/convenios';

  getAll(params: ConvenioListParams = {}): Observable<ConvenioListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)              p = p.set('query',     params.query);
    if (params.estado)             p = p.set('estado',    params.estado);
    return this.http.get<ConvenioListResponse>(this.baseUrl, { params: p });
  }

  getAll$(): Observable<ConvenioOption[]> {
    return this.http.get<ConvenioOption[]>(`${this.baseUrl}/all`);
  }

  getById(id: number): Observable<ConvenioDetalle> {
    return this.http.get<ConvenioDetalle>(`${this.baseUrl}/${id}`);
  }

  create(data: CreatePayload): Observable<Convenio> {
    const fd = new FormData();
    Object.entries(data).forEach(([k, v]) => {
      if (k === 'logo_file' || k === 'documento_file') return;
      if (v != null && v !== '') fd.append(k, String(v));
    });
    if (data.logo_file)      fd.append('logo',      data.logo_file);
    if (data.documento_file) fd.append('documento', data.documento_file);
    return this.http.post<Convenio>(this.baseUrl, fd);
  }

  update(id: number, data: UpdatePayload): Observable<Convenio> {
    const fd = new FormData();
    fd.append('_method', 'PUT');
    Object.entries(data).forEach(([k, v]) => {
      if (k === 'logo_file' || k === 'documento_file') return;
      if (v != null && v !== '') fd.append(k, String(v));
    });
    if (data.logo_file)        fd.append('logo',             data.logo_file);
    if (data.documento_file)   fd.append('documento',        data.documento_file);
    if (data.quitar_logo)      fd.append('quitar_logo',      data.quitar_logo);
    if (data.quitar_documento) fd.append('quitar_documento', data.quitar_documento);
    return this.http.post<Convenio>(`${this.baseUrl}/${id}`, fd);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
