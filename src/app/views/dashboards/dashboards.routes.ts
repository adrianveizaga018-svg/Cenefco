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
        path: 'crm/embudo',
        loadComponent: () => import('../../crm/presentation/embudo/embudo').then(m => m.EmbudoCrmComponent),
        data: { title: 'Embudo de ventas' }
    },
    {
        path: 'crm/tareas',
        loadComponent: () => import('../../crm/presentation/tareas/tareas').then(m => m.TareasCrmComponent),
        data: { title: 'Mis tareas de hoy' }
    },
    {
        path: 'crm/tablero',
        loadComponent: () => import('../../crm/presentation/tablero/tablero').then(m => m.TableroCrmComponent),
        data: { title: 'Tablero de ventas' }
    },
    {
        path: 'caja/comprobantes',
        loadComponent: () => import('../../caja/presentation/comprobantes/comprobantes').then(m => m.ComprobantesCajaComponent),
        data: { title: 'Comprobantes por verificar' }
    },
    {
        path: 'cobranzas/dashboard',
        loadComponent: () => import('../../cobranzas/presentation/dashboard/dashboard').then(m => m.Dashboard),
        data: { title: 'Dashboard de Cobranzas' }
    },
    {
        path: 'envios/dashboard',
        loadComponent: () => import('../../envios-certificado/presentation/dashboard-envios/dashboard-envios').then(m => m.DashboardEnvios),
        data: { title: 'Dashboard de Envíos' }
    }
]