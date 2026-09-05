export type TipoHonorario = 'diplomado_fijo' | 'rm_por_dia' | 'aval_por_dia';

export interface ConfigHonorario {
  id: number;
  id_programa: number;
  nombre_programa: string | null;
  tipo_honorario: TipoHonorario;
  monto_fijo: number | null;
  monto_por_dia: number | null;
}

export interface UpsertConfigHonorarioPayload {
  id_programa: number;
  tipo_honorario: TipoHonorario;
  monto_fijo?: number | null;
  monto_por_dia?: number | null;
}

export interface DocenteHonorarioSugerido {
  id_us: number;
  docente_nombre: string;
  id_imp: number;
  nombre_curso: string;
  id_programa: number;
  nombre_programa: string;
  tipo_honorario: TipoHonorario;
  dias_dictados: number;
  monto_sugerido: number;
  ya_registrado: boolean;
}
