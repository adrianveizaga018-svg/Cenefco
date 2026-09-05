import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { ConfigSitioResponse } from '../../domain/models/config-sitio.model';

@Injectable({ providedIn: 'root' })
export class ConfigSitioService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/configuracion-sitio';

  getAll(): Observable<ConfigSitioResponse> {
    return this.http.get<ConfigSitioResponse>(this.baseUrl);
  }

  update(items: { clave: string; valor: string | null }[]): Observable<{ message: string }> {
    return this.http.put<{ message: string }>(this.baseUrl, { items });
  }
}
