# Manual de Usuario — Sistema de Gestión CENEFCO

> Índice general del manual. Cada entrada corresponde a un capítulo con las instrucciones paso a paso del módulo. Estructura basada en los módulos reales del panel administrativo (`cenefco-admin`) y el sitio público (`cenefco-portal`).

## Descripción General del Sistema

El **Sistema de Gestión CENEFCO** es la plataforma que digitaliza y unifica, de punta a punta, toda la operación de un centro de formación continua: capta al estudiante desde que descubre un curso en internet, lo acompaña durante su inscripción y pago, gestiona su vida académica mientras cursa, y culmina entregándole un certificado verificable — todo desde un mismo sistema, con datos consistentes en cada paso. No es un conjunto de planillas ni de sistemas sueltos que hay que conciliar a mano: es una sola fuente de verdad para lo académico, lo financiero, lo administrativo y lo comercial.

Antes de un sistema así, la operación típica de un centro de formación vive repartida en hojas de cálculo, cuadernos de caja, carpetas de comprobantes escaneados y conversaciones de WhatsApp sueltas — con el riesgo constante de perder inscripciones, cobrar de más o de menos, emitir certificados a quien no aprobó, o no tener claridad de cuánto se facturó este mes hasta cerrar todo a fin de mes. CENEFCO resuelve ese problema centralizando cada proceso en un único flujo digital, trazable y auditable.

**¿Qué resuelve, en concreto?**

- **Presencia comercial y captación** — un sitio web público donde se publican los programas, sus fechas, precios y planes de pago, con contenido institucional (noticias, testimonios, banners, FAQs) que da confianza al futuro estudiante antes de inscribirse.
- **Inscripción y cobro sin fricción** — el estudiante se inscribe y paga en línea (o el cajero lo registra presencialmente), con planes de cuotas, descuentos por convenio y comprobantes generados automáticamente — sin depender de que alguien concilie manualmente caja contra un cuaderno.
- **Gestión académica ordenada** — cursos, grupos, docentes, horarios y notas quedan enlazados entre sí, así que en todo momento se sabe quién está inscrito en qué, con qué docente y bajo qué plan, sin depender de la memoria de una sola persona.
- **Certificación con respaldo digital** — cada certificado se emite con un código único y QR verificable públicamente, eliminando el riesgo de certificados falsificados o emitidos por error a alumnos que no cumplieron requisitos.
- **Control financiero en tiempo real** — ingresos, pagos pendientes, sueldos docentes, gastos y planillas se ven consolidados y actualizados al instante, no al cierre de mes.
- **Comunicación institucional integrada** — correo, WhatsApp (incluyendo un bot con reconocimiento de intenciones para atención automática) y redes sociales quedan conectados al mismo sistema, sin herramientas aisladas ni mensajes perdidos.
- **Seguridad y trazabilidad** — cada acción queda asociada a un usuario y un rol con permisos específicos, así que siempre es posible saber quién hizo qué y cuándo.

**¿A quién beneficia?**

- La **dirección** obtiene visibilidad real del negocio: ingresos, inscripciones y ocupación de cursos sin esperar reportes armados a mano.
- **Coordinación académica** gana orden: cursos, docentes y horarios dejan de coordinarse por chats sueltos.
- **Caja/administración** reduce errores de cobro y descuadres, con cada pago validado y auditado en el sistema.
- **Docentes** acceden a su información de dictado, listas de estudiantes y liquidación de honorarios sin depender de terceros.
- **Estudiantes** viven una experiencia moderna: se informan, se inscriben, pagan y descargan su certificado desde el mismo portal, sin trámites presenciales innecesarios.

Está compuesto por dos aplicaciones que trabajan sobre la misma base de datos, garantizando que lo que ve el público y lo que gestiona el personal interno sea siempre la misma información, sin duplicar ni desincronizar datos:

- **Panel Administrativo** (`cenefco-admin`) — de uso interno, para el personal de CENEFCO (administración, cajeros, coordinadores académicos, docentes). Aquí se gestionan cursos, inscripciones, pagos, certificados, contenido del sitio web y la configuración general del sistema.
- **Sitio Público / Portal** (`cenefco-portal`) — de cara al público, donde los visitantes conocen la oferta académica, se inscriben, pagan en línea y consultan su historial de pagos.

## De Qué Trata Este Documento

Este manual es la guía de referencia para el personal que opera el sistema día a día. Su objetivo es que cualquier usuario, sin importar su rol, pueda encontrar rápidamente **cómo realizar una tarea concreta** en el módulo correspondiente: crear un curso, registrar un pago, emitir un certificado, publicar una noticia, etc.

El documento está organizado como un **índice general** (la sección siguiente) que enumera todos los módulos del sistema agrupados por área de trabajo, con una breve descripción de qué hace cada uno. Cada entrada del índice se irá completando, en revisiones futuras, con el detalle paso a paso (capturas de pantalla, requisitos y casos de uso) de su módulo correspondiente — por ahora sirve como mapa de navegación de todo lo que el sistema permite hacer.

## Importancia y Cuidado de las Credenciales

El sistema maneja información sensible: datos personales de estudiantes, pagos, certificados oficiales y comunicación institucional. El acceso se controla mediante **usuario, contraseña y roles/permisos** — por eso el cuidado de las credenciales de acceso es responsabilidad directa de cada usuario del sistema.

Recomendaciones que todo usuario debe seguir:

- **No compartas tu usuario ni tu contraseña** con otra persona, ni siquiera con un compañero de trabajo. Cada acción que se realiza en el sistema queda asociada a la cuenta que la ejecutó (auditoría interna) — si prestas tu cuenta, cualquier error o uso indebido quedará registrado a tu nombre.
- **Usa una contraseña única y segura**, distinta a la de tu correo personal u otras plataformas. Cámbiala periódicamente desde **Mi Perfil**.
- **Cierra sesión** al terminar de usar el sistema, especialmente en computadoras compartidas o de uso público.
- **No guardes contraseñas en notas visibles** (papeles pegados al monitor, archivos de texto sin protección, chats de WhatsApp).
- Los roles y permisos existen para limitar qué puede hacer cada usuario (por ejemplo, un cajero no debería poder eliminar cursos). **No pidas permisos que no correspondan a tu función** — repórtalo al administrador del sistema si detectas que tienes más acceso del que deberías.
- Si sospechas que tu cuenta fue comprometida (accesos que no reconoces, cambios que no hiciste), **repórtalo de inmediato** al equipo de sistemas para que se restablezca la contraseña y se revise el historial de actividad.
- Las llaves y credenciales técnicas (accesos a servidores, base de datos, API keys de servicios externos como WhatsApp, Zoom o pasarelas de pago) son de uso exclusivo del equipo técnico y **no deben compartirse fuera de ese ámbito** ni subirse a repositorios, chats o documentos de acceso público.

## Índice

### 1. Introducción y Acceso al Sistema
- **1.1 Inicio de sesión y roles** — Cómo ingresar al panel, recuperar contraseña y qué ve cada rol según sus permisos.
- **1.2 Mi Perfil** — Editar datos personales y contraseña de la cuenta propia.
- **1.3 Navegación general del panel** — Estructura del menú lateral, buscador y notificaciones.

### 2. Gestión Académica
- **2.1 Cursos / Programas** — Alta, edición y publicación de los programas académicos (diplomados, cursos, maestrías) que se muestran en el sitio público.
- **2.2 Programas Académicos** — Vista alternativa de programas con activación/desactivación (no elimina el registro).
- **2.3 Áreas de Conocimiento** — Categorías temáticas bajo las que se agrupan los programas (Gestión, Salud, Tecnología, etc.).
- **2.4 Categorías de Programa** — Clasificación adicional de programas para filtros del sitio público.
- **2.5 Planes Académicos** — Definición de planes de cuotas (número de pagos, montos, plazos) asociados a un programa.
- **2.6 Convenios** — Registro de convenios institucionales que otorgan descuentos o condiciones especiales.
- **2.7 Grupo Académico (Imparte)** — Paralelos/grupos de dictado de una materia: docente, período, cupo, fechas de inicio y fin.
- **2.8 Calendario Académico** — Eventos institucionales (feriados, cierres de inscripción) y listado de cursos vigentes según fecha.
- **2.9 Cursos Migrados** — Importación y consulta de cursos históricos migrados desde el sistema anterior.
- **2.10 Docentes** — Perfiles de docentes, materias que dictan y su información pública en el sitio web.
- **2.11 Sueldos Docentes** — Cálculo, ajuste y pago de honorarios a docentes por curso dictado.
- **2.12 Documentos Académicos** — Reglamentos, guías y documentos de referencia del área académica.
- **2.13 Citas de Asesoría** — Agenda de citas de orientación académica con prospectos o estudiantes.
- **2.14 Formularios de Inscripción / Generador de Formularios** — Creación de formularios dinámicos usados en el proceso de inscripción a cada programa.

### 3. Inscripciones
- **3.1 Inscripciones** — Registro y seguimiento de estudiantes inscritos a un curso o grupo académico.
- **3.2 Inscripciones a Diplomado** — Flujo específico de inscripción para programas tipo diplomado.
- **3.3 Reportes de Inscripciones** — Estadísticas de inscritos por período, canal de venta y curso.

### 4. Pagos y Finanzas
- **4.1 Pagos Académicos** — Registro y verificación de pagos de cuotas realizados por los estudiantes.
- **4.2 Fechas de Pago** — Definición de las cuotas (montos y plazos) de cada plan académico.
- **4.3 Reporte de Cobros** — Consolidado de cobros por curso, docente o período.
- **4.4 Ingresos** — Listado y resumen de todos los pagos activos del sistema (total general, mensual y anual).
- **4.5 Ventas** — Registro de ventas realizadas fuera del flujo normal de inscripción (venta directa/manual).
- **4.6 Reporte Financiero** — Consolidado de ingresos y ventas para análisis financiero general.
- **4.7 Correos Enviados** — Historial de comprobantes y notificaciones de pago enviadas por correo.
- **4.8 Gastos / Gastos Recurrentes** — Registro de egresos operativos, únicos y recurrentes.
- **4.9 Dashboard de Gastos** — Panel visual de egresos por categoría y período.
- **4.10 Regalía Sociedad de Ingenieros** — Cálculo y control de la regalía institucional (SIB).
- **4.11 Empleados / Planillas / Ajustes de Sueldo** — Gestión de personal de planta, generación de planillas mensuales y ajustes salariales.
- **4.12 Configurar Honorarios / Honorarios del Mes** — Configuración de honorarios docentes por programa y su liquidación mensual.

### 5. Certificados
- **5.1 Certificados** — Emisión y consulta de certificados con código único y QR de verificación.
- **5.2 Plantillas de Certificado** — Diseño visual (JPG + posición de campos) de las plantillas de certificado.
- **5.3 Lista de Aprobados** — Carga de la lista oficial de aprobados por curso, base para generar certificados en lote.
- **5.4 Verificaciones de Certificados** — Historial de verificaciones públicas hechas desde `/verificar/{codigo}`.
- **5.5 Certificados Post-Inscripción / Solicitudes** — Configuración de certificados automáticos tras completar un curso y sus solicitudes.

### 6. Catálogos del Sistema
- **6.1 Ciudades, Profesiones, Niveles** — Catálogos base usados en formularios de usuario y programas.
- **6.2 Tipos de Pago, Tipos de Universidad, Tipos de Postgrado** — Catálogos de clasificación usados en inscripciones y perfiles.
- **6.3 Configuración Académica** — Parámetros generales del área académica (gestiones, períodos válidos, etc.).
- **6.4 Universidades, Grados Académicos, Expedido** — Catálogos de procedencia académica de estudiantes y docentes.

### 7. Configuración del Sistema
- **7.1 Configuraciones / Configuración del Sitio** — Parámetros generales de la plataforma y datos públicos del sitio (nombre, contacto, mantenimiento).
- **7.2 Moodle (Moodles, Cursos, Usuarios)** — Integración y sincronización con la plataforma Moodle.
- **7.3 Zoom (Cuentas, Reuniones, Grabaciones)** — Integración con Zoom para clases virtuales.
- **7.4 Cartas Modelo / Cartas / Cartas Generadas** — Plantillas de cartas institucionales y su generación para estudiantes o docentes.

### 8. Contenido del Sitio Web
- **8.1 Banners** — Imágenes principales rotativas del sitio público.
- **8.2 Eventos / Tipos de Evento / Fotos de Eventos** — Publicación de eventos institucionales y su galería asociada.
- **8.3 Artículos / Etiquetas** — Blog institucional y etiquetado de contenido.
- **8.4 Noticias / Categorías de Noticia** — Módulo de noticias del portal.
- **8.5 Comunicados** — Comunicados oficiales publicados en el sitio.
- **8.6 Boletines** — Boletines informativos periódicos.
- **8.7 Popups** — Ventanas emergentes promocionales del sitio público.
- **8.8 Galería (Categorías, Videos, Fotos)** — Administración de la galería multimedia institucional.
- **8.9 Descargables** — Archivos descargables (brochures, guías) disponibles al público.
- **8.10 Redes Sociales / Redirecciones** — Enlaces a redes sociales y reglas de redirección de URLs.
- **8.11 FAQs / Ayudas** — Preguntas frecuentes y contenido de soporte al usuario.
- **8.12 Reseñas / Testimonios** — Opiniones y testimonios de estudiantes mostrados en el sitio.
- **8.13 Suscriptores** — Lista de correos suscritos al boletín institucional.
- **8.14 Mensajes de Contacto / Sugerencias y Reclamos** — Bandeja de mensajes recibidos desde los formularios públicos.
- **8.15 Analytics del Portal** — Métricas de visitas y comportamiento del sitio público.

### 9. Institucional
- **9.1 Autoridades / Secretarías / Organigramas** — Estructura organizativa y autoridades vigentes de la institución.
- **9.2 Directorio Institucional** — Directorio de contacto de áreas y personal.
- **9.3 Historia Institucional / Hitos Institucionales** — Línea de tiempo y hechos relevantes de la institución.
- **9.4 Cifras Institucionales** — Indicadores destacados mostrados en la página de inicio (ej. "1000+ egresados").
- **9.5 Aliados / Acreditaciones** — Instituciones aliadas y acreditaciones obtenidas.
- **9.6 Notas de Prensa** — Apariciones en medios de comunicación.
- **9.7 Normas / Tipos de Norma** — Normativa legal e institucional publicada.
- **9.8 Manuales Institucionales** — Manuales y reglamentos internos publicados.
- **9.9 Transparencia (Documentos y Tipos)** — Documentos de transparencia institucional.
- **9.10 Tesis / Monografías / Revistas / Revistas Científicas** — Repositorio de producción académica institucional.
- **9.11 Menús del Portal** — Configuración de la navegación del sitio público.

### 10. Usuarios y Seguridad
- **10.1 Usuarios** — Alta, edición y estado de las cuentas del sistema (estudiantes, docentes, personal).
- **10.2 Roles** — Definición de roles (Admin, Cajero, Docente, etc.) y sus permisos asociados.
- **10.3 Permisos** — Catálogo de permisos disponibles por módulo (`recurso.accion`).
- **10.4 Notificaciones del Sistema / Notificaciones** — Envío y consulta de comunicados y alertas internas.
- **10.5 Vendedores** — Registro de vendedores/asesores comerciales y su seguimiento de ventas.

### 11. WhatsApp
- **11.1 Bot WhatsApp — Estado / Cuentas** — Estado de conexión y cuentas configuradas del bot.
- **11.2 Motor NLU / Gestión de Intents** — Configuración de las intenciones que reconoce el bot automático.
- **11.3 Asesores WhatsApp / Conversaciones / Mensajes** — Bandeja de conversaciones atendidas por asesores humanos.
- **11.4 Enviar Mensaje / Plantillas WhatsApp** — Envío manual de mensajes y plantillas aprobadas.
- **11.5 Grupos de WhatsApp** — Enlaces a grupos de WhatsApp por curso para coordinación con estudiantes.
- **11.6 Speech de Ventas** — Guiones de venta sugeridos para asesores.
- **11.7 Efectos Especiales** — Configuración de mensajes automáticos especiales del bot.

### 12. Sitio Público (Portal del Estudiante)
- **12.1 Página de Inicio** — Landing principal con cifras, testimonios y programas destacados.
- **12.2 Cursos y Programas** — Catálogo público de cursos con filtro, buscador y ficha de detalle.
- **12.3 Cursos Anteriores** — Histórico de cursos ya finalizados, visibles como referencia.
- **12.4 Carrito y Checkout** — Proceso de compra/inscripción en línea y confirmación de pago.
- **12.5 Mis Pagos** — Consulta de pagos e inscripciones del estudiante autenticado.
- **12.6 Biblioteca** — Recursos y publicaciones descargables para el público.
- **12.7 Noticias, Comunicados, Blog, Eventos** — Contenido informativo del portal institucional.

---

*Manual en construcción — cada sección del índice se completará con el detalle paso a paso (capturas de pantalla, flujos y casos de uso) en revisiones posteriores.*
