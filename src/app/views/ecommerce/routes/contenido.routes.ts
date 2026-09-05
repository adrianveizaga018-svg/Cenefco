import { Routes } from '@angular/router'

import { Banners }      from '../../../banners/presentation/banners/banners'
import { BannerCreate } from '../../../banners/presentation/banner-create/banner-create'
import { BannerEdit }   from '../../../banners/presentation/banner-edit/banner-edit'

import { Eventos }      from '../../../eventos/presentation/eventos/eventos'
import { EventoCreate } from '../../../eventos/presentation/evento-create/evento-create'
import { EventoEdit }   from '../../../eventos/presentation/evento-edit/evento-edit'

import { TiposEvento }      from '../../../tipos-evento/presentation/tipos-evento/tipos-evento'
import { TipoEventoCreate } from '../../../tipos-evento/presentation/tipo-evento-create/tipo-evento-create'
import { TipoEventoEdit }   from '../../../tipos-evento/presentation/tipo-evento-edit/tipo-evento-edit'

import { EventosFotos } from '../../../eventos-fotos/presentation/eventos-fotos/eventos-fotos'

import { Articulos }      from '../../../articulos/presentation/articulos/articulos'
import { ArticuloCreate } from '../../../articulos/presentation/articulo-create/articulo-create'
import { ArticuloEdit }   from '../../../articulos/presentation/articulo-edit/articulo-edit'

import { Etiquetas }      from '../../../etiquetas/presentation/etiquetas/etiquetas'
import { EtiquetaCreate } from '../../../etiquetas/presentation/etiqueta-create/etiqueta-create'
import { EtiquetaEdit }   from '../../../etiquetas/presentation/etiqueta-edit/etiqueta-edit'

import { Noticias }      from '../../../noticias/presentation/noticias/noticias'
import { NoticiaCreate } from '../../../noticias/presentation/noticia-create/noticia-create'
import { NoticiaEdit }   from '../../../noticias/presentation/noticia-edit/noticia-edit'

import { CategoriasNoticia }      from '../../../categorias-noticia/presentation/categorias-noticia/categorias-noticia'
import { CategoriaNoticiaCreate } from '../../../categorias-noticia/presentation/categoria-noticia-create/categoria-noticia-create'
import { CategoriaNoticiaEdit }   from '../../../categorias-noticia/presentation/categoria-noticia-edit/categoria-noticia-edit'

import { Comunicados }      from '../../../comunicados/presentation/comunicados/comunicados'
import { ComunicadoCreate } from '../../../comunicados/presentation/comunicado-create/comunicado-create'
import { ComunicadoEdit }   from '../../../comunicados/presentation/comunicado-edit/comunicado-edit'

import { Boletines }     from '../../../boletines/presentation/boletines/boletines'
import { BoletinCreate } from '../../../boletines/presentation/boletin-create/boletin-create'
import { BoletinEdit }   from '../../../boletines/presentation/boletin-edit/boletin-edit'

import { Popups }      from '../../../popups/presentation/popups/popups'
import { PopupCreate } from '../../../popups/presentation/popup-create/popup-create'
import { PopupEdit }   from '../../../popups/presentation/popup-edit/popup-edit'

import { GaleriaCategorias }      from '../../../galeria-categorias/presentation/galeria-categorias/galeria-categorias'
import { GaleriaCategoriaCreate } from '../../../galeria-categorias/presentation/galeria-categoria-create/galeria-categoria-create'
import { GaleriaCategoriaEdit }   from '../../../galeria-categorias/presentation/galeria-categoria-edit/galeria-categoria-edit'

import { GaleriaVideos }      from '../../../galeria-videos/presentation/galeria-videos/galeria-videos'
import { GaleriaVideoCreate } from '../../../galeria-videos/presentation/galeria-video-create/galeria-video-create'
import { GaleriaVideoEdit }   from '../../../galeria-videos/presentation/galeria-video-edit/galeria-video-edit'

import { Fotos }      from '../../../fotos/presentation/fotos/fotos'
import { FotoCreate } from '../../../fotos/presentation/foto-create/foto-create'
import { FotoEdit }   from '../../../fotos/presentation/foto-edit/foto-edit'

import { Descargables }      from '../../../descargables/presentation/descargables/descargables'
import { DescargableCreate } from '../../../descargables/presentation/descargable-create/descargable-create'
import { DescargableEdit }   from '../../../descargables/presentation/descargable-edit/descargable-edit'

import { RedesSociales }    from '../../../redes-sociales/presentation/redes-sociales/redes-sociales'
import { RedSocialCreate }  from '../../../redes-sociales/presentation/red-social-create/red-social-create'
import { RedSocialEdit }    from '../../../redes-sociales/presentation/red-social-edit/red-social-edit'

import { Redirecciones }     from '../../../redirecciones/presentation/redirecciones/redirecciones'
import { RedireccionCreate } from '../../../redirecciones/presentation/redireccion-create/redireccion-create'
import { RedireccionEdit }   from '../../../redirecciones/presentation/redireccion-edit/redireccion-edit'

import { Faqs }      from '../../../faqs/presentation/faqs/faqs'
import { FaqCreate } from '../../../faqs/presentation/faq-create/faq-create'
import { FaqEdit }   from '../../../faqs/presentation/faq-edit/faq-edit'

import { Ayudas }      from '../../../ayudas/presentation/ayudas/ayudas'
import { AyudaCreate } from '../../../ayudas/presentation/ayuda-create/ayuda-create'
import { AyudaEdit }   from '../../../ayudas/presentation/ayuda-edit/ayuda-edit'

import { Resenas }      from '../../../resenas/presentation/resenas/resenas'
import { ResenaCreate } from '../../../resenas/presentation/resena-create/resena-create'
import { ResenaDetail } from '../../../resenas/presentation/resena-detail/resena-detail'

import { Testimonios }      from '../../../testimonios/presentation/testimonios/testimonios'
import { TestimonioCreate } from '../../../testimonios/presentation/testimonio-create/testimonio-create'
import { TestimonioEdit }   from '../../../testimonios/presentation/testimonio-edit/testimonio-edit'

import { Suscriptores } from '../../../suscriptores/presentation/suscriptores/suscriptores'
import { MensajesContacto }      from '../../../mensajes-contacto/presentation/mensajes-contacto/mensajes-contacto'
import { MensajeContactoDetail } from '../../../mensajes-contacto/presentation/mensaje-contacto-detail/mensaje-contacto-detail'

import { SugerenciasReclamos }     from '../../../sugerencias-reclamos/presentation/sugerencias-reclamos/sugerencias-reclamos'
import { SugerenciaReclamoDetail } from '../../../sugerencias-reclamos/presentation/sugerencia-reclamo-detail/sugerencia-reclamo-detail'

import { Analytics } from '../../../analytics/presentation/analytics/analytics'

import { TriviaCategorias }      from '../../../trivia-categorias/presentation/trivia-categorias/trivia-categorias'
import { TriviaCategoriaCreate } from '../../../trivia-categorias/presentation/trivia-categoria-create/trivia-categoria-create'
import { TriviaCategoriaEdit }   from '../../../trivia-categorias/presentation/trivia-categoria-edit/trivia-categoria-edit'

import { TriviaNiveles }      from '../../../trivia-niveles/presentation/trivia-niveles/trivia-niveles'
import { TriviaNivelCreate }  from '../../../trivia-niveles/presentation/trivia-nivel-create/trivia-nivel-create'
import { TriviaNivelEdit }    from '../../../trivia-niveles/presentation/trivia-nivel-edit/trivia-nivel-edit'

import { TriviaPreguntas }      from '../../../trivia-preguntas/presentation/trivia-preguntas/trivia-preguntas'
import { TriviaPreguntaCreate } from '../../../trivia-preguntas/presentation/trivia-pregunta-create/trivia-pregunta-create'
import { TriviaPreguntaEdit }   from '../../../trivia-preguntas/presentation/trivia-pregunta-edit/trivia-pregunta-edit'

import { TriviaRanking } from '../../../trivia-ranking/presentation/trivia-ranking/trivia-ranking'

import { TriviaPremios }      from '../../../trivia-premios/presentation/trivia-premios/trivia-premios'
import { TriviaPremioCreate } from '../../../trivia-premios/presentation/trivia-premio-create/trivia-premio-create'
import { TriviaPremioEdit }   from '../../../trivia-premios/presentation/trivia-premio-edit/trivia-premio-edit'

import { TriviaCanjes } from '../../../trivia-canjes/presentation/trivia-canjes/trivia-canjes'

export const CONTENIDO_ROUTES: Routes = [
    { path: 'cenefco/banners',          component: Banners,      data: { title: 'Banners' } },
    { path: 'cenefco/banner-create',    component: BannerCreate, data: { title: 'Nuevo Banner' } },
    { path: 'cenefco/banner-edit/:slug', component: BannerEdit,   data: { title: 'Editar Banner' } },

    { path: 'cenefco/eventos',          component: Eventos,      data: { title: 'Eventos' } },
    { path: 'cenefco/evento-create',    component: EventoCreate, data: { title: 'Nuevo Evento' } },
    { path: 'cenefco/evento-edit/:slug', component: EventoEdit,   data: { title: 'Editar Evento' } },

    { path: 'cenefco/tipos-evento',          component: TiposEvento,      data: { title: 'Tipos de Evento' } },
    { path: 'cenefco/tipo-evento-create',    component: TipoEventoCreate, data: { title: 'Nuevo Tipo de Evento' } },
    { path: 'cenefco/tipo-evento-edit/:id',  component: TipoEventoEdit,   data: { title: 'Editar Tipo de Evento' } },

    { path: 'cenefco/eventos-fotos', component: EventosFotos, data: { title: 'Fotos de Eventos' } },

    { path: 'cenefco/articulos',         component: Articulos,      data: { title: 'Artículos' } },
    { path: 'cenefco/articulo-create',   component: ArticuloCreate, data: { title: 'Nuevo Artículo' } },
    { path: 'cenefco/articulo-edit/:slug', component: ArticuloEdit,   data: { title: 'Editar Artículo' } },

    { path: 'cenefco/etiquetas',          component: Etiquetas,      data: { title: 'Etiquetas' } },
    { path: 'cenefco/etiqueta-create',    component: EtiquetaCreate, data: { title: 'Nueva Etiqueta' } },
    { path: 'cenefco/etiqueta-edit/:slug', component: EtiquetaEdit,   data: { title: 'Editar Etiqueta' } },

    { path: 'cenefco/noticias',          component: Noticias,      data: { title: 'Noticias' } },
    { path: 'cenefco/noticia-create',    component: NoticiaCreate, data: { title: 'Nueva Noticia' } },
    { path: 'cenefco/noticia-edit/:slug', component: NoticiaEdit,   data: { title: 'Editar Noticia' } },

    { path: 'cenefco/categorias-noticia',          component: CategoriasNoticia,      data: { title: 'Categorías de Noticia' } },
    { path: 'cenefco/categoria-noticia-create',    component: CategoriaNoticiaCreate, data: { title: 'Nueva Categoría de Noticia' } },
    { path: 'cenefco/categoria-noticia-edit/:id',  component: CategoriaNoticiaEdit,   data: { title: 'Editar Categoría de Noticia' } },

    { path: 'cenefco/comunicados',          component: Comunicados,      data: { title: 'Comunicados' } },
    { path: 'cenefco/comunicado-create',    component: ComunicadoCreate, data: { title: 'Nuevo Comunicado' } },
    { path: 'cenefco/comunicado-edit/:slug', component: ComunicadoEdit,   data: { title: 'Editar Comunicado' } },

    { path: 'cenefco/boletines',          component: Boletines,     data: { title: 'Boletines' } },
    { path: 'cenefco/boletin-create',     component: BoletinCreate, data: { title: 'Nuevo Boletín' } },
    { path: 'cenefco/boletin-edit/:id',   component: BoletinEdit,   data: { title: 'Editar Boletín' } },

    { path: 'cenefco/popups',          component: Popups,      data: { title: 'Popups' } },
    { path: 'cenefco/popup-create',    component: PopupCreate, data: { title: 'Nuevo Popup' } },
    { path: 'cenefco/popup-edit/:id',  component: PopupEdit,   data: { title: 'Editar Popup' } },

    { path: 'cenefco/galeria-categorias',          component: GaleriaCategorias,      data: { title: 'Categorías de Galería' } },
    { path: 'cenefco/galeria-categoria-create',    component: GaleriaCategoriaCreate, data: { title: 'Nueva Categoría de Galería' } },
    { path: 'cenefco/galeria-categoria-edit/:id',  component: GaleriaCategoriaEdit,   data: { title: 'Editar Categoría de Galería' } },

    { path: 'cenefco/galeria-videos',          component: GaleriaVideos,      data: { title: 'Galería de Videos' } },
    { path: 'cenefco/galeria-video-create',    component: GaleriaVideoCreate, data: { title: 'Nuevo Video' } },
    { path: 'cenefco/galeria-video-edit/:id',  component: GaleriaVideoEdit,   data: { title: 'Editar Video' } },

    { path: 'cenefco/fotos',           component: Fotos,      data: { title: 'Galería de Fotos' } },
    { path: 'cenefco/foto-create',     component: FotoCreate, data: { title: 'Nueva Foto' } },
    { path: 'cenefco/foto-edit/:id',   component: FotoEdit,   data: { title: 'Editar Foto' } },

    { path: 'cenefco/descargables',          component: Descargables,      data: { title: 'Descargables' } },
    { path: 'cenefco/descargable-create',    component: DescargableCreate, data: { title: 'Nuevo Descargable' } },
    { path: 'cenefco/descargable-edit/:id',  component: DescargableEdit,   data: { title: 'Editar Descargable' } },

    { path: 'cenefco/redes-sociales',          component: RedesSociales,   data: { title: 'Redes Sociales' } },
    { path: 'cenefco/red-social-create',       component: RedSocialCreate, data: { title: 'Nueva Red Social' } },
    { path: 'cenefco/red-social-edit/:id',     component: RedSocialEdit,   data: { title: 'Editar Red Social' } },

    { path: 'cenefco/redirecciones',          component: Redirecciones,     data: { title: 'Redirecciones' } },
    { path: 'cenefco/redireccion-create',     component: RedireccionCreate, data: { title: 'Nueva Redirección' } },
    { path: 'cenefco/redireccion-edit/:id',   component: RedireccionEdit,   data: { title: 'Editar Redirección' } },

    { path: 'cenefco/faqs',         component: Faqs,      data: { title: 'FAQs' } },
    { path: 'cenefco/faq-create',   component: FaqCreate, data: { title: 'Nueva FAQ' } },
    { path: 'cenefco/faq-edit/:id', component: FaqEdit,   data: { title: 'Editar FAQ' } },

    { path: 'cenefco/ayudas',          component: Ayudas,      data: { title: 'Ayudas / Soporte' } },
    { path: 'cenefco/ayuda-create',    component: AyudaCreate, data: { title: 'Nueva Ayuda' } },
    { path: 'cenefco/ayuda-edit/:id',  component: AyudaEdit,   data: { title: 'Editar Ayuda' } },

    { path: 'cenefco/resenas',           component: Resenas,      data: { title: 'Reseñas' } },
    { path: 'cenefco/resena-create',     component: ResenaCreate, data: { title: 'Nueva Reseña' } },
    { path: 'cenefco/resena-detail/:id', component: ResenaDetail, data: { title: 'Detalle de Reseña' } },

    { path: 'cenefco/testimonios',          component: Testimonios,      data: { title: 'Testimonios' } },
    { path: 'cenefco/testimonio-create',    component: TestimonioCreate, data: { title: 'Nuevo Testimonio' } },
    { path: 'cenefco/testimonio-edit/:slug', component: TestimonioEdit,   data: { title: 'Editar Testimonio' } },

    { path: 'cenefco/suscriptores', component: Suscriptores, data: { title: 'Suscriptores' } },

    { path: 'cenefco/mensajes-contacto',    component: MensajesContacto,      data: { title: 'Mensajes de Contacto' } },
    { path: 'cenefco/mensaje-contacto/:id', component: MensajeContactoDetail, data: { title: 'Detalle del Mensaje' } },

    { path: 'cenefco/sugerencias-reclamos',           component: SugerenciasReclamos,     data: { title: 'Sugerencias y Reclamos' } },
    { path: 'cenefco/sugerencia-reclamo-detail/:id',  component: SugerenciaReclamoDetail, data: { title: 'Detalle de Sugerencia / Reclamo' } },

    { path: 'cenefco/analytics', component: Analytics, data: { title: 'Analytics del Portal' } },

    { path: 'cenefco/trivia-categorias',          component: TriviaCategorias,      data: { title: 'Categorías de Trivia' } },
    { path: 'cenefco/trivia-categoria-create',    component: TriviaCategoriaCreate, data: { title: 'Nueva Categoría de Trivia' } },
    { path: 'cenefco/trivia-categoria-edit/:id',  component: TriviaCategoriaEdit,   data: { title: 'Editar Categoría de Trivia' } },

    { path: 'cenefco/trivia-niveles',          component: TriviaNiveles,      data: { title: 'Niveles de Trivia' } },
    { path: 'cenefco/trivia-nivel-create',      component: TriviaNivelCreate, data: { title: 'Nuevo Nivel de Trivia' } },
    { path: 'cenefco/trivia-nivel-edit/:id',    component: TriviaNivelEdit,   data: { title: 'Editar Nivel de Trivia' } },

    { path: 'cenefco/trivia-preguntas',          component: TriviaPreguntas,      data: { title: 'Preguntas de Trivia' } },
    { path: 'cenefco/trivia-pregunta-create',    component: TriviaPreguntaCreate, data: { title: 'Nueva Pregunta de Trivia' } },
    { path: 'cenefco/trivia-pregunta-edit/:id',  component: TriviaPreguntaEdit,   data: { title: 'Editar Pregunta de Trivia' } },

    { path: 'cenefco/trivia-ranking', component: TriviaRanking, data: { title: 'Ranking de Jugadores' } },

    { path: 'cenefco/trivia-premios',          component: TriviaPremios,      data: { title: 'Premios Canjeables' } },
    { path: 'cenefco/trivia-premio-create',    component: TriviaPremioCreate, data: { title: 'Nuevo Premio' } },
    { path: 'cenefco/trivia-premio-edit/:id',  component: TriviaPremioEdit,   data: { title: 'Editar Premio' } },

    { path: 'cenefco/trivia-canjes', component: TriviaCanjes, data: { title: 'Canjes de Puntos' } },
]
