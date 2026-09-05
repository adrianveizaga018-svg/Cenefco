import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { EnvioCertificado, CreateEnvioCertificadoPayload } from '../../domain/models/envio-certificado.model';

@Injectable({ providedIn: 'root' })
export class EnvioCertificadoService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/envios-certificado';

  getByInscripcion(idIns: number): Observable<EnvioCertificado[]> {
    return this.http.get<EnvioCertificado[]>(this.base, { params: { id_ins: idIns } });
  }

  create(payload: CreateEnvioCertificadoPayload): Observable<EnvioCertificado> {
    const fd = new FormData();
    fd.append('id_ins', String(payload.id_ins));
    fd.append('ciudad_destino', payload.ciudad_destino);
    fd.append('fecha_envio', payload.fecha_envio);
    fd.append('imagen_guia', payload.imagen_guia);
    if (payload.aclaraciones) fd.append('aclaraciones', payload.aclaraciones);
    if (payload.costo != null) fd.append('costo', String(payload.costo));
    return this.http.post<EnvioCertificado>(this.base, fd);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
