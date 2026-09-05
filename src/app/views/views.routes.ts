import { Routes } from "@angular/router";

export const VIEWS_ROUTES: Routes = [
    {
        path: '',
        loadChildren: () => import('./dashboards/dashboards.routes').then((mod) => mod.DASHBOARDS_ROUTES)
    },
    {
        path: '',
        loadChildren: () => import('./ecommerce/ecommerce.routes').then((mod) => mod.ECOMMERCE_ROUTES)
    },
    {
        path: '',
        loadChildren: () => import('./extra/extra.routes').then((mod) => mod.EXTRA_ROUTES)
    },
    {
        path: 'settings',
        loadComponent: () => import('./settings/settings').then((mod) => mod.SettingsComponent),
        data: { title: 'Configuraciones' }
    }
]
