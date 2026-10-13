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

/** Ganancia de un programa en el mes: cobrado contra docentes, publicidad y otros gastos suyos. */
export interface RentabilidadPrograma {
  id_programa: number;
  programa: string;
  ingresos: number;
  docentes: number;
  publicidad: number;
  otros: number;
  gastos: number;
  ganancia: number;
  margen: number | null;
}

export interface DashboardGastos {
  anio: number;
  mes: number;
  resumen_mes: ResumenMesGastos;
  gastos_por_categoria: GastoPorCategoria[];
  gastos_por_linea_negocio: GastoPorLineaNegocio[];
  gastos_pendientes: GastoRecurrentePendiente[];
  rentabilidad: { programas: RentabilidadPrograma[]; gastos_generales: number };
}
