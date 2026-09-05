import { getPool, getBotSettings, getBotIaSettings } from './store.js';
import { getSpeechesContext } from './handlers/speeches.js';

const CFG = {
  host:      process.env.OLLAMA_HOST  || 'http://localhost:11434',
  model:     process.env.OLLAMA_MODEL || 'llama3.2:1b',
  rateLimit: 20,
};

const FORBIDDEN = [
  'ignore previous','forget instructions','system prompt','ignore all',
  'new instructions','you are now','act as','pretend you','jailbreak',
  'dan mode','ignore your','disregard','override','bypass','do anything now',
  'sin restricciones','ignora tus instrucciones','olvida tus instrucciones',
  'actua como','finge ser',
];

const rateLimitMap = new Map();

setInterval(() => {
  const cutoff = Date.now() - 60_000;
  for (const [key, entry] of rateLimitMap) {
    if (entry.start < cutoff) rateLimitMap.delete(key);
  }
}, 10 * 60_000);

function checkRateLimit(phone) {
  const now = Date.now();
  const windowMs = 60_000;
  const entry = rateLimitMap.get(phone) ?? { count: 0, start: now };
  if (now - entry.start > windowMs) {
    rateLimitMap.set(phone, { count: 1, start: now });
    return true;
  }
  entry.count++;
  rateLimitMap.set(phone, entry);
  return entry.count <= CFG.rateLimit;
}

function sanitize(input, maxChars = 500) {
  return input.replace(/<[^>]*>/g, '').trim().substring(0, maxChars);
}

function esJailbreak(input) {
  const norm = input.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/\s+/, ' ').trim();
  return FORBIDDEN.some(p => norm.includes(p));
}

// Palabras en scope: consultas académicas de CENEFCO
const SCOPE_CENEFCO = [
  'curso','cursos','programa','programas','diplomado','diplomados','capacitacion','capacitaciones',
  'inscripcion','inscripciones','inscribirme','registro','preinscripcion','matricula',
  'pago','pagos','costo','costos','precio','cuota','cuotas','mensualidad','arancel',
  'docente','docentes','profesor','profesores','instructor','facilitador','cuerpo docente',
  'certificado','titulo','diploma','constancia','aprobacion','participacion',
  'evento','eventos','actividad','actividades','taller','seminario','conferencia','webinar',
  'noticia','noticias','boletin','boletines','publicacion',
  'horario','horarios','atencion','direccion','ubicacion','sede',
  'cenefco','formacion continua','academia','academico','academica','educacion','estudio',
  'clase','clases','asignatura','materia','periodo','gestion','modalidad','virtual','presencial',
  'bolivia','cochabamba','boliviano','boliviana',
];

// Palabras fuera de scope
const SCOPE_EXCLUIDO = [
  'restaurante','hotel','hostal','turismo','turista','atracciones','que visitar',
  'pasear','entretenimiento','diversion','receta','cocinar','pelicula','serie','musica',
  'cancion','deporte','futbol','partido','comprar ropa','centro comercial','shopping',
  'moda','tecnologia general','viaje','vuelo','bus interprovincial','precio vuelo',
];

function esConsultaFueraDeScopeIA(input) {
  const norm = input.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
  const tieneExcluido = SCOPE_EXCLUIDO.some(p => norm.includes(p));
  const tieneCenefco = SCOPE_CENEFCO.some(p => norm.includes(p));
  return tieneExcluido && !tieneCenefco;
}

const contextCache = new Map();

setInterval(() => {
  const cutoff = Date.now() - 300_000;
  for (const [key, entry] of contextCache) {
    if (entry.ts < cutoff) contextCache.delete(key);
  }
}, 15 * 60_000);

async function buildContext() {
  const cached = contextCache.get('_general');
  if (cached && Date.now() - cached.ts < 300_000) return cached.value;

  const db  = getPool();
  const BOT = await getBotSettings();

  // Cursos activos
  const { rows: cursos } = await db.query(
    `SELECT m.nombre AS nombre_curso, m.nombremat, m.carga_horaria, m.modalidad,
            i.periodo, i.gestion, i.imparte_fecha_inicio, i.imparte_fecha_fin
     FROM t_imparte i
     JOIN t_materia m ON m.id_mat = i.id_mat AND m.id_us_reg = i.id_us_reg
     WHERE i.estado = 1
     ORDER BY i.imparte_fecha_inicio DESC LIMIT 10`
  ).catch(() => ({ rows: [] }));

  const modalidades = { 0: 'Presencial', 1: 'Virtual', 2: 'Semipresencial' };
  const cursosCtx = cursos.map(c => {
    const nombre = c.nombre_curso || c.nombremat || 'Curso';
    let linea = `- ${nombre}`;
    if (c.periodo) linea += ` | Periodo: ${c.periodo}${c.gestion ? '/' + c.gestion : ''}`;
    if (c.carga_horaria && c.carga_horaria !== '0') linea += ` | ${c.carga_horaria} horas`;
    linea += ` | Modalidad: ${modalidades[c.modalidad] ?? 'Presencial'}`;
    return linea;
  }).join('\n');

  // Noticias recientes
  const { rows: noticias } = await db.query(
    `SELECT titulo, entradilla, fecha_publicacion FROM t_articulo
     WHERE estado_web = 'publicado' AND estado = 1 AND deleted_at IS NULL
     ORDER BY fecha_publicacion DESC LIMIT 5`
  ).catch(() => ({ rows: [] }));

  const noticiasCtx = noticias.map(n => {
    const fecha = n.fecha_publicacion ? new Date(n.fecha_publicacion).toLocaleDateString('es-BO') : '';
    return `- ${n.titulo}${fecha ? ' (' + fecha + ')' : ''}${n.entradilla ? ': ' + n.entradilla.substring(0, 100) : ''}`;
  }).join('\n');

  // Próximos eventos
  const { rows: eventos } = await db.query(
    `SELECT titulo, fecha_inicio, lugar, gratuito, precio FROM web_evento
     WHERE estado = 'publicado' AND fecha_inicio >= NOW() AND deleted_at IS NULL
     ORDER BY fecha_inicio ASC LIMIT 5`
  ).catch(() => ({ rows: [] }));

  const eventosCtx = eventos.map(e => {
    let linea = `- ${e.titulo}`;
    if (e.fecha_inicio) linea += ` | Fecha: ${new Date(e.fecha_inicio).toLocaleDateString('es-BO')}`;
    if (e.lugar) linea += ` | Lugar: ${e.lugar}`;
    linea += e.gratuito ? ' | Gratuito' : (e.precio ? ` | Bs. ${parseFloat(e.precio).toFixed(2)}` : '');
    return linea;
  }).join('\n');

  // Speeches de ventas (script del asistente para objeciones, cierres, etc.)
  const speechesCtx = await getSpeechesContext().catch(() => null);

  const value = `INSTITUCIÓN: ${BOT.nombre} — Centro de Formación Continua
DIRECCIÓN: ${BOT.direccion} — Referencia: ${BOT.referencia}
MAPA: ${BOT.maps}
HORARIOS DE ATENCIÓN: ${BOT.horarios}
TELÉFONO: ${BOT.telefono}
EMAIL: ${BOT.email}
WEB: ${BOT.web}

CURSOS Y PROGRAMAS ACTIVOS:
${cursosCtx || '(sin cursos activos registrados)'}

NOTICIAS RECIENTES:
${noticiasCtx || '(sin noticias)'}

PRÓXIMOS EVENTOS:
${eventosCtx || '(sin eventos próximos)'}

SCRIPTS DE VENTAS Y ATENCIÓN (úsalos como guía para responder objeciones, precios, cierres e inscripciones):
${speechesCtx || '(sin scripts configurados)'}`;

  contextCache.set('_general', { value, ts: Date.now() });
  return value;
}

async function buildSystemPrompt(contexto) {
  const [bot, ia] = await Promise.all([getBotSettings(), getBotIaSettings()]);

  const base = (ia.system_prompt_base ?? '')
    .replace('{nombre}', bot.nombre)
    .trim();

  const reglasExtra = ia.reglas_extra?.trim()
    ? '\n\nREGLAS ADICIONALES:\n' + ia.reglas_extra.trim()
    : '';

  return `${base}${reglasExtra}\n\nCONTEXTO DE LA INSTITUCIÓN:\n${contexto}`;
}

async function getHistorial(phone, excludeInput) {
  const db = getPool();
  const { rows } = await db.query(
    `SELECT direccion, contenido FROM whatsapp_mensajes
     WHERE phone = $1 AND contenido IS NOT NULL AND contenido != $2
     ORDER BY created_at DESC LIMIT 6`,
    [phone, excludeInput]
  );
  return rows.reverse();
}

export async function responder(phone, userInput) {
  const iaQuick = await getBotIaSettings();
  const input = sanitize(userInput, iaQuick.max_input_chars);

  if (!checkRateLimit(phone)) {
    return '⏳ Estás enviando muchos mensajes. Por favor espera un momento.';
  }

  if (esJailbreak(input)) {
    console.warn('[Agent] Jailbreak bloqueado:', phone, input);
    return 'Lo siento, no puedo procesar esa solicitud.';
  }

  if (esConsultaFueraDeScopeIA(input)) {
    return 'Solo puedo ayudarte con consultas sobre los servicios académicos de *CENEFCO*. 😊\nEscribe *menú* para ver todas las opciones disponibles.';
  }

  let contexto, prompt;
  try {
    contexto = await buildContext();
    prompt = await buildSystemPrompt(contexto);
  } catch (err) {
    console.error('[Agent] Error al construir contexto:', err.message);
    return null;
  }

  const historial = await getHistorial(phone, input);
  const messages = historial.map(m => ({
    role: m.direccion === 'entrante' ? 'user' : 'assistant',
    content: m.contenido,
  }));
  messages.push({ role: 'user', content: input });

  const ia     = await getBotIaSettings();
  const inicio = Date.now();
  let output = null;

  try {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), ia.timeout_segundos * 1000);

    const res = await fetch(`${CFG.host}/api/chat`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      signal: controller.signal,
      body: JSON.stringify({
        model: CFG.model,
        messages: [{ role: 'system', content: prompt }, ...messages],
        stream: false,
        options: {
          temperature: ia.temperature,
          num_predict: ia.max_tokens,
        },
      }),
    });

    clearTimeout(timer);

    if (!res.ok) throw new Error(`Ollama HTTP ${res.status}`);

    const data = await res.json();
    output = data.message?.content?.trim() ?? null;

  } catch (err) {
    console.error('[Agent] Error Ollama:', err.message);
    return null;
  }

  const latencia = Date.now() - inicio;
  console.log(`[Agent] Respuesta IA en ${latencia}ms`);
  return output || null;
}
