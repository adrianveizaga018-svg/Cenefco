export interface GeneralSettings {
  site_name: string;
  site_active: boolean;
  contact_email: string;
  items_per_page: number;
  maintenance_mode: boolean;
  site_logo?: string | null;
}

export interface UpdateSettingsRequest {
  site_name?: string;
  site_active?: boolean;
  contact_email?: string;
  items_per_page?: number;
  maintenance_mode?: boolean;
  site_logo?: File | null;
}

export interface SettingsResponse {
  message?: string;
  settings?: GeneralSettings;
}

export type ColorEstadoCobro = 'success' | 'warning' | 'danger' | 'primary' | 'info';

export interface CobroEstadoSettings {
  completo_label: string;
  completo_color: ColorEstadoCobro;
  parcial_label: string;
  parcial_color: ColorEstadoCobro;
  sin_pagos_label: string;
  sin_pagos_color: ColorEstadoCobro;
  colores_disponibles?: ColorEstadoCobro[];
}

export interface CobroEstadoSettingsResponse {
  message?: string;
  settings?: CobroEstadoSettings;
}
