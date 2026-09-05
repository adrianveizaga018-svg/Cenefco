import { Routes } from "@angular/router";
import { Ecommerce } from "../../dashboard/presentation/ecommerce/ecommerce";

export const DASHBOARDS_ROUTES: Routes = [
    {
        path: 'dashboards/cenefco',
        component: Ecommerce,
        data: { title: 'Dashboard' },
    }
]
