import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { ComisionLiquidacionDetalle } from '../../domain/models/comision.model';

export interface RangoComision { desde: number; hasta: number | null; monto: number; }

export interface ProductoComision {
  id: number;
  nombre: string;
  /** rangos: monto fijo según el precio · negociar: el monto se acuerda venta por venta */
  modalidad: 'rangos' | 'negociar';
  porcentaje_minimo_pago: number;
  activo: boolean;
  rangos: RangoComision[];
}

export interface ProgramaComision { id_programa: number; nombre: string; producto_id: number | null; precio: number | null; }

export interface FilaRanking {
  vendedor_id: number;
  nombre: string;
  foto: string | null;
  ventas: number;
  recaudado: number;
  meta: number | null;
  progreso_meta: number | null;
  puesto: number | null;
  es_yo: boolean;
}

export interface PodioMes { periodo: string; podio: { puesto: number; vendedor_id: number; nombre: string; recaudado: number; ventas: number }[]; }

export interface RankingVentas { periodo: string; cerrado: boolean; ranking: FilaRanking[]; total: number; historial: PodioMes[]; }

/** Tabla de comisiones (Administración/Contabilidad), ventas con su comisión y ranking de vendedores. */
@Injectable({ providedIn: 'root' })
export class TablaComisionesService {
  private http = inject(HttpClient);

  tabla(): Observable<{ productos: ProductoComision[]; programas: ProgramaComision[] }> {
    return this.http.get<{ productos: ProductoComision[]; programas: ProgramaComision[] }>('/api/v1/comisiones-tabla');
  }

  guardarProducto(producto: Omit<ProductoComision, 'id'> & { id?: number }): Observable<{ id: number }> {
    return producto.id
      ? this.http.put<{ id: number }>(`/api/v1/comisiones-tabla/productos/${producto.id}`, producto)
      : this.http.post<{ id: number }>('/api/v1/comisiones-tabla/productos', producto);
  }

  asignarProgramas(asignaciones: { id_programa: number; producto_id: number | null }[]): Observable<{ actualizados: number }> {
    return this.http.patch<{ actualizados: number }>('/api/v1/comisiones-tabla/programas', { asignaciones });
  }

  ventas(desde: string, hasta: string, vendedorId?: number | null): Observable<{ data: ComisionLiquidacionDetalle[] }> {
    let params = new HttpParams().set('desde', desde).set('hasta', hasta);
    if (vendedorId) params = params.set('vendedor_id', vendedorId);
    return this.http.get<{ data: ComisionLiquidacionDetalle[] }>('/api/v1/comisiones-ventas', { params });
  }

  acordar(datos: { id_ins: number; monto: number | null; vendedor_id: number | null; motivo: string }): Observable<{ message: string }> {
    return this.http.post<{ message: string }>('/api/v1/comisiones-acuerdos', datos);
  }

  quitarAcuerdo(idIns: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`/api/v1/comisiones-acuerdos/${idIns}`);
  }

  ranking(mes: string): Observable<RankingVentas> {
    return this.http.get<RankingVentas>('/api/v1/ventas-ranking', { params: { mes } });
  }
}
