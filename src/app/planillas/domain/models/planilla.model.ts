export interface PlanillaDetalle {
  id: number;
  empleado_id: number;
  nombre_completo: string;
  cargo: string;
  monto: number;
  monto_base: number | null;
  total_descuentos: number;
  total_bonos: number;
}

export interface Planilla {
  id: number;
  anio: number;
  mes: number;
  total: number;
  gasto_id: number;
  detalle: PlanillaDetalle[];
  created_at: string | null;
}

export interface PlanillaListResponse {
  data: Planilla[];
  total: number;
}

export interface GenerarPlanillaPayload {
  anio: number;
  mes: number;
}

export interface AjusteSueldoPreview {
  id: number;
  empleado_id: number;
  empleado_nombre: string | null;
  anio: number;
  mes: number;
  tipo: 'descuento' | 'bono';
  monto: number;
  motivo: string;
  aplicado: boolean;
  planilla_detalle_id: number | null;
  created_at: string | null;
}

export interface PlanillaPreviewItem {
  empleado_id: number;
  nombre_completo: string;
  cargo: string;
  monto_base: number;
  total_descuentos: number;
  total_bonos: number;
  monto_neto: number;
  ajustes: AjusteSueldoPreview[];
}

export interface PlanillaPreviewResponse {
  data: PlanillaPreviewItem[];
}
