import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface CatalogoTarea {
  id?: number;
  titulo: string;
  requiere_archivo: boolean;
  estado: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class CatalogoTareaService {
  private http = inject(HttpClient);
  private base = '/api/v1/catalogo-tareas';

  getAll(): Observable<CatalogoTarea[]> {
    return this.http.get<CatalogoTarea[]>(this.base);
  }

  create(data: CatalogoTarea): Observable<CatalogoTarea> {
    return this.http.post<CatalogoTarea>(this.base, data);
  }

  update(id: number, data: CatalogoTarea): Observable<CatalogoTarea> {
    return this.http.put<CatalogoTarea>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
