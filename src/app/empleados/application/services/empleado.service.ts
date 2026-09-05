import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  Empleado,
  EmpleadoListResponse,
  CreateEmpleadoPayload,
  UpdateEmpleadoPayload,
} from '../../domain/models/empleado.model';

@Injectable({ providedIn: 'root' })
export class EmpleadoService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/empleados';

  getAll(params: { pageIndex?: number; pageSize?: number; query?: string }): Observable<EmpleadoListResponse> {
    let httpParams = new HttpParams();
    Object.entries(params).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        httpParams = httpParams.set(key, String(value));
      }
    });
    return this.http.get<EmpleadoListResponse>(this.base, { params: httpParams });
  }

  getActivos(): Observable<Empleado[]> {
    return this.http.get<Empleado[]>(`${this.base}/activos`);
  }

  getById(id: number): Observable<Empleado> {
    return this.http.get<Empleado>(`${this.base}/${id}`);
  }

  create(payload: CreateEmpleadoPayload): Observable<Empleado> {
    return this.http.post<Empleado>(this.base, this.toFormData(payload));
  }

  update(id: number, payload: UpdateEmpleadoPayload): Observable<Empleado> {

    const form = this.toFormData(payload);
    form.append('_method', 'PUT');
    return this.http.post<Empleado>(`${this.base}/${id}`, form);
  }

  private toFormData(payload: CreateEmpleadoPayload | UpdateEmpleadoPayload): FormData {
    const form = new FormData();
    Object.entries(payload).forEach(([key, value]) => {
      if (value === null || value === undefined) return;
      if (key === 'carnet' && value instanceof File) {
        form.append('carnet', value);
      } else {
        form.append(key, String(value));
      }
    });
    return form;
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
