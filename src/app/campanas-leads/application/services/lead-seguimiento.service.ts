import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  LeadSeguimiento,
  CreateSeguimientoPayload,
  ActualizarEstadoLeadPayload,
  Lead,
} from '../../domain/models/campana-lead.model';

@Injectable({ providedIn: 'root' })
export class LeadSeguimientoService {
  private http = inject(HttpClient);

  private baseUrl(campanaLeadId: number, leadId: number): string {
    return '/api/v1/campanas-leads/' + campanaLeadId + '/leads/' + leadId + '/seguimientos';
  }

  getSeguimientos(campanaLeadId: number, leadId: number): Observable<LeadSeguimiento[]> {
    return this.http.get<LeadSeguimiento[]>(this.baseUrl(campanaLeadId, leadId));
  }

  crearSeguimiento(campanaLeadId: number, leadId: number, payload: CreateSeguimientoPayload): Observable<LeadSeguimiento> {
    return this.http.post<LeadSeguimiento>(this.baseUrl(campanaLeadId, leadId), payload);
  }

  actualizarEstado(campanaLeadId: number, leadId: number, payload: ActualizarEstadoLeadPayload): Observable<Lead> {
    return this.http.patch<Lead>('/api/v1/campanas-leads/' + campanaLeadId + '/leads/' + leadId + '/estado', payload);
  }
}

