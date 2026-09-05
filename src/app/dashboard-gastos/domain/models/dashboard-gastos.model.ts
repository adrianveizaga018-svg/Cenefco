export interface ResumenMesGastos {
  total_ingresos: number;
  total_gastos: number;
  balance: number;
  n_pagos_mes: number;
}

export interface GastoPorCategoria {
  categoria_id: number;
  categoria_nombre: string | null;
  total: number;
}

export interface GastoPorLineaNegocio {
  linea_negocio: string | null;
  total: number;
}

export interface GastoRecurrentePendiente {
  id: number;
  categoria_gasto_id: number;
  categoria_nombre: string | null;
  concepto: string;
  monto: number;
  dia_del_mes: number;
  activo: boolean;
  ultima_confirmacion: string | null;
}

export interface DashboardGastos {
  anio: number;
  mes: number;
  resumen_mes: ResumenMesGastos;
  gastos_por_categoria: GastoPorCategoria[];
  gastos_por_linea_negocio: GastoPorLineaNegocio[];
  gastos_pendientes: GastoRecurrentePendiente[];
}
