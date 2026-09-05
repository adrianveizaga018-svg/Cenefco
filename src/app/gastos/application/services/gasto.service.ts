import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  Gasto,
  GastoListResponse,
  CreateGastoPayload,
  UpdateGastoPayload,
  CategoriaGasto,
  GastoRecurrente,
  CreateGastoRecurrentePayload,
  ResumenMes,
} from '../../domain/models/gasto.model';

@Injectable({ providedIn: 'root' })
export class GastoService {
  private http = inject(HttpClient);
  private readonly base            = '/api/v1/gastos';
  private readonly categoriasBase  = '/api/v1/categorias-gasto';
  private readonly recurrentesBase = '/api/v1/gastos-recurrentes';

  getAll(params: {
    pageIndex?: number;
    pageSize?: number;
    query?: string;
    sortKey?: string;
    sortOrder?: string;
    categoria_gasto_id?: number;
    campana_publicidad_id?: number;
    fecha_desde?: string;
    fecha_hasta?: string;
  }): Observable<GastoListResponse> {
    let httpParams = new HttpParams();
    Object.entries(params).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        httpParams = httpParams.set(key, String(value));
      }
    });
    return this.http.get<GastoListResponse>(this.base, { params: httpParams });
  }

  getById(id: number): Observable<Gasto> {
    return this.http.get<Gasto>(`${this.base}/${id}`);
  }

  create(payload: CreateGastoPayload): Observable<Gasto> {
    return this.http.post<Gasto>(this.base, this.toFormData(payload));
  }

  update(id: number, payload: UpdateGastoPayload): Observable<Gasto> {

    const form = this.toFormData(payload);
    form.append('_method', 'PUT');
    return this.http.post<Gasto>(`${this.base}/${id}`, form);
  }

  private toFormData(payload: CreateGastoPayload | UpdateGastoPayload): FormData {
    const form = new FormData();
    Object.entries(payload).forEach(([key, value]) => {
      if (value === null || value === undefined) return;
      if (key === 'comprobante' && value instanceof File) {
        form.append('comprobante', value);
      } else {
        form.append(key, String(value));
      }
    });
    return form;
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  getResumenMes(anio: number, mes: number): Observable<ResumenMes> {
    return this.http.get<ResumenMes>(`${this.base}/resumen-mes`, { params: { anio, mes } as any });
  }

  getCategorias(): Observable<CategoriaGasto[]> {
    return this.http.get<CategoriaGasto[]>(this.categoriasBase);
  }

  createCategoria(payload: { nombre: string; linea_negocio?: string | null; activo?: boolean }): Observable<CategoriaGasto> {
    return this.http.post<CategoriaGasto>(this.categoriasBase, payload);
  }

  getRecurrentes(): Observable<GastoRecurrente[]> {
    return this.http.get<GastoRecurrente[]>(this.recurrentesBase);
  }

  createRecurrente(payload: CreateGastoRecurrentePayload): Observable<GastoRecurrente> {
    return this.http.post<GastoRecurrente>(this.recurrentesBase, payload);
  }

  confirmarRecurrente(id: number, fecha: string, comprobante?: File | null): Observable<Gasto> {

    const form = new FormData();
    form.append('fecha', fecha);
    form.append('_method', 'PATCH');
    if (comprobante) form.append('comprobante', comprobante);
    return this.http.post<Gasto>(`${this.recurrentesBase}/${id}/confirmar`, form);
  }
}
