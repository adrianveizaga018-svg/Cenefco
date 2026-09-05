import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  CertPlantilla, CertPlantillaListResponse,
  CertCampo, CertCampoListResponse,
  ListaAprobadoListResponse,
  CertificadoListResponse, GenerarLoteResult,
  ImportarParticipantesExcelResult,
  CursoCertificadoExternoListResponse,
  RegistrarEstudianteExternoResult,
} from '../../domain/models/certificado.model';

@Injectable({ providedIn: 'root' })
export class CertificadoService {
  private http = inject(HttpClient);

  getPlantillas(params: { pageIndex?: number; pageSize?: number; soloActivos?: boolean } = {}): Observable<CertPlantillaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex  != null) p = p.set('pageIndex',   params.pageIndex);
    if (params.pageSize   != null) p = p.set('pageSize',    params.pageSize);
    if (params.soloActivos)        p = p.set('soloActivos', 'true');
    return this.http.get<CertPlantillaListResponse>('/api/v1/cert-plantillas', { params: p });
  }

  getPlantillaById(id: number): Observable<CertPlantilla> {
    return this.http.get<CertPlantilla>(`/api/v1/cert-plantillas/${id}`);
  }

  createPlantilla(data: Partial<CertPlantilla>): Observable<CertPlantilla> {
    return this.http.post<CertPlantilla>('/api/v1/cert-plantillas', data);
  }

  updatePlantilla(id: number, data: Partial<CertPlantilla>): Observable<CertPlantilla> {
    return this.http.put<CertPlantilla>(`/api/v1/cert-plantillas/${id}`, data);
  }

  deletePlantilla(id: number): Observable<void> {
    return this.http.delete<void>(`/api/v1/cert-plantillas/${id}`);
  }

  previewPlantilla(id: number, format: 'jpg' | 'pdf'): Observable<Blob> {
    return this.http.get(`/api/v1/cert-plantillas/${id}/preview`, {
      params: { format },
      responseType: 'blob',
    });
  }

  uploadImagenPlantilla(file: File): Observable<{ url: string }> {
    const fd = new FormData();
    fd.append('file', file);
    return this.http.post<{ url: string }>('/api/v1/upload/image', fd);
  }

  getCampos(plantillaId: number): Observable<CertCampoListResponse> {
    return this.http.get<CertCampoListResponse>('/api/v1/cert-plantilla-campos', {
      params: new HttpParams().set('plantilla_id', plantillaId).set('pageSize', 100),
    });
  }

  createCampo(data: Partial<CertCampo>): Observable<CertCampo> {
    return this.http.post<CertCampo>('/api/v1/cert-plantilla-campos', data);
  }

  updateCampo(id: number, data: Partial<CertCampo>): Observable<CertCampo> {
    return this.http.put<CertCampo>(`/api/v1/cert-plantilla-campos/${id}`, data);
  }

  deleteCampo(id: number): Observable<void> {
    return this.http.delete<void>(`/api/v1/cert-plantilla-campos/${id}`);
  }

  getAprobados(params: { pageIndex?: number; pageSize?: number; imparte_id?: number; query?: string } = {}): Observable<ListaAprobadoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex  != null) p = p.set('pageIndex',  params.pageIndex);
    if (params.pageSize   != null) p = p.set('pageSize',   params.pageSize);
    if (params.imparte_id != null) p = p.set('imparte_id', params.imparte_id);
    if (params.query)              p = p.set('query',      params.query);
    return this.http.get<ListaAprobadoListResponse>('/api/v1/lista-aprobados', { params: p });
  }

  createAprobado(data: any): Observable<any> {
    return this.http.post('/api/v1/lista-aprobados', data);
  }

  updateAprobado(id: number, data: {
    condicion?: string;
    nota_final?: number | null;
    observacion?: string | null;
    comprobante_url?: string | null;
  }): Observable<any> {
    return this.http.put(`/api/v1/lista-aprobados/${id}`, data);
  }

  updateEstudianteNombre(usuarioId: number, data: {
    nombre?: string;
    appaterno?: string;
    apmaterno?: string;
  }): Observable<any> {
    return this.http.put(`/api/v1/usuarios-academicos/${usuarioId}`, data);
  }

  deleteAprobado(id: number): Observable<void> {
    return this.http.delete<void>(`/api/v1/lista-aprobados/${id}`);
  }

  deleteAprobadosBulk(ids: number[]): Observable<{ eliminados: number }> {
    return this.http.delete<{ eliminados: number }>('/api/v1/lista-aprobados/bulk', { body: { ids } });
  }

  registrarParticipanteLibre(data: {
    imparte_id: number;
    nombre: string;
    appaterno?: string;
    apmaterno?: string;
    ci?: string;
    email?: string;
    condicion?: string;
    nota_final?: number | null;
    observacion?: string;
    comprobante_url?: string;
    solo_nombre_completo?: boolean;
  }): Observable<any> {
    return this.http.post('/api/v1/participantes/registrar', data);
  }

  importarParticipantesExcel(imparteId: number, archivo: File, soloNombreCompleto = false): Observable<ImportarParticipantesExcelResult> {
    const form = new FormData();
    form.append('imparte_id', String(imparteId));
    form.append('archivo', archivo);
    form.append('solo_nombre_completo', soloNombreCompleto ? '1' : '0');
    return this.http.post<ImportarParticipantesExcelResult>('/api/v1/participantes/importar-excel', form);
  }

  listarCursosCertificadoExterno(query?: string): Observable<CursoCertificadoExternoListResponse> {
    let p = new HttpParams();
    if (query) p = p.set('q', query);
    return this.http.get<CursoCertificadoExternoListResponse>('/api/v1/certificados-externos/cursos', { params: p });
  }

  registrarEstudiantesCertificadoExterno(cursoId: number, nombres: string[]): Observable<RegistrarEstudianteExternoResult> {
    return this.http.post<RegistrarEstudianteExternoResult>(`/api/v1/certificados-externos/cursos/${cursoId}/estudiantes`, { nombres });
  }

  getCertificados(params: { pageIndex?: number; pageSize?: number; imparte_id?: number } = {}): Observable<CertificadoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex  != null) p = p.set('pageIndex',  params.pageIndex);
    if (params.pageSize   != null) p = p.set('pageSize',   params.pageSize);
    if (params.imparte_id != null) p = p.set('imparte_id', params.imparte_id);
    return this.http.get<CertificadoListResponse>('/api/v1/certificados', { params: p });
  }

  generarLote(imparteId: number, plantillaId: number): Observable<GenerarLoteResult> {
    return this.http.post<GenerarLoteResult>('/api/v1/certificados/generar-lote', {
      imparte_id: imparteId,
      plantilla_id: plantillaId,
    });
  }

  previewPagosCompletos(programaId: number): Observable<{ id_imp: number; total: number; estudiantes: { id_us: number; id_ins: number; nombre: string }[] }> {
    return this.http.get<any>('/api/v1/certificados/preview-pagos-completos', {
      params: new HttpParams().set('programa_id', programaId),
    });
  }

  generarPagosCompletos(programaId: number, plantillaId: number): Observable<GenerarLoteResult> {
    return this.http.post<GenerarLoteResult>('/api/v1/certificados/generar-pagos-completos', {
      programa_id:  programaId,
      plantilla_id: plantillaId,
    });
  }

  descargarZip(imparteId: number): Observable<Blob> {
    return this.http.get('/api/v1/certificados/descargar-zip', {
      params: new HttpParams().set('imparte_id', imparteId),
      responseType: 'blob',
    });
  }

  deleteCertificado(id: number): Observable<void> {
    return this.http.delete<void>(`/api/v1/certificados/${id}`);
  }
}
