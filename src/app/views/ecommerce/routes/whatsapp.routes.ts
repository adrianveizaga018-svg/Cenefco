import { Routes } from '@angular/router'

import { WhatsappAsesores }      from '../../../whatsapp/presentation/whatsapp-asesores/whatsapp-asesores'
import { WhatsappConversaciones } from '../../../whatsapp/presentation/whatsapp-conversaciones/whatsapp-conversaciones'
import { WhatsappMensajes }       from '../../../whatsapp/presentation/whatsapp-mensajes/whatsapp-mensajes'
import { WhatsappEnviar }         from '../../../whatsapp/presentation/whatsapp-enviar/whatsapp-enviar'
import { WhatsappPlantillas }     from '../../../whatsapp/presentation/whatsapp-plantillas/whatsapp-plantillas'
import { WhatsappEstado }         from '../../../whatsapp/presentation/whatsapp-estado/whatsapp-estado'
import { WhatsappCuentas }        from '../../../whatsapp/presentation/whatsapp-cuentas/whatsapp-cuentas'
import { NluPanel }               from '../../../whatsapp/presentation/nlu-panel/nlu-panel'
import { IntentList }             from '../../../whatsapp/presentation/intent-list/intent-list'
import { IntentForm }             from '../../../whatsapp/presentation/intent-form/intent-form'

import { WhatsappGrupos }      from '../../../whatsapp-grupos/presentation/whatsapp-grupos/whatsapp-grupos'
import { WhatsappGrupoCreate } from '../../../whatsapp-grupos/presentation/whatsapp-grupo-create/whatsapp-grupo-create'
import { WhatsappGrupoEdit }   from '../../../whatsapp-grupos/presentation/whatsapp-grupo-edit/whatsapp-grupo-edit'

import { SpeechesVentas }     from '../../../speeches-ventas/presentation/speeches-ventas/speeches-ventas'
import { SpeechVentasCreate } from '../../../speeches-ventas/presentation/speech-ventas-create/speech-ventas-create'
import { SpeechVentasEdit }   from '../../../speeches-ventas/presentation/speech-ventas-edit/speech-ventas-edit'

import { EfectosEspeciales }    from '../../../efectos-especiales/presentation/efectos-especiales/efectos-especiales'
import { EfectoEspecialCreate } from '../../../efectos-especiales/presentation/efecto-especial-create/efecto-especial-create'
import { EfectoEspecialEdit }   from '../../../efectos-especiales/presentation/efecto-especial-edit/efecto-especial-edit'

export const WHATSAPP_ROUTES: Routes = [
    { path: 'cenefco/whatsapp-estado',    component: WhatsappEstado,  data: { title: 'Bot WhatsApp — Estado' } },
    { path: 'cenefco/whatsapp-cuentas',   component: WhatsappCuentas, data: { title: 'Cuentas WhatsApp' } },
    { path: 'cenefco/whatsapp-nlu',       component: NluPanel,        data: { title: 'Motor NLU' } },
    { path: 'cenefco/intents',            component: IntentList,      data: { title: 'Gestión de Intents' } },
    { path: 'cenefco/intent-create',      component: IntentForm,      data: { title: 'Nuevo Intent' } },
    { path: 'cenefco/intent-edit/:id',    component: IntentForm,      data: { title: 'Editar Intent' } },

    { path: 'cenefco/asesores',               component: WhatsappAsesores,       data: { title: 'Asesores WhatsApp' } },
    { path: 'cenefco/whatsapp-conversaciones', component: WhatsappConversaciones, data: { title: 'Conversaciones WhatsApp' } },
    { path: 'cenefco/whatsapp-mensajes/:id',   component: WhatsappMensajes,       data: { title: 'Mensajes WhatsApp' } },
    { path: 'cenefco/whatsapp-enviar',         component: WhatsappEnviar,         data: { title: 'Enviar Mensaje WhatsApp' } },
    { path: 'cenefco/whatsapp-plantillas',     component: WhatsappPlantillas,     data: { title: 'Plantillas WhatsApp' } },

    { path: 'cenefco/whatsapp-grupos',           component: WhatsappGrupos,      data: { title: 'Grupos de WhatsApp' } },
    { path: 'cenefco/whatsapp-grupo-create',     component: WhatsappGrupoCreate, data: { title: 'Nuevo Grupo WhatsApp' } },
    { path: 'cenefco/whatsapp-grupo-edit/:id',   component: WhatsappGrupoEdit,   data: { title: 'Editar Grupo WhatsApp' } },

    { path: 'cenefco/speeches-ventas',           component: SpeechesVentas,     data: { title: 'Speech de Ventas' } },
    { path: 'cenefco/speeches-ventas/create',    component: SpeechVentasCreate, data: { title: 'Nuevo Speech' } },
    { path: 'cenefco/speeches-ventas/:id/edit',  component: SpeechVentasEdit,   data: { title: 'Editar Speech' } },

    { path: 'cenefco/efectos-especiales',          component: EfectosEspeciales,    data: { title: 'Efectos Especiales' } },
    { path: 'cenefco/efecto-especial-create',      component: EfectoEspecialCreate, data: { title: 'Nuevo Efecto Especial' } },
    { path: 'cenefco/efecto-especial-edit/:id',    component: EfectoEspecialEdit,   data: { title: 'Editar Efecto Especial' } },
]
