import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Menu, MenuListParams, MenuListResponse, CreateMenuPayload } from '../../domain/models/menu.model';

@Injectable({ providedIn: 'root' })
export class MenuService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/web-menus';

  getAll(params: MenuListParams = {}): Observable<MenuListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<MenuListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Menu> {
    return this.http.get<Menu>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateMenuPayload): Observable<Menu> {
    return this.http.post<Menu>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateMenuPayload>): Observable<Menu> {
    return this.http.put<Menu>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
