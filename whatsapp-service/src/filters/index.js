import { normalizeText } from '../utils/text.js';

const PALABRAS_MALAS = [
  'mierda','mierd','puta','puto','puta madre','hijo de puta','hdp','concha','conchatumare',
  'carajo','carajos','idiota','imbecil','imbécil','estupido','estúpido','pendejo','pendeja',
  'boludo','pelotudo','culiao','culiado','marica','maricon','maricón','cojudo','cojuda',
  'weon','weona','cabron','cabrón','joder','maldito','maldita','inutil','inútil',
  'basura','asco','verga','pija','pene','culo','trasero','pedo','mamon','mamón',
  'tarado','tarada','zorra','perra','hdlp','hijo de la','la concha','la puta',
  'que mierda','chucha','chupar','jodete','que puta','fuck','shit','bitch','damn','crap','wtf',
  'mdre','ptm','ctm',
];

export function esTextoSinSentido(text) {
  const norm = text.trim().toLowerCase();
  if (norm.length < 3) return false;
  if (/^[^aeiou\s\d]{5,}$/.test(norm)) return true;
  if (/(.)\1{4,}/.test(norm)) return true;
  if (/qwert|asdfg|zxcvb|qazws|poiuy|lkjhg|mnbvc/.test(norm)) return true;
  const soloConsonantes = norm.replace(/[^a-z]/g, '');
  if (soloConsonantes.length > 4 && !/[aeiou]/.test(soloConsonantes)) return true;
  const sinEspacios = norm.replace(/\s/g, '');
  if (sinEspacios.length > 8) {
    const unicos = new Set(sinEspacios).size;
    if (unicos / sinEspacios.length < 0.35) return true;
  }
  return false;
}

export function esMensajeInapropiado(text) {
  const norm = text.toLowerCase().trim();
  return PALABRAS_MALAS.some(p => norm.includes(p));
}

const RESPUESTA_FUERA_DE_TEMA = [
  '🎓 Soy el asistente virtual de *CENEFCO — Centro de Formación Continua*. Solo puedo ayudarte con temas relacionados a nuestra oferta académica: cursos, inscripciones, pagos, docentes, eventos y más.\n\nEscribe *menú* para ver todas las opciones.',
  '😊 Solo puedo responder consultas sobre *CENEFCO*: cursos, inscripciones, pagos, docentes y eventos.\n\nEscribe *menú* si necesitas ayuda.',
  '🎓 Estoy aquí para orientarte sobre los servicios de *CENEFCO*. Por favor escríbeme una consulta relacionada con nuestra oferta académica.\n\nEscribe *menú* para ver las opciones disponibles.',
];

const RESPUESTA_INAPROPIADO = [
  '🙏 Por favor usa un lenguaje respetuoso. Soy el asistente virtual de *CENEFCO* y estoy aquí para ayudarte con cursos, inscripciones y servicios académicos.',
  '😊 Te pido que uses un lenguaje amable. Estoy aquí para orientarte en los servicios de *CENEFCO*. Escribe *menú* para ver las opciones.',
];

export function respuestaFueraDeTema() {
  return RESPUESTA_FUERA_DE_TEMA[Math.floor(Math.random() * RESPUESTA_FUERA_DE_TEMA.length)];
}

export function respuestaInapropiada() {
  return RESPUESTA_INAPROPIADO[Math.floor(Math.random() * RESPUESTA_INAPROPIADO.length)];
}

const CORTESIA = [
  {
    palabras: ['gracias','muchas gracias','grax','grac','gracias totales','thank you','thanks','muy amable','excelente atencion'],
    respuestas: [
      '😊 ¡De nada! Estamos para servirte. ¿Hay algo más en lo que pueda ayudarte?',
      '🙌 ¡Con gusto! Para eso estamos. ¿Necesitas algo más?',
      '😄 ¡A ti por contactarnos! Si tienes otra consulta, aquí estoy.',
    ],
  },
  {
    palabras: ['de nada','no hay de que','ok gracias','okey gracias','listo gracias','ya gracias','entendido gracias','perfecto gracias'],
    respuestas: [
      '😊 ¡Que tengas un excelente día! Si necesitas algo más, escríbeme.',
      '👍 ¡Hasta luego! Estamos a tu disposición cuando lo necesites.',
    ],
  },
  {
    palabras: ['ok','okay','okey','entendido','perfecto','excelente','genial','listo','bien','muy bien','claro','si','sip','dale'],
    respuestas: [
      '👍 ¿Hay algo más en lo que pueda ayudarte?',
      '😊 ¿Necesitas algo más?',
    ],
  },
  {
    palabras: ['adios','hasta luego','chao','bye','nos vemos','hasta pronto','hasta manana','cuídate','que estes bien'],
    respuestas: [
      '👋 ¡Hasta luego! Que tengas un excelente día. Recuerda que puedes escribirnos cuando necesites.',
      '😊 ¡Cuídate mucho! Estamos aquí cuando nos necesites.',
    ],
  },
  {
    palabras: ['buen trabajo','muy util','me ayudaste','me sirvio','me fue util','que bueno','que bien'],
    respuestas: [
      '😄 ¡Qué bueno que te pude ayudar! Para eso estamos.',
      '🙌 ¡Me alegra haberte sido de utilidad! ¿Hay algo más en lo que pueda asistirte?',
    ],
  },
];

export function detectarCortesia(text) {
  const norm = normalizeText(text);
  for (const grupo of CORTESIA) {
    if (grupo.palabras.some(p => norm === p || norm.startsWith(p + ' ') || norm.endsWith(' ' + p) || norm.includes(' ' + p + ' '))) {
      return grupo.respuestas[Math.floor(Math.random() * grupo.respuestas.length)];
    }
  }
  return null;
}
