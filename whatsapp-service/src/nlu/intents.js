/**
 * Intents — estructura completa inspirada en Dialogflow Essentials.
 *
 * Cada intent tiene:
 *   name            → nombre legible (como en Dialogflow console)
 *   slug            → identificador interno
 *   domain          → agrupación temática
 *   priority        → desempate cuando dos intents tienen mismo score
 *   events          → disparadores programáticos ['WELCOME', 'MY_EVENT']
 *   inputContexts   → contextos que DEBEN estar activos para que matchee
 *   outputContexts  → contextos que se ACTIVAN al hacer match [{name, lifespan}]
 *   trainingPhrases → frases de entrenamiento (equivale a "Training phrases")
 *   parameters      → entidades a extraer del texto [{name, entity, required}]
 *   responses       → variantes de respuesta directa (selección aleatoria)
 *                     Si está vacío → el fulfillment handler maneja la respuesta
 *   action          → qué handler ejecutar (fulfillment)
 */

export const INTENTS = [
  {
    name:    'Default Welcome Intent',
    slug:    'saludo',
    domain:  'general',
    priority: 600,
    events:   ['WELCOME'],
    inputContexts:  [],
    outputContexts: [],
    trainingPhrases: [
      'hola', 'buenas', 'buenos dias', 'buen dia', 'buenas tardes', 'buenas noches',
      'hi', 'hello', 'saludos', 'saludos a todos', 'muy buenas', 'muy buenos dias',
      'que tal', 'como estas', 'como te va', 'ey', 'ei', 'oye',
      'holi', 'holis', 'holaa', 'ola', 'ke tal', 'q tal',
      'inicio', 'menu', 'start', 'empezar', 'comenzar', 'volver',
      'quiero informacion', 'necesito informacion', 'buenas quisiera informacion',
      'buenas tardes quisiera informacion sobre sus cursos',
    ],
    parameters: [],
    responses:  [],
    action: 'menu',
  },

  {
    name:    'Bot Presentacion',
    slug:    'presentacion',
    domain:  'general',
    priority: 400,
    events:   [],
    inputContexts:  [],
    outputContexts: [],
    trainingPhrases: [
      'que eres', 'quien eres', 'que haces', 'eres un bot', 'eres humano',
      'eres robot', 'eres una ia', 'como te llamas', 'cuales son tus funciones',
      'en que me ayudas', 'que puedo hacer aqui', 'para que sirves',
      'que tipo de asistente eres', 'como funciona esto',
      'quiero saber que puedes hacer', 'que opciones tienes',
    ],
    parameters: [],
    responses:  [],
    action: 'presentacion',
  },

  {
    name:    'Consulta Cursos',
    slug:    'cursos',
    domain:  'academico',
    priority: 500,
    events:   [],
    inputContexts:  [],
    outputContexts: [{ name: 'cursos-flow', lifespan: 3 }],
    trainingPhrases: [
      'que cursos tienen', 'que programas ofrecen', 'cuales son sus diplomados',
      'quiero ver los cursos disponibles', 'tienen cursos de administracion',
      'que estudiar', 'oferta academica', 'oferta educativa', 'catalogo de cursos',
      'lista de cursos', 'cursos disponibles', 'programas disponibles',
      'que diplomados tienen', 'que capacitaciones ofrecen',
      'quiero aprender', 'quiero capacitarme', 'quiero estudiar',
      'cuanto dura el diplomado', 'cuantas horas tiene el curso',
      'es presencial o virtual', 'modalidad del curso', 'modalidad semipresencial',
      'que certificado dan', 'que titulo otorgan', 'tienen certificacion',
      'me interesan sus cursos', 'informacion sobre cursos',
      'que clases tienen', 'que pueden ensenarme', 'que areas de estudio tienen',
      'tienen algo de marketing', 'tienen algo de administracion',
      'tienen algo de salud', 'tienen algo de derecho', 'tienen algo de educacion',
      'cursos en linea', 'cursos online', 'formacion continua',
    ],
    parameters: [],
    responses:  [],
    action: 'cursos',
  },

  {
    name:    'Consulta Cursos - Me Interesa',
    slug:    'cursos_interes',
    domain:  'academico',
    priority: 650,
    events:   [],
    inputContexts:  ['cursos-flow'],
    outputContexts: [{ name: 'cursos-flow', lifespan: 2 }, { name: 'inscripcion-flow', lifespan: 3 }],
    trainingPhrases: [
      'ese me interesa', 'me interesa ese curso', 'quiero ese',
      'dime mas de ese', 'mas informacion', 'cuanto cuesta ese',
      'como me inscribo en ese', 'quiero inscribirme en ese',
      'el primero', 'el segundo', 'el tercero', 'el cuarto', 'el quinto',
      'si ese quiero', 'me anoto en ese', 'quiero postular',
    ],
    parameters: [],
    responses: [
      '¡Excelente elección! 😊\n\nPara completar tu inscripción necesito algunos datos.\n\n¿Cuál es tu *nombre completo*?',
      'Perfecto, te ayudo con la inscripción. 📝\n\n¿Cuál es tu *nombre completo*?',
      '¡Genial! Vamos a iniciar tu proceso de inscripción. 🎓\n\n¿Cuál es tu *nombre completo*?',
    ],
    action: 'inscripcion_flow',
  },

  {
    name:    'Proceso Inscripcion',
    slug:    'inscripciones',
    domain:  'academico',
    priority: 500,
    events:   [],
    inputContexts:  [],
    outputContexts: [{ name: 'inscripcion-flow', lifespan: 3 }],
    trainingPhrases: [
      'como me inscribo', 'quiero inscribirme', 'como me registro',
      'proceso de inscripcion', 'requisitos para inscribirse',
      'que documentos necesito', 'documentos para inscripcion',
      'donde me inscribo', 'cuando cierran inscripciones',
      'fecha limite de inscripcion', 'todavia hay cupos',
      'quedan vacantes', 'hay plazas disponibles',
      'como puedo postular', 'quiero anotarme', 'quiero postular',
      'como accedo al curso', 'como entro al programa',
      'formulario de inscripcion', 'llenar formulario',
      'inscripcion en linea', 'inscripcion online', 'preinscripcion',
      'que documentos piden', 'que papeles necesito',
    ],
    parameters: [],
    responses:  [],
    action: 'inscripciones',
  },

  {
    name:    'Inscripcion - Confirmar',
    slug:    'inscripcion_confirmar',
    domain:  'academico',
    priority: 650,
    events:   [],
    inputContexts:  ['inscripcion-flow'],
    outputContexts: [],
    trainingPhrases: [
      'si', 'si por favor', 'dale', 'listo', 'procede',
      'adelante', 'procedemos', 'confirmado', 'si inscribeme',
      'si me anoto', 'va', 'vamos', 'de acuerdo', 'correcto',
      'si asi es', 'afirmativo', 'claro que si',
      'acepto', 'confirmo', 'esta bien', 'perfecto listo',
    ],
    parameters: [],
    responses: [
      '✅ ¡Perfecto! Para completar tu inscripción, comunícate con nosotros:\n\n' +
      '📞 *Teléfono/WhatsApp:* envía un mensaje a este número\n' +
      '📧 *Email:* info@cenefco.edu.bo\n' +
      '🏢 *Presencial:* visítanos en nuestras oficinas en horario de atención.\n\n' +
      '¿Hay algo más en que pueda ayudarte?',
      '📝 Anotado. Para formalizar tu inscripción puedes:\n\n' +
      '• *Visitar nuestras oficinas* en horario de atención\n' +
      '• *Escribir al email* info@cenefco.edu.bo\n' +
      '• *Llamarnos* durante el horario hábil\n\n' +
      'Un asesor te guiará en los siguientes pasos. 😊',
    ],
    action: 'inscripcion_confirmar',
  },

  {
    name:    'Consulta Pagos',
    slug:    'pagos',
    domain:  'academico',
    priority: 500,
    events:   [],
    inputContexts:  [],
    outputContexts: [{ name: 'pagos-flow', lifespan: 3 }],
    trainingPhrases: [
      'cuanto cuesta', 'cual es el precio', 'cuanto vale',
      'cuanto cobran', 'cual es el costo', 'cuanto es la cuota',
      'cuanto es la mensualidad', 'cuanto es la matricula',
      'como pago', 'formas de pago', 'metodos de pago',
      'se puede pagar en cuotas', 'acepta tarjeta',
      'pago en linea', 'deposito bancario', 'transferencia bancaria',
      'cuando vence el pago', 'fecha de pago', 'fechas de pago',
      'tengo una deuda', 'mi estado de cuenta', 'cuanto debo',
      'cuota pendiente', 'pagos pendientes', 'mi pago',
      'tienen becas', 'dan descuentos', 'media beca', 'precio especial',
      'es muy caro', 'sale caro', 'no es barato', 'resulta costoso',
    ],
    parameters: [],
    responses:  [],
    action: 'pagos',
  },

  {
    name:    'Pagos - Metodo Especifico',
    slug:    'pagos_metodo',
    domain:  'academico',
    priority: 650,
    events:   [],
    inputContexts:  ['pagos-flow'],
    outputContexts: [],
    trainingPhrases: [
      'efectivo', 'en cash', 'pago en efectivo',
      'tarjeta de credito', 'tarjeta de debito', 'con tarjeta',
      'transferencia', 'deposito', 'por transferencia',
      'qr', 'pago qr', 'codigo qr',
      'tigo money', 'billetera movil',
      'cuotas', 'a plazos', 'financiamiento', 'en cuotas mensuales',
      'tienen descuento al contado',
    ],
    parameters: [],
    responses: [
      '💳 Aceptamos los siguientes métodos de pago:\n\n' +
      '• 💵 *Efectivo* (en oficina)\n' +
      '• 📱 *QR / Billetera móvil* (Tigo Money, SimpleBank)\n' +
      '• 🏦 *Transferencia bancaria* (BNB, BCP, Banco Unión)\n' +
      '• 💳 *Tarjeta de débito/crédito*\n' +
      '• 📅 *Pago en cuotas* (consultar condiciones con un asesor)\n\n' +
      'Para consultar el precio específico de un curso, escribe *"pagos"* o comunícate con nosotros. 😊',
      '🏦 Manejamos múltiples formas de pago para tu comodidad:\n\n' +
      '✅ Efectivo · ✅ QR · ✅ Transferencia · ✅ Tarjeta\n\n' +
      'También existe la opción de *cuotas mensuales* según el programa.\n' +
      'Consulta con un asesor los detalles de financiamiento. 😊',
    ],
    action: 'pagos_metodo',
  },

  {
    name:    'Consulta Docentes',
    slug:    'docentes',
    domain:  'academico',
    priority: 400,
    events:   [],
    inputContexts:  [],
    outputContexts: [{ name: 'docentes-flow', lifespan: 2 }],
    trainingPhrases: [
      'quien da las clases', 'quienes son los profesores',
      'quien imparte el curso', 'cuerpo docente', 'planta docente',
      'curriculum del profesor', 'experiencia del docente',
      'perfil del docente', 'que titulo tiene el docente',
      'quien ensena', 'instructores', 'facilitadores',
      'son buenos los profesores', 'que formacion tiene el docente',
      'que profesores tienen', 'quienes son sus docentes',
      'profesores disponibles', 'quienes dan clases',
      'que profesores dan los cursos', 'listado de profesores',
      'datos del profesor', 'nombre del instructor',
    ],
    parameters: [],
    responses:  [],
    action: 'docentes',
  },

  {
    name:    'Consulta Noticias',
    slug:    'noticias',
    domain:  'contenido',
    priority: 300,
    events:   [],
    inputContexts:  [],
    outputContexts: [],
    trainingPhrases: [
      'que hay de nuevo', 'ultimas noticias', 'novedades',
      'noticias del cenefco', 'que paso', 'noticias recientes',
      'actualizaciones', 'noticias de hoy', 'algo nuevo',
      'publicaciones recientes', 'nota de prensa', 'blog',
    ],
    parameters: [],
    responses:  [],
    action: 'noticias',
  },

  {
    name:    'Consulta Boletines',
    slug:    'boletines',
    domain:  'contenido',
    priority: 300,
    events:   [],
    inputContexts:  [],
    outputContexts: [],
    trainingPhrases: [
      'boletin', 'boletines', 'boletin academico', 'boletin cenefco',
      'boletin mensual', 'ultimo boletin', 'publicaciones periodicas',
      'revista', 'circular', 'comunicado', 'informe periodico',
    ],
    parameters: [],
    responses:  [],
    action: 'boletines',
  },

  {
    name:    'Consulta Eventos',
    slug:    'eventos',
    domain:  'contenido',
    priority: 400,
    events:   [],
    inputContexts:  [],
    outputContexts: [],
    trainingPhrases: [
      'que eventos hay', 'proximos eventos', 'actividades programadas',
      'hay algo este fin de semana', 'que hay este sabado',
      'seminarios disponibles', 'talleres proximos', 'conferencias',
      'webinar', 'charlas', 'jornada academica', 'graduacion',
      'ceremonia de graduacion', 'acto de graduacion',
      'calendario de eventos', 'agenda de actividades',
      'que pasa este mes', 'que tienen programado',
    ],
    parameters: [],
    responses:  [],
    action: 'eventos',
  },

  {
    name:    'Consulta Horarios',
    slug:    'horario',
    domain:  'general',
    priority: 400,
    events:   [],
    inputContexts:  [],
    outputContexts: [],
    trainingPhrases: [
      'horario de atencion', 'a que hora atienden', 'hasta que hora',
      'cuando atienden', 'horario de oficina', 'dias habiles',
      'atienden sabado', 'atienden domingo', 'atienden hoy',
      'esta abierto ahora', 'esta cerrado', 'cuando abren',
      'desde que hora trabajan', 'horario laboral',
      'horario de clases', 'que dias tienen clases', 'estan abiertos',
    ],
    parameters: [],
    responses:  [],
    action: 'horario',
  },

  {
    name:    'Consulta Ubicacion',
    slug:    'ubicacion',
    domain:  'general',
    priority: 400,
    events:   [],
    inputContexts:  [],
    outputContexts: [],
    trainingPhrases: [
      'donde estan', 'donde queda', 'como llego', 'como llegar',
      'la direccion', 'cual es la direccion', 'donde se ubican',
      'donde funciona el cenefco', 'en que barrio estan',
      'que transporte tomo', 'que micro va', 'que trufi va',
      'mapa', 'pin de ubicacion', 'google maps', 'coordenadas',
      'como voy hasta ahi', 'ruta para llegar',
    ],
    parameters: [],
    responses:  [],
    action: 'ubicacion',
  },

  {
    name:    'Solicitar Soporte Humano',
    slug:    'soporte',
    domain:  'general',
    priority: 700,
    events:   [],
    inputContexts:  [],
    outputContexts: [],
    trainingPhrases: [
      'hablar con una persona', 'quiero hablar con alguien',
      'atencion personalizada', 'atencion humana',
      'comunicarme con un asesor', 'necesito asesor',
      'hablar con alguien de verdad', 'hablar con humano',
      'tengo una queja', 'quiero quejarme', 'quiero hacer un reclamo',
      'no entiendo nada', 'estoy confundido', 'necesito ayuda urgente',
      'problema urgente', 'hay alguien que me atienda',
      'me puede atender alguien', 'atencion al cliente',
    ],
    parameters: [],
    responses:  [],
    action: 'soporte',
  },

];
