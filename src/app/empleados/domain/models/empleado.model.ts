export interface Empleado {
  id: number;
  nombre_completo: string;
  cargo: string;
  sueldo_mensual: number;
  ci: string;
  carnet_pdf: string | null;
  correo: string | null;
  celular_personal: string | null;
  celular_corporativo: string | null;
  direccion: string | null;
  fecha_ingreso: string | null;
  activo: boolean;
}

export interface EmpleadoListResponse {
  data: Empleado[];
  total: number;
}

export interface CreateEmpleadoPayload {
  nombre_completo: string;
  cargo: string;
  sueldo_mensual: number;
  ci: string;
  carnet: File;
  correo?: string | null;
  celular_personal?: string | null;
  celular_corporativo?: string | null;
  direccion?: string | null;
  fecha_ingreso?: string | null;
  activo?: boolean;
}

export interface UpdateEmpleadoPayload {
  nombre_completo?: string;
  cargo?: string;
  sueldo_mensual?: number;
  ci?: string;
  carnet?: File | null;
  correo?: string | null;
  celular_personal?: string | null;
  celular_corporativo?: string | null;
  direccion?: string | null;
  fecha_ingreso?: string | null;
  activo?: boolean;
}
