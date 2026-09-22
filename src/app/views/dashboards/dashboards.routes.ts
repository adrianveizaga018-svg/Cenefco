import { Routes } from "@angular/router";
import { Ecommerce } from "../../dashboard/presentation/ecommerce/ecommerce";

export const DASHBOARDS_ROUTES: Routes = [
    {
        path: 'dashboards/cenefco',
        component: Ecommerce,
        data: { title: 'Dashboard' },
    },
    {
        path: 'gerencia/dashboard',
        loadComponent: () => import('../../gerencia/presentation/dashboard-gerencial/dashboard-gerencial').then(m => m.default),
        data: { title: 'Dashboard Gerencial' },
    },
    {
        path: 'caja/inscripcion',
        loadComponent: () => import('../../caja/presentation/inscripcion-presencial/inscripcion-presencial').then(m => m.default),
        data: { title: 'Caja - Inscripción Presencial' },
    }
,
    {
        path: 'caja/pago-cuota',
        loadComponent: () => import('../../caja/presentation/pago-cuota/pago-cuota').then(m => m.PagoCuota),
        data: { title: 'Caja - Pago de Cuota' }
    }
,
    {
        path: 'cobranzas/dashboard',
        loadComponent: () => import('../../cobranzas/presentation/dashboard/dashboard').then(m => m.Dashboard),
        data: { title: 'Dashboard de Cobranzas' }
    }
]