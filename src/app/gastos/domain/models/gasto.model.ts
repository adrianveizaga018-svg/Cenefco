export interface CategoriaGasto {
  id: number;
  nombre: string;
  linea_negocio: string | null;
  activo: boolean;
}

export interface Gasto {
  id: number;
  categoria_gasto_id: number;
  categoria_nombre: string | null;
  concepto: string;
  monto: number;
  fecha: string;
  responsable: string | null;
  comprobante_url: string | null;
  nota: string | null;
  gasto_recurrente_id: number | null;
  campana_publicidad_id: number | null;
  campana_publicidad_nombre: string | null;
  created_at: string | null;
  updated_at: string | null;
}

export interface GastoRecurrente {
  id: number;
  categoria_gasto_id: number;
  categoria_nombre: string | null;
  concepto: string;
  monto: number;
  dia_del_mes: number;
  activo: boolean;
  ultima_confirmacion: string | null;
}

export interface GastoListResponse {
  data: Gasto[];
  total: number;
}

export interface CreateGastoPayload {
  categoria_gasto_id: number;
  concepto: string;
  monto: number;
  fecha: string;
  responsable?: string | null;
  comprobante?: File | null;
  nota?: string | null;
  campana_publicidad_id?: number | null;
}

export interface UpdateGastoPayload {
  categoria_gasto_id?: number;
  concepto?: string;
  monto?: number;
  fecha?: string;
  responsable?: string | null;
  comprobante?: File | null;
  nota?: string | null;
  campana_publicidad_id?: number | null;
}

export interface CreateGastoRecurrentePayload {
  categoria_gasto_id: number;
  concepto: string;
  monto: number;
  dia_del_mes: number;
  activo?: boolean;
}

export interface ResumenMes {
  resumen: {
    total_mes: number;
    por_categoria: { categoria_id: number; categoria_nombre: string | null; total: number }[];
  };
  por_linea_negocio: { linea_negocio: string | null; total: number }[];
}
