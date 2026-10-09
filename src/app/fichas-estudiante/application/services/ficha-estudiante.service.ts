import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { EstudianteResumen, FichaEstudiante, OpcionesCertificado } from '../../domain/models/ficha-estudiante.model';

@Injectable({ providedIn: 'root' })
export class FichaEstudianteService {
  private http = inject(HttpClient);

  /** Por carnet (exacto o parcial) o por nombre/apellido. */
  buscar(texto: string): Observable<EstudianteResumen[]> {
    return this.http.get<EstudianteResumen[]>('/api/v1/estudiantes/buscar', { params: new HttpParams().set('q', texto) });
  }

  getFicha(idUs: number): Observable<FichaEstudiante> {
    return this.http.get<FichaEstudiante>(`/api/v1/estudiantes/${idUs}/ficha`);
  }

  /** Plantillas asignadas al programa y si se puede emitir (pago completo, etc.). */
  getOpcionesCertificado(idIns: number): Observable<OpcionesCertificado> {
    return this.http.get<OpcionesCertificado>(`/api/v1/inscripciones/${idIns}/certificado-opciones`);
  }

  emitirCertificado(idIns: number, plantillaId: number): Observable<{ message: string; certificado: { codigo_verificacion: string; archivo_url: string | null } | null }> {
    return this.http.post<{ message: string; certificado: { codigo_verificacion: string; archivo_url: string | null } | null }>(
      `/api/v1/inscripciones/${idIns}/emitir-certificado`, { plantilla_id: plantillaId },
    );
  }
}
