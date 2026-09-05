export interface Universidad {
  id_universidad:      number;
  nombre_universidad:  string;
  id_ciudad:           number | null;
  id_tipouniversidad:  number | null;
  estado:              number;
  id_us_reg:           number | null;
}

export interface UniversidadListResponse {
  data:  Universidad[];
  total: number;
}

export interface UniversidadListParams {
  pageIndex: number;
  pageSize:  number;
  query?:    string;
  refresh?:  number;
}
