import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { MenuItem, MenuItemListParams, MenuItemListResponse, CreateMenuItemPayload } from '../../domain/models/menu-item.model';

@Injectable({ providedIn: 'root' })
export class MenuItemService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/web-menu-items';

  getAll(params: MenuItemListParams = {}): Observable<MenuItemListResponse> {
    let p = new HttpParams();
    if (params.menu_id   != null) p = p.set('menu_id',   params.menu_id);
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<MenuItemListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<MenuItem> {
    return this.http.get<MenuItem>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateMenuItemPayload): Observable<MenuItem> {
    return this.http.post<MenuItem>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateMenuItemPayload>): Observable<MenuItem> {
    return this.http.put<MenuItem>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
