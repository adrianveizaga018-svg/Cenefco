import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AnalyticsStats } from '../../domain/models/analytics.model';

@Injectable({ providedIn: 'root' })
export class AnalyticsAdminService {
  private http = inject(HttpClient);

  getStats(periodo: 7 | 30 | 90 = 30): Observable<AnalyticsStats> {
    const params = new HttpParams().set('periodo', String(periodo));
    return this.http.get<AnalyticsStats>('/api/v1/analytics/stats', { params });
  }
}
