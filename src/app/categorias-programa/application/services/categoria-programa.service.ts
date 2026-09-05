import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import {
  CategoriaCampo,
  CategoriaCampoListResponse,
  CreateCategoriaCampoPayload,
  CategoriaProgramaItem,
  CategoriaProgramaListResponse,
  CategoriaProgramaListParams,
} from '../../domain/models/categoria-programa.model';

@Injectable({ providedIn: 'root' })
export class CategoriaProgramaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/categorias-programa';

  getAll(params: CategoriaProgramaListParams = {}): Observable<CategoriaProgramaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<CategoriaProgramaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<CategoriaProgramaItem> {
    return this.http.get<CategoriaProgramaItem>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<CategoriaProgramaItem>): Observable<CategoriaProgramaItem> {
    return this.http.post<CategoriaProgramaItem>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CategoriaProgramaItem>): Observable<CategoriaProgramaItem> {
    return this.http.put<CategoriaProgramaItem>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  getTiposLegacy(): Observable<{ id_tipoprograma: number; nombre_tipoprograma: string }[]> {
    return this.http.get<{ id_tipoprograma: number; nombre_tipoprograma: string }[]>('/api/v1/tipos-programa/legacy');
  }

  getCampos(categoriaId: number): Observable<CategoriaCampo[]> {
    return this.http
      .get<CategoriaCampoListResponse>(`${this.baseUrl}/${categoriaId}/campos`)
      .pipe(map(r => r.data));
  }

  createCampo(categoriaId: number, payload: CreateCategoriaCampoPayload): Observable<CategoriaCampo> {
    return this.http.post<CategoriaCampo>(`${this.baseUrl}/${categoriaId}/campos`, payload);
  }

  updateCampo(categoriaId: number, campoId: number, payload: Partial<CreateCategoriaCampoPayload>): Observable<CategoriaCampo> {
    return this.http.put<CategoriaCampo>(`${this.baseUrl}/${categoriaId}/campos/${campoId}`, payload);
  }

  deleteCampo(categoriaId: number, campoId: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${categoriaId}/campos/${campoId}`);
  }

  reorderCampos(categoriaId: number, items: { id: number; orden: number }[]): Observable<void> {
    return this.http.post<void>(`${this.baseUrl}/${categoriaId}/campos/reorder`, { items });
  }

  seedCampos(categoriaId: number): Observable<{ insertados: number; data: CategoriaCampo[] }> {
    return this.http.post<{ insertados: number; data: CategoriaCampo[] }>(`${this.baseUrl}/${categoriaId}/campos/seed`, {});
  }
}
