import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  ConfigHonorario,
  UpsertConfigHonorarioPayload,
  DocenteHonorarioSugerido,
} from '../../domain/models/honorario.model';

@Injectable({ providedIn: 'root' })
export class HonorarioService {
  private http = inject(HttpClient);
  private readonly configBase    = '/api/v1/config-honorarios';
  private readonly sugeridosBase = '/api/v1/honorarios-sugeridos';

  getConfiguraciones(): Observable<ConfigHonorario[]> {
    return this.http.get<ConfigHonorario[]>(this.configBase);
  }

  upsertConfiguracion(payload: UpsertConfigHonorarioPayload): Observable<ConfigHonorario> {
    return this.http.post<ConfigHonorario>(this.configBase, payload);
  }

  eliminarConfiguracion(idPrograma: number): Observable<void> {
    return this.http.delete<void>(`${this.configBase}/${idPrograma}`);
  }

  getSugeridosDelMes(anio: number, mes: number): Observable<DocenteHonorarioSugerido[]> {
    return this.http.get<DocenteHonorarioSugerido[]>(this.sugeridosBase, { params: { anio, mes } as any });
  }
}
