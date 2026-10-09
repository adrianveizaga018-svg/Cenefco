import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { DevolucionesResponse, EstadoDevolucion } from '../../domain/models/devolucion.model';

@Injectable({ providedIn: 'root' })
export class DevolucionPanelService {
  private http = inject(HttpClient);

  listar(estado: EstadoDevolucion | 'todas'): Observable<DevolucionesResponse> {
    return this.http.get<DevolucionesResponse>('/api/v1/devoluciones', { params: new HttpParams().set('estado', estado) });
  }

  resolver(id: number, estado: 'aprobada' | 'rechazada', nota: string | null): Observable<unknown> {
    return this.http.patch(`/api/v1/devoluciones/${id}/resolver`, { estado, nota_respuesta: nota });
  }
}
