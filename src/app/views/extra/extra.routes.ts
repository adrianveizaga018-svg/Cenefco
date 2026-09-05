import { Routes } from "@angular/router";
import { Starter } from "./pages/starter/starter";
import { Pricing } from "./pages/pricing/pricing";
import { Faqs } from "./pages/faqs/faqs";
import { Timeline } from "./pages/timeline/timeline";

export const EXTRA_ROUTES: Routes = [
    {
        path: 'pages/starter',
        component: Starter,
        data: { title: 'Starter Page' },
    },
    {
        path: 'pages/pricing',
        component: Pricing,
        data: { title: 'Pricing' },
    },
    {
        path: 'pages/faqs',
        component: Faqs,
        data: { title: 'FAQs' },
    },
    {
        path: 'pages/timeline',
        component: Timeline,
        data: { title: 'Timeline' },
    }
]