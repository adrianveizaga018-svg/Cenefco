import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class CobranzaService {
  private http = inject(HttpClient);

  getMetrics(): Observable<any> {
    return this.http.get('/api/v1/cobranzas/metrics');
  }

  getDashboard(params: any): Observable<any> {
    let p = new HttpParams();
    if (params.page) p = p.set('page', params.page);
    if (params.per_page) p = p.set('per_page', params.per_page);
    if (params.estado) p = p.set('estado', params.estado);
    if (params.ci) p = p.set('ci', params.ci);
    
    return this.http.get('/api/v1/cobranzas', { params: p });
  }
}
