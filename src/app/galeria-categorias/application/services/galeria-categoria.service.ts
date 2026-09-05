import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { GaleriaCategoria, GaleriaCategoriaListParams, GaleriaCategoriaListResponse, CreateGaleriaCategoriaPayload } from '../../domain/models/galeria-categoria.model';

@Injectable({ providedIn: 'root' })
export class GaleriaCategoriaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/galeria-categorias';

  getAll(params: GaleriaCategoriaListParams = {}): Observable<GaleriaCategoriaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<GaleriaCategoriaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<GaleriaCategoria> {
    return this.http.get<GaleriaCategoria>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateGaleriaCategoriaPayload): Observable<GaleriaCategoria> {
    return this.http.post<GaleriaCategoria>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateGaleriaCategoriaPayload>): Observable<GaleriaCategoria> {
    return this.http.put<GaleriaCategoria>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
