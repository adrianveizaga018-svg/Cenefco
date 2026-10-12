import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

export type Etapa = 'nuevo' | 'contactado' | 'interesado' | 'por_pagar' | 'inscrito' | 'perdido';
export type Origen = 'manual' | 'excel' | 'portal' | 'contacto' | 'whatsapp' | 'whatsapp_grupo' | 'meta' | 'caja';

export const ETAPAS: { valor: Etapa; etiqueta: string }[] = [
  { valor: 'nuevo',      etiqueta: 'Nuevo' },
  { valor: 'contactado', etiqueta: 'Contactado' },
  { valor: 'interesado', etiqueta: 'Interesado' },
  { valor: 'por_pagar',  etiqueta: 'Por pagar' },
  { valor: 'inscrito',   etiqueta: 'Inscrito' },
  { valor: 'perdido',    etiqueta: 'Perdido' },
];

export const ORIGENES: Record<Origen, string> = {
  manual: 'Carga manual', excel: 'Excel', portal: 'Portal', contacto: 'Solicitar información',
  whatsapp: 'WhatsApp', whatsapp_grupo: 'Grupo de WhatsApp', meta: 'Anuncio', caja: 'Caja',
};

export interface LeadCrm {
  id: number;
  nombre: string;
  celular: string | null;
  correo: string | null;
  ci: string | null;
  estado: Etapa;
  origen: Origen;
  motivo_perdida: string | null;
  vendedor_asignado_id: number | null;
  vendedor: string | null;
  programa_id: number | null;
  programa: string | null;
  id_us: number | null;
  id_ins: number | null;
  created_at: string;
  ultimo_contacto_at: string | null;
  proxima_accion_at: string | null;
  cerrado_at: string | null;
  sin_atender: boolean;
  horas_sin_contacto: number | null;
}

export interface SeguimientoCrm {
  id: number;
  tipo: string;
  resultado: string;
  nota: string | null;
  proxima_accion: string | null;
  fecha_proxima_accion: string | null;
  created_at: string;
  autor: string | null;
}

export interface DetalleLead {
  lead: LeadCrm;
  seguimientos: SeguimientoCrm[];
  inscripciones: { id_ins: number; estado: number; fecha_ins: string; programa: string | null }[];
}

export interface VendedorCrm { id: number; nombre: string; meta_ventas: number | null; }

export interface FilaTablero {
  vendedor_id: number;
  vendedor: string;
  interesados: number;
  contactados: number;
  inscritos: number;
  conversion: number;
  cobrado: number;
  meta: number;
  avance_meta: number | null;
  sin_atender: number;
  primera_respuesta_h: number | null;
  abiertos: number;
}

export interface TableroCrm {
  periodo: { desde: string; hasta: string };
  totales: { interesados: number; inscritos: number; conversion: number; cobrado: number; sin_atender: number; sin_asignar: number; primera_respuesta_h: number | null };
  vendedores: FilaTablero[];
  embudo: Record<Etapa, number>;
  motivos_perdida: { motivo: string | null; total: number }[];
  por_origen: { origen: Origen; interesados: number; inscritos: number }[];
}

export interface TareaCrm extends LeadCrm {
  vencida: boolean;
  proxima_accion: string | null;
  intentos_sin_respuesta: number;
}

export interface ResultadoImportacion {
  nuevos: number;
  ya_estaban: number;
  ya_estudiantes: number;
  errores: string[];
  por_vendedor: { vendedor: string; cantidad: number }[];
}

export interface ContactoCrm {
  tipo: 'llamada' | 'whatsapp' | 'correo' | 'reunion' | 'otro';
  resultado: 'no_contesto' | 'contactado' | 'interesado' | 'no_interesado';
  nota: string | null;
  proxima_accion: string | null;
  proxima_accion_at: string | null;
}

/** Embudo de ventas: el vendedor ve lo suyo; quien supervisa ve todo y reasigna. */
@Injectable({ providedIn: 'root' })
export class CrmService {
  private http = inject(HttpClient);
  private base = '/api/v1/crm';

  leads(filtros: { vendedor_id?: string; origen?: string; q?: string }): Observable<{ data: LeadCrm[] }> {
    let params = new HttpParams();
    for (const [k, v] of Object.entries(filtros)) if (v) params = params.set(k, v);
    return this.http.get<{ data: LeadCrm[] }>(`${this.base}/leads`, { params });
  }

  detalle(id: number): Observable<DetalleLead> {
    return this.http.get<DetalleLead>(`${this.base}/leads/${id}`);
  }

  crear(datos: { nombre: string; celular?: string | null; correo?: string | null; ci?: string | null; nota?: string | null; vendedor_id?: number | null }): Observable<{ message: string; id: number }> {
    return this.http.post<{ message: string; id: number }>(`${this.base}/leads`, datos);
  }

  registrarContacto(id: number, datos: ContactoCrm): Observable<{ message: string; estado: Etapa }> {
    return this.http.post<{ message: string; estado: Etapa }>(`${this.base}/leads/${id}/contacto`, datos);
  }

  moverEtapa(id: number, etapa: Etapa, motivo?: string | null): Observable<{ message: string }> {
    return this.http.patch<{ message: string }>(`${this.base}/leads/${id}/etapa`, { etapa, motivo: motivo ?? null });
  }

  asignar(id: number, vendedorId: number): Observable<{ message: string }> {
    return this.http.patch<{ message: string }>(`${this.base}/leads/${id}/asignar`, { vendedor_id: vendedorId });
  }

  /** El mismo contacto a varios (p. ej. "les envié el flyer"); solo se aplica a los que son del vendedor. */
  contactoMasivo(ids: number[], datos: Pick<ContactoCrm, 'tipo' | 'resultado' | 'nota'>): Observable<{ message: string; registrados: number }> {
    return this.http.post<{ message: string; registrados: number }>(`${this.base}/leads/contacto-masivo`, { ids, ...datos });
  }

  asignarMasivo(ids: number[], vendedorId: number): Observable<{ message: string }> {
    return this.http.patch<{ message: string }>(`${this.base}/leads/asignar-masivo`, { ids, vendedor_id: vendedorId });
  }

  tareas(): Observable<{ data: TareaCrm[]; vencidas: number }> {
    return this.http.get<{ data: TareaCrm[]; vencidas: number }>(`${this.base}/tareas`);
  }

  importar(datos: FormData): Observable<ResultadoImportacion> {
    return this.http.post<ResultadoImportacion>(`${this.base}/importar`, datos);
  }

  plantilla(): Observable<Blob> {
    return this.http.get(`${this.base}/importar/plantilla`, { responseType: 'blob' });
  }

  tablero(desde: string, hasta: string): Observable<TableroCrm> {
    return this.http.get<TableroCrm>(`${this.base}/tablero`, { params: new HttpParams().set('desde', desde).set('hasta', hasta) });
  }

  vendedores(): Observable<VendedorCrm[]> {
    return this.http.get<VendedorCrm[]>(`${this.base}/vendedores`);
  }
}
