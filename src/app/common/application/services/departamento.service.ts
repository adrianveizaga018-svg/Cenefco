import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Departamento {
  id: number;
  nombre: string;
  slug: string;
  activo: boolean;
}

@Injectable({ providedIn: 'root' })
export class DepartamentoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/departamentos';

  getAll(): Observable<{ data: Departamento[]; total: number }> {
    return this.http.get<{ data: Departamento[]; total: number }>(this.baseUrl, {
      params: { pageSize: 50, pageIndex: 1 }
    });
  }
}
