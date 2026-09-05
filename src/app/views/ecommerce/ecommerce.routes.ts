import { Routes } from '@angular/router'

import { Ecommerce } from '../../dashboard/presentation/ecommerce/ecommerce'

import { CATALOGOS_ROUTES }    from './routes/catalogos.routes'
import { ACADEMICO_ROUTES }    from './routes/academico.routes'
import { CONTENIDO_ROUTES }    from './routes/contenido.routes'
import { INSTITUCIONAL_ROUTES } from './routes/institucional.routes'
import { USUARIOS_ROUTES }     from './routes/usuarios.routes'
import { WHATSAPP_ROUTES }     from './routes/whatsapp.routes'
import { CONFIGURACION_ROUTES } from './routes/configuracion.routes'

export const ECOMMERCE_ROUTES: Routes = [
    { path: 'cenefco/dashboard', component: Ecommerce, data: { title: 'Dashboard' } },

    ...CATALOGOS_ROUTES,
    ...ACADEMICO_ROUTES,
    ...CONTENIDO_ROUTES,
    ...INSTITUCIONAL_ROUTES,
    ...USUARIOS_ROUTES,
    ...WHATSAPP_ROUTES,
    ...CONFIGURACION_ROUTES,
]
