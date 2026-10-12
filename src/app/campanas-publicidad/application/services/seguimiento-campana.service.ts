import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface RegistroDiario {
  id: number;
  fecha: string;
  nuevos_grupo: number;
  total_grupo: number | null;
  gasto_extra: number;
  flyer_url: string | null;
  nota: string | null;
  registrado_por_nombre: string | null;
}

export interface ResumenCampana {
  campana: { id: number; nombre: string; plataforma: string; estado: string; programa: string | null; responsable: string | null; fecha_inicio: string | null; fecha_fin: string | null };
  periodo: { desde: string; hasta: string };
  facebook: { gasto: number; alcance: number; impresiones: number; clics: number; resultados: number; actualizado: string | null };
  grupo: { nuevos: number; total: number; dias: { fecha: string; nuevos: number; total: number }[] };
  flyers: { fecha: string; url: string }[];
  gastos: { facebook: number; otros: number; total: number };
  crm: { interesados: number; contactados: number; inscritos: number; cobrado: number };
  costos: { por_persona_grupo: number | null; por_inscrito: number | null; retorno: number | null };
}

export interface InformeCampana {
  id: number;
  desde: string;
  hasta: string;
  archivo_url: string;
  created_at: string;
  generado_por: string | null;
  resumen: Pick<ResumenCampana, 'gastos' | 'grupo' | 'crm' | 'costos'>;
}

/** Registro diario de marketing, resumen del periodo e informes en PDF de una campaña. */
@Injectable({ providedIn: 'root' })
export class SeguimientoCampanaService {
  private http = inject(HttpClient);
  private base = '/api/v1/campanas-publicidad';

  ver(id: number, desde?: string, hasta?: string): Observable<{ registros: RegistroDiario[]; resumen: ResumenCampana; informes: InformeCampana[] }> {
    let params = new HttpParams();
    if (desde) params = params.set('desde', desde);
    if (hasta) params = params.set('hasta', hasta);
    return this.http.get<{ registros: RegistroDiario[]; resumen: ResumenCampana; informes: InformeCampana[] }>(`${this.base}/${id}/seguimiento`, { params });
  }

  guardar(id: number, datos: FormData): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${this.base}/${id}/registro-diario`, datos);
  }

  eliminar(idRegistro: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.base}/registro-diario/${idRegistro}`);
  }

  generarInforme(id: number, desde: string, hasta: string): Observable<{ message: string; informe: InformeCampana }> {
    return this.http.post<{ message: string; informe: InformeCampana }>(`${this.base}/${id}/informes`, { desde, hasta });
  }
}
