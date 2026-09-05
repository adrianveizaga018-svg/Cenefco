import { normalizeText } from '../utils/text.js';

export const KEYWORDS = {
  presentacion: [
    'que eres','quien eres','que haces','que puedes hacer','para que sirves','como funciona',
    'como te llamas','eres un bot','eres humano','eres robot','eres una ia','eres inteligencia',
    'que tipo de asistente','cuales son tus funciones','cuales son tus capacidades',
    'que me puedes decir','en que me ayudas','que servicios ofreces','que puedo hacer aqui',
    'que ofreces','bot','asistente virtual','asistente cenefco','asistente academico',
  ],
  saludo: [
    'hola','buenas','buenos','buen dia','buenas tardes','buenas noches',
    'hi','hello','saludos','inicio','menu','start','empezar','comenzar',
    'ke tal','q tal','que tal','como estas','muy buenas','ey','ei','oye',
    'holi','holis','holaa','ola ','buenas a todos','saludos a todos',
  ],
  cursos: [
    'curso','cursos','programa','programas','diplomado','diplomados','capacitacion',
    'capacitaciones','formacion','oferta academica','oferta educativa','que cursos',
    'que programas','cursos disponibles','programas disponibles','clases','asignatura',
    'asignaturas','materia','materias','que ensenan','que ensenian','que estudiar',
    'quiero estudiar','quiero aprender','quiero capacitarme','modalidad','semipresencial',
    'presencial','virtual','en linea','online','carga horaria','duracion del curso',
    'cuanto dura','cuanto tiempo','cuantas horas','catalogo de cursos','lista de cursos',
    'que carrera','que puedo estudiar','ofrecen cursos','tienen cursos','cursos que tienen',
    'certificado de asistencia','certificado de aprobacion','que certifican','que titulo',
    'cursos con certificado','diplomado con titulo','que clases','que materias',
  ],
  inscripciones: [
    'inscripcion','inscripciones','inscribirme','inscribirse','como me inscribo',
    'quiero inscribirme','registrarme','registro','preinscripcion','pre-inscripcion',
    'como me registro','proceso de inscripcion','requisitos inscripcion',
    'que necesito para inscribirme','documentos inscripcion','donde me inscribo',
    'fecha de inscripcion','cuando inscribo','plazo de inscripcion','cupos',
    'hay cupo','quedan cupos','iniciar inscripcion','abrir inscripcion','formulario inscripcion',
    'llenar formulario','postular','como postular','plazas disponibles','vacantes',
  ],
  pagos: [
    'pago','pagos','costo','costos','precio','precios','cuanto cuesta','cuanto vale',
    'cuanto cobran','cuanto es','monto','aranceles','arancel','matricula','cuota',
    'cuotas','mensualidad','mensualidades','boleta','deposito','fecha de pago',
    'fechas de pago','vencimiento','vence','cuando vence','cobran','cobros',
    'formas de pago','metodos de pago','como pago','donde pago','pago en linea',
    'transferencia','deposito bancario','pago con tarjeta','descuento','becas',
    'beca','media beca','rebaja','precio especial','es caro','es barato','gratuito',
    'gratis','libre de costo','sin costo','tengo deuda','mi deuda','cuanto debo',
    'estado de cuenta','mi cuenta','cuota pendiente','pagos pendientes',
  ],
  docentes: [
    'docente','docentes','profesor','profesores','maestro','maestros','instructor',
    'instructores','facilitador','facilitadores','quien ensena','quien enseña',
    'quien da clases','quien imparte','cuerpo docente','planta docente','equipo docente',
    'especialistas','expertos','quien es el profe','perfil del docente','curriculo',
    'curriculum','hoja de vida','experiencia del docente','formacion del docente',
    'titulo del docente','grado academico del docente','coordinador','coordinadores',
  ],
  noticias: [
    'noticia','noticias','novedad','novedades','que hay de nuevo','actualizaciones',
    'informacion reciente','ultimas noticias','noticias del cenefco','que paso',
    'noticias de hoy','algo nuevo','comunicacion','prensa','blog','articulo',
    'publicacion','nota de prensa','boletin informativo',
  ],
  boletines: [
    'boletin','boletines','publicacion periodica','revista','informe periodico',
    'boletin cenefco','boletin academico','publicaciones','ultimo boletin',
    'boletin mensual','informativo','circular','comunicado interno',
  ],
  eventos: [
    'evento','eventos','actividad','actividades','agenda','que actividades hay',
    'que eventos hay','proximos eventos','actos','ceremonias','ferias','feria','seminario',
    'seminarios','taller','talleres','conferencia','conferencias','webinar','charla',
    'charlas','jornada','jornadas','simposio','congreso','convenio','graduacion',
    'acto de graduacion','que pasa este','que hay este','sabado hay algo','que esta programado',
    'calendario academico','calendario de eventos',
  ],
  horario: [
    'horario','horarios','que hora','a que hora','hora de atencion','abierto','atienden',
    'cuando atienden','abren','cierran','disponible','horas de trabajo','dias de trabajo',
    'dias habiles','hasta que hora','desde que hora','manana atienden','sabados atienden',
    'domingos atienden','tienen atencion','esta abierto','esta cerrado','cuando puedo ir',
    'que dias atienden','dias de atencion','horario de oficina','horario laboral',
    'atienden hoy','hoy atienden','atienden sabado','a que horas abren','a que horas cierran',
    'en que horario atienden','que horario tienen','horario academico','horario de clases',
    'que dias dan clases',
  ],
  ubicacion: [
    'ubicacion','direccion','donde','donde queda','donde esta','como llegar','como llego',
    'lugar','mapa','llegar','sede','instalaciones','domicilio','ruta para llegar',
    'que bus','que micro','que trufi','que transporte','barrio','zona','calle',
    'pin de ubicacion','coordenadas','como voy','por donde queda','google maps',
    'maps','en que barrio','en que zona','en que calle','cerca de','transporte publico',
    'donde funciona cenefco','donde queda el cenefco','cenefco cochabamba','cochabamba',
  ],
  soporte: [
    'ayuda','soporte','asesor','hablar','persona','humano','hablar con alguien','me ayuden',
    'necesito ayuda','quiero hablar','asesoria','orientacion','me orienten','no entiendo',
    'tengo dudas','tengo problemas','no puedo','operador','atencion personalizada',
    'hablar con humano','quiero asesoria','necesito orientacion','tengo una queja',
    'quiero quejarme','denuncia','reclamo','queja','problema urgente','inconveniente',
    'no entiendo nada','no comprendo','estoy confundido','me confundi',
    'necesito que me expliquen','explicame','no se como','hablar con alguien de verdad',
    'atencion humana','comunicarme con alguien',
  ],
};

export const INTENT_PATTERNS = [
  // Cursos
  { re: /cuanto (dura|tiempo|horas?) (el |la )?(curso|programa|diplomado|capacitacion)/, intent: 'cursos' },
  { re: /(que|cuales?) (cursos?|programas?|diplomados?|capacitaciones?) (tienen|hay|ofrecen|estan disponibles)/, intent: 'cursos' },
  { re: /(quiero|necesito|quisiera) (estudiar|aprender|capacitarme|formarme)/, intent: 'cursos' },
  { re: /(tienen|ofrecen|hay) (cursos?|programas?|diplomados?) (de |en )?/, intent: 'cursos' },
  { re: /(modalidad|es presencial|es virtual|es online|es semipresencial)/, intent: 'cursos' },
  { re: /(que|cual) (certificado|titulo|diploma|constancia) (dan|otorgan|entregan)/, intent: 'cursos' },
  // Inscripciones
  { re: /como (me )?(inscribo|registro|postulo|anoto)/, intent: 'inscripciones' },
  { re: /(quiero|necesito|deseo) (inscribirme|registrarme|postular|anotarme)/, intent: 'inscripciones' },
  { re: /proceso de (inscripcion|registro|admision)/, intent: 'inscripciones' },
  { re: /(fecha|plazo|hasta cuando) (de |para )?(inscripcion|registro|postulacion)/, intent: 'inscripciones' },
  { re: /(hay|quedan?) (cupos?|plazas?|vacantes?)/, intent: 'inscripciones' },
  { re: /que (documentos?|requisitos?|papeles?) (piden|necesito|se necesita) para (inscribirme|registrarme)/, intent: 'inscripciones' },
  // Pagos
  { re: /cuanto (cuesta|vale|cobran?|sale|es el costo|es el precio|es la cuota)/, intent: 'pagos' },
  { re: /(fecha|plazo|cuando|hasta cuando) (de |para )?(pago|pagar|vencimiento)/, intent: 'pagos' },
  { re: /como (pago|puedo pagar|hago el pago)/, intent: 'pagos' },
  { re: /(formas?|metodos?|maneras?) de pago/, intent: 'pagos' },
  { re: /(hay|tienen) (becas?|descuentos?|media beca|precio especial)/, intent: 'pagos' },
  { re: /(cuota|mensualidad|matricula) (pendiente|vencida|proxima)/, intent: 'pagos' },
  // Docentes
  { re: /(quien|quienes?) (ensenan?|ensena?|imparte|imparten|da|dan) (las? |el )?(clases?|cursos?|materias?)/, intent: 'docentes' },
  { re: /(cuerpo|planta|equipo) docente/, intent: 'docentes' },
  { re: /(perfil|curriculum|experiencia) del? docente/, intent: 'docentes' },
  // Horario
  { re: /(hasta|desde|a que?) (hora|horas)/, intent: 'horario' },
  { re: /horario(s)? (de )?(atencion|oficina|trabajo|clases|apertura)?/, intent: 'horario' },
  { re: /(atienden|abren|cierran|trabajan|funcionan) (hoy|manana|sabado|domingo|el fin de semana)?/, intent: 'horario' },
  { re: /esta(n)? (abierto|cerrado|atendiendo)/, intent: 'horario' },
  // Ubicación
  { re: /(como|de que forma|por donde|que camino) (llego|llegamos|voy|vamos|llegar)/, intent: 'ubicacion' },
  { re: /(donde|adonde|en que (lugar|calle|zona|barrio)) (esta|queda|se encuentra|funciona|esta ubicado)/, intent: 'ubicacion' },
  { re: /(que bus|que micro|que trufi|que transporte|que linea) (tomo|va|llega|pasa)/, intent: 'ubicacion' },
  // Soporte
  { re: /(hablar|comunicarme|contactar) (directamente )?(con )?(una persona|alguien|un asesor|atencion|un humano)/, intent: 'soporte' },
  { re: /(tengo un?a?|quiero hacer una?|necesito hacer una?) (queja|reclamo|consulta personal)/, intent: 'soporte' },
  { re: /no (entiendo|comprendo|se como|puedo con) (nada|esto|el proceso)/, intent: 'soporte' },
  // Eventos
  { re: /(hay|tienen|cuando|proximo|proximos) (eventos?|actividades?|talleres?|seminarios?|charlas?)/, intent: 'eventos' },
  { re: /que (hay|pasa|tienen) (este |el )?(fin de semana|sabado|domingo|semana|mes)/, intent: 'eventos' },
  // Noticias
  { re: /(ultimas?|recientes?) (noticias?|novedades?|actualizaciones?)/, intent: 'noticias' },
  // Boletines
  { re: /(ultimo|nuevo) boletin/, intent: 'boletines' },
  { re: /boletin (academico|cenefco|mensual|informativo)/, intent: 'boletines' },
];

function _scoreIntents(text) {
  const norm = normalizeText(text);
  const scores = new Map();
  const bump = (intent, pts) => scores.set(intent, (scores.get(intent) || 0) + pts);
  for (const { re, intent } of INTENT_PATTERNS) {
    if (re.test(norm)) bump(intent, 2);
  }
  for (const [intent, words] of Object.entries(KEYWORDS)) {
    const hits = words.filter(w => norm.includes(w)).length;
    if (hits > 0) bump(intent, hits);
  }
  return scores;
}

export function detectKeywordIntents(text) {
  return [..._scoreIntents(text).entries()]
    .sort((a, b) => b[1] - a[1])
    .map(([intent]) => intent);
}

export function getDominantIntent(text) {
  const scores = _scoreIntents(text);
  if (scores.size === 0) return null;
  if (scores.size === 1) return [...scores.keys()][0];
  const sorted = [...scores.entries()].sort((a, b) => b[1] - a[1]);
  return sorted[0][1] >= sorted[1][1] * 2 ? sorted[0][0] : null;
}
