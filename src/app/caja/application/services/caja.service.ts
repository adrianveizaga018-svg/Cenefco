import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface CajaEstudiante {
  id_us: number;
  nombre: string;
  apellido_paterno: string;
  apellido_materno: string;
  ci: string;
  expedido: number | null;
  email: string | null;
  celular: string | null;
  genero: number | null;
  inscripciones: any[];
}

export interface CajaPrograma {
  id_programa: number;
  nombre_programa: string;
  imparticiones: CajaImparticion[];
  planes: CajaPlan[]; // planes a nivel de programa
}

export interface CajaImparticion {
  id_imp: number;
  nombre_version: string | null;
  periodo: string;
  gestion: string;
  imparte_fecha_inicio: string;
  imparte_fecha_fin: string;
  planes?: CajaPlan[];
}

export interface CajaPlan {
  id_plan: number;
  titulo: string;
  costo: string;
  nro_cuotas: string;
  descuento: string;
  qr_image_url?: string | null;
}

export interface CajaBanco {
  id: number;
  nombre: string;
  numero_cuenta?: string | null;
  titular?: string | null;
}

@Injectable({ providedIn: 'root' })
export class CajaService {
  private http = inject(HttpClient);
  private api = '/api/v1/caja';

  buscarEstudiante(ci: string): Observable<CajaEstudiante | null> {
    return this.http.get<CajaEstudiante | null>(`${this.api}/buscar-estudiante`, { params: new HttpParams().set('ci', ci) });
  }

  buscarProgramas(q: string = ''): Observable<CajaPrograma[]> {
    return this.http.get<CajaPrograma[]>(`${this.api}/programas`, { params: new HttpParams().set('q', q) });
  }

  getBancos(): Observable<CajaBanco[]> {
    return this.http.get<CajaBanco[]>(`${this.api}/bancos`);
  }

  inscribir(payload: any, comprobanteFile?: File | null): Observable<any> {
    const fd = new FormData();
    Object.entries(payload).forEach(([k, v]) => {
      if (v !== null && v !== undefined) fd.append(k, String(v));
    });
    if (comprobanteFile) {
      fd.append('comprobante', comprobanteFile);
    }
    return this.http.post(`${this.api}/inscribir`, fd);
  }
}