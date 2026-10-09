import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

export type EstadoComprobante = 'por_revisar' | 'aprobados' | 'rechazados' | 'todos';

export interface ComprobanteEstudiante {
  id_pago:             number;
  id_ins:              number;
  id_us:               number;
  monto:               number;
  fecha_deposito:      string;
  metodo_pago:         string;
  nro_boleta:          string | null;
  comprobante_url:     string | null;
  estado_verificacion: string;
  nota_verificacion:   string | null;
  fecha_reg:           string;
  banco:               string | null;
  ci:                  string | null;
  celular:             string | null;
  estudiante:          string;
  programa:            string | null;
  /** Es el primer pago de una inscripción que el estudiante hizo solo desde su panel. */
  es_inscripcion:      boolean;
  saldo_inscripcion:   number;
}

/** Comprobantes que los estudiantes suben desde su panel; Caja los aprueba o rechaza. */
@Injectable({ providedIn: 'root' })
export class ComprobantesCajaService {
  private http = inject(HttpClient);
  private base = '/api/v1/caja/comprobantes';

  listar(estado: EstadoComprobante): Observable<{ data: ComprobanteEstudiante[]; pendientes: number }> {
    return this.http.get<{ data: ComprobanteEstudiante[]; pendientes: number }>(this.base, { params: new HttpParams().set('estado', estado) });
  }

  pendientes(): Observable<{ pendientes: number }> {
    return this.http.get<{ pendientes: number }>(`${this.base}/pendientes-count`);
  }

  aprobar(idPago: number): Observable<{ message: string; saldo: number }> {
    return this.http.patch<{ message: string; saldo: number }>(`${this.base}/${idPago}/aprobar`, {});
  }

  rechazar(idPago: number, motivo: string): Observable<{ message: string }> {
    return this.http.patch<{ message: string }>(`${this.base}/${idPago}/rechazar`, { motivo });
  }
}
