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
]
