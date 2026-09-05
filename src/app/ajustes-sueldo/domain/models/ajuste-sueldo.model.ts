export type TipoAjusteSueldo = 'descuento' | 'bono';

export interface AjusteSueldo {
  id: number;
  empleado_id: number;
  empleado_nombre: string | null;
  anio: number;
  mes: number;
  tipo: TipoAjusteSueldo;
  monto: number;
  motivo: string;
  aplicado: boolean;
  planilla_detalle_id: number | null;
  created_at: string | null;
}

export interface AjusteSueldoListResponse {
  data: AjusteSueldo[];
  total: number;
}

export interface CreateAjusteSueldoPayload {
  empleado_id: number;
  anio: number;
  mes: number;
  tipo: TipoAjusteSueldo;
  monto: number;
  motivo: string;
}
