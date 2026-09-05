import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { DashboardGastos } from '../../domain/models/dashboard-gastos.model';

@Injectable({ providedIn: 'root' })
export class DashboardGastosService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/dashboard-gastos';

  getDashboard(anio: number, mes: number): Observable<DashboardGastos> {
    return this.http.get<DashboardGastos>(this.base, { params: { anio, mes } as any });
  }
}
