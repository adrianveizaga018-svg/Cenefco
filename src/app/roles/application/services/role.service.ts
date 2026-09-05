import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of, switchMap } from 'rxjs';
import { Permiso, Role, RolePayload } from '../../domain/models/role.model';

@Injectable({ providedIn: 'root' })
export class RoleService {
  private http = inject(HttpClient);

  getAll(): Observable<Role[]> {
    return this.http.get<Role[]>('/api/v1/roles');
  }

  getPermisos(): Observable<Permiso[]> {
    return this.fetchPermisosPagina(1, []);
  }

  private fetchPermisosPagina(pageIndex: number, acumulado: Permiso[]): Observable<Permiso[]> {
    return this.http
      .get<{ data: Permiso[]; total: number }>('/api/v1/permisos', { params: { pageIndex, pageSize: 100 } })
      .pipe(
        switchMap(res => {
          const todos = [...acumulado, ...res.data];
          return todos.length < res.total ? this.fetchPermisosPagina(pageIndex + 1, todos) : of(todos);
        }),
      );
  }

  getById(id: number): Observable<Role> {
    return this.http.get<Role>(`/api/v1/roles/${id}`);
  }

  create(data: RolePayload): Observable<Role> {
    return this.http.post<Role>('/api/v1/roles', data);
  }

  update(id: number, data: RolePayload): Observable<Role> {
    return this.http.put<Role>(`/api/v1/roles/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`/api/v1/roles/${id}`);
  }
}
