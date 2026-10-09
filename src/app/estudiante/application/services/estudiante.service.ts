import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface EstudianteDashboardResponse {
  usuario: {
    id: number;
    nombre: string;
    email: string;
    ci: string | null;
  };
  resumen: {
    cursos_activos: number;
    certificados: number;
    clases_grabadas: number;
    deuda_pendiente: number;
  };
  proxima_cuota: (CuotaEstudiante & { programa: string }) | null;
  cursos: CursoEstudiante[];
  recomendados: ProgramaCatalogo[];
}

export interface GrabacionZoom {
  id: number;
  tema: string;
  play_url: string;
  duracion: number;
  recording_start: string;
}

export type EstadoCurso = 'por_iniciar' | 'en_curso' | 'finalizado';

export interface CursoEstudiante {
  id_ins: number;
  fecha_ins: string;
  id_imp: number;
  nombre_materia: string;
  titulo_personalizado: string | null;
  imparte_fecha_inicio: string | null;
  imparte_fecha_fin: string | null;
  horas_academicas: number | null;
  nombre_docente: string | null;
  slug: string | null;
  foto: string | null;
  whatsapp_grupo_url: string | null;
  estado_curso: EstadoCurso;
  tiene_certificado: boolean;
  gratuito: boolean;
  grabaciones: GrabacionZoom[];
}

export interface PlanCatalogo {
  id_plan: number;
  titulo: string;
  costo: number | null;
  nro_cuotas: number;
  gratuito: boolean;
  qr_url: string | null;
  /** Lo que se paga para inscribirse: la primera cuota o, al contado, el costo. */
  primer_pago: number;
}

export interface BancoCuenta {
  id: number;
  nombre: string;
  numero_cuenta: string | null;
  titular: string | null;
}

export interface InfoPago {
  id_ins: number;
  programa: string | null;
  plan: string | null;
  qr_url: string | null;
  saldo: number;
  proxima: { descripcion: string; saldo: number; vence: string | null } | null;
  bancos: BancoCuenta[];
  en_revision: number;
}

export interface ProgramaCatalogo {
  id_programa: number;
  id_imp: number;
  nombre: string;
  slug: string | null;
  foto: string | null;
  resumen: string;
  creditaje: string | null;
  fecha_inicio: string | null;
  fecha_fin: string | null;
  planes: PlanCatalogo[];
  gratuito: boolean;
  precio_desde: number | null;
  url_portal: string | null;
}

export interface CertificadoEstudiante {
  id: number;
  nombre_en_certificado: string;
  programa_en_certificado: string;
  codigo_verificacion: string;
  qr_url: string;
  archivo_url: string | null;
  estado: string;
  created_at: string;
}

export interface CuotaEstudiante {
  id_fechapago: number;
  nro_pago: number;
  descripcion: string;
  vencida: boolean;
  /** Lo que falta pagar de la cuota (una parcial cobra solo esto). */
  saldo: number;
  estado: 'pagado' | 'parcial' | 'pendiente';
  monto_a_pagar: number;
  fecha_limite: string | null;
  pagado: boolean;
  monto_pagado: number | null;
  fecha_pago: string | null;
  metodo_pago: string | null;
}

export interface PlanPagoEstudiante {
  id_ins: number;
  nombre_programa: string;
  programa_slug: string | null;
  fecha_ins: string;
  fecha_inicio: string | null;
  fecha_fin: string | null;
  gratuito: boolean;
  total_plan: number;
  total_pagado: number;
  saldo: number;
  estado_pago: 'pagado' | 'parcial' | 'pendiente' | 'gratuito';
  cuotas: CuotaEstudiante[];
  nro_pagos: number;
  comprobantes_en_revision: { id_pago: number; monto: number; fecha: string }[];
}

@Injectable({ providedIn: 'root' })
export class EstudianteService {
  private http = inject(HttpClient);
  private apiUrl = '/api/v1/estudiante';

  getDashboard(): Observable<EstudianteDashboardResponse> {
    return this.http.get<EstudianteDashboardResponse>(`${this.apiUrl}/dashboard`);
  }

  getMisCursos(): Observable<{ data: CursoEstudiante[] }> {
    return this.http.get<{ data: CursoEstudiante[] }>(`${this.apiUrl}/mis-cursos`);
  }

  getMisCertificados(): Observable<{ data: CertificadoEstudiante[] }> {
    return this.http.get<{ data: CertificadoEstudiante[] }>(`${this.apiUrl}/mis-certificados`);
  }

  getMisPagos(): Observable<{ data: PlanPagoEstudiante[] }> {
    return this.http.get<{ data: PlanPagoEstudiante[] }>(`${this.apiUrl}/mis-pagos`);
  }

  /** Programas abiertos en los que todavía no está inscrito. */
  getCatalogo(): Observable<{ data: ProgramaCatalogo[] }> {
    return this.http.get<{ data: ProgramaCatalogo[] }>(`${this.apiUrl}/catalogo`);
  }

  getInfoPago(idIns: number): Observable<InfoPago> {
    return this.http.get<InfoPago>(`${this.apiUrl}/pago-info/${idIns}`);
  }

  getBancos(): Observable<BancoCuenta[]> {
    return this.http.get<BancoCuenta[]>(`${this.apiUrl}/bancos`);
  }

  /** Comprobante de una cuota: queda en revisión hasta que Caja lo apruebe. */
  enviarComprobante(datos: FormData): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${this.apiUrl}/comprobantes`, datos);
  }

  /** Inscripción a un programa de pago con el comprobante del primer pago. */
  inscribirseConComprobante(datos: FormData): Observable<{ message: string; id_ins: number; programa: string }> {
    return this.http.post<{ message: string; id_ins: number; programa: string }>(`${this.apiUrl}/inscribirse`, datos);
  }

  /** Solo para planes gratuitos: la inscripción queda hecha al instante. */
  inscribirseGratis(idImp: number, idPlan: number): Observable<{ message: string; id_ins: number; programa: string }> {
    return this.http.post<{ message: string; id_ins: number; programa: string }>(`${this.apiUrl}/inscribirse-gratis`, { id_imp: idImp, id_plan: idPlan });
  }
}
