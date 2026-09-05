import pg from 'pg';

const { Pool } = pg;

let pool = null;

export function getPool() {
  if (!pool) {
    pool = new Pool({
      host:     process.env.DB_HOST     || 'localhost',
      port:     parseInt(process.env.DB_PORT || '5432'),
      database: process.env.DB_DATABASE,
      user:     process.env.DB_USERNAME,
      password: process.env.DB_PASSWORD,
      max:      10,
    });
  }
  return pool;
}

export async function testConnection() {
  const client = await getPool().connect();
  client.release();
  console.log('[DB] Conexión a PostgreSQL exitosa —', process.env.DB_DATABASE);
}


const _settingsCache = new Map();
const SETTINGS_TTL   = 5 * 60 * 1000;

async function readSettingsGroup(group) {
  const cached = _settingsCache.get(group);
  if (cached && Date.now() - cached.ts < SETTINGS_TTL) return cached.data;

  const { rows } = await getPool().query(
    'SELECT name, payload FROM settings WHERE "group" = $1',
    [group]
  );
  const data = {};
  for (const row of rows) {
    try { data[row.name] = JSON.parse(row.payload); } catch { data[row.name] = row.payload; }
  }
  _settingsCache.set(group, { data, ts: Date.now() });
  return data;
}

export function invalidateSettingsCache(group) {
  if (group) _settingsCache.delete(group);
  else _settingsCache.clear();
}

let _configSitioCache = null;
let _configSitioCacheTs = 0;
const CONFIG_SITIO_TTL = 5 * 60 * 1000;

async function readConfigSitio() {
  if (_configSitioCache && Date.now() - _configSitioCacheTs < CONFIG_SITIO_TTL) {
    return _configSitioCache;
  }
  try {
    const { rows } = await getPool().query(
      `SELECT nombre, slogan, email_contacto, telefono, whatsapp_numero,
              direccion, ciudad, latitud, longitud, horario_atencion
       FROM config_sitio WHERE activo = true LIMIT 1`
    );
    _configSitioCache = rows[0] ?? {};
  } catch {
    _configSitioCache = {};
  }
  _configSitioCacheTs = Date.now();
  return _configSitioCache;
}

export function invalidateConfigSitioCache() {
  _configSitioCache = null;
  _configSitioCacheTs = 0;
}

export async function getBotSettings() {
  const [s, cfg] = await Promise.all([readSettingsGroup('bot'), readConfigSitio()]);
  return {
    nombre:     s.nombre     ?? cfg.nombre             ?? process.env.BOT_NOMBRE    ?? 'CENEFCO',
    sigla:      s.sigla      ?? 'CENEFCO',
    telefono:   s.telefono   ?? cfg.telefono            ?? process.env.BOT_TELEFONO  ?? '',
    whatsapp:   s.whatsapp   ?? cfg.whatsapp_numero     ?? '',
    email:      s.email      ?? cfg.email_contacto      ?? process.env.BOT_EMAIL     ?? '',
    web:        s.web        ?? process.env.BOT_WEB     ?? 'https://www.cenefco.com',
    direccion:  s.direccion  ?? cfg.direccion           ?? process.env.BOT_UBICACION_DIRECCION  ?? '',
    ciudad:     cfg.ciudad   ?? 'Cochabamba',
    referencia: s.referencia ?? process.env.BOT_UBICACION_REFERENCIA ?? '',
    maps:       s.maps       ?? process.env.BOT_MAPS_LINK ?? 'https://maps.google.com',
    horarios:   s.horarios   ?? cfg.horario_atencion    ?? '',
    latitud:    cfg.latitud  ?? parseFloat(process.env.BOT_LATITUD  || '-17.3895'),
    longitud:   cfg.longitud ?? parseFloat(process.env.BOT_LONGITUD || '-66.1568'),
  };
}

export async function getBotIaSettings() {
  const s = await readSettingsGroup('bot_ia');
  return {
    reglas_extra:       s.reglas_extra       ?? '',
    max_tokens:         s.max_tokens         ?? 200,
    temperature:        s.temperature        ?? 0.3,
    timeout_segundos:   s.timeout_segundos   ?? 90,
    max_input_chars:    s.max_input_chars    ?? 500,
    system_prompt_base: s.system_prompt_base ?? `Eres el asistente virtual oficial de {nombre} — Centro de Formación Continua de Cochabamba, Bolivia. Tu nombre es "Asistente CENEFCO". NUNCA digas que eres un modelo de lenguaje, una IA genérica, ChatGPT, Llama ni ningún otro sistema. Si alguien pregunta qué eres o cómo te llamas, responde únicamente que eres el asistente virtual de {nombre}. Responde siempre en español, de forma amable y concisa. Solo responde preguntas relacionadas con los servicios de {nombre}: cursos, diplomados, inscripciones, pagos, docentes, horarios, eventos y boletines. No inventes información. Si no sabes algo, indica que el usuario puede escribir *menú* o contactar directamente a {nombre}.`,
  };
}

export async function getOrCreateConversacion(phone, nombre = null, phoneDisplay = null) {
  const db = getPool();

  const { rows } = await db.query(
    'SELECT * FROM whatsapp_conversaciones WHERE phone = $1 LIMIT 1',
    [phone]
  );

  if (rows.length > 0) {
    const conv = rows[0];
    const updates = [];
    const params  = [];
    if (nombre && conv.nombre !== nombre) {
      params.push(nombre); updates.push(`nombre = $${params.length}`);
    }
    if (phoneDisplay && !conv.phone_display) {
      params.push(phoneDisplay); updates.push(`phone_display = $${params.length}`);
    }
    if (updates.length) {
      params.push(phone);
      await db.query(
        `UPDATE whatsapp_conversaciones SET ${updates.join(', ')} WHERE phone = $${params.length}`,
        params
      );
      if (nombre) conv.nombre = nombre;
      if (phoneDisplay) conv.phone_display = phoneDisplay;
    }
    return conv;
  }

  const insert = await db.query(
    `INSERT INTO whatsapp_conversaciones (phone, phone_display, nombre, estado, contexto, created_at, updated_at)
     VALUES ($1, $2, $3, 'menu', '{}', NOW(), NOW())
     RETURNING *`,
    [phone, phoneDisplay, nombre]
  );
  return insert.rows[0];
}

export async function setState(phone, estado, contexto = {}) {
  await getPool().query(
    `UPDATE whatsapp_conversaciones
     SET estado = $1, contexto = $2, updated_at = NOW()
     WHERE phone = $3`,
    [estado, JSON.stringify(contexto), phone]
  );
}

export async function setContexto(phone, contexto) {
  await getPool().query(
    `UPDATE whatsapp_conversaciones SET contexto = $1, updated_at = NOW() WHERE phone = $2`,
    [JSON.stringify(contexto), phone]
  );
}

export async function mergeContexto(phone, extra) {
  const { rows } = await getPool().query(
    'SELECT contexto FROM whatsapp_conversaciones WHERE phone = $1 LIMIT 1',
    [phone]
  );
  const existing = rows[0]
    ? (typeof rows[0].contexto === 'string'
        ? JSON.parse(rows[0].contexto)
        : (rows[0].contexto ?? {}))
    : {};
  await setContexto(phone, { ...existing, ...extra });
}

export async function getConversacion(phone) {
  const { rows } = await getPool().query(
    'SELECT * FROM whatsapp_conversaciones WHERE phone = $1 LIMIT 1',
    [phone]
  );
  return rows[0] ?? null;
}

export async function listConversaciones({ query = '', estado = '', page = 1, pageSize = 15 } = {}) {
  const db = getPool();
  const offset = (page - 1) * pageSize;
  const params = [];
  const conditions = [];
  let i = 1;

  if (query) {
    conditions.push(`(phone ILIKE $${i} OR nombre ILIKE $${i + 1})`);
    params.push(`%${query}%`, `%${query}%`);
    i += 2;
  }
  if (estado) {
    conditions.push(`estado = $${i}`);
    params.push(estado);
    i++;
  }

  const where = conditions.length ? `WHERE ${conditions.join(' AND ')}` : '';

  const countRes = await db.query(
    `SELECT COUNT(*) AS total FROM whatsapp_conversaciones ${where}`,
    params
  );
  const total = parseInt(countRes.rows[0].total);

  const dataRes = await db.query(
    `SELECT * FROM whatsapp_conversaciones ${where}
     ORDER BY CASE WHEN estado = 'soporte' THEN 0 ELSE 1 END, updated_at DESC
     LIMIT $${i} OFFSET $${i + 1}`,
    [...params, pageSize, offset]
  );

  return { data: dataRes.rows, total };
}

export async function marcarAtendido(phone) {
  await getPool().query(
    `UPDATE whatsapp_conversaciones SET estado = 'menu', contexto = '{}', updated_at = NOW() WHERE phone = $1`,
    [phone]
  );
}

const DIRECCIONES_VALIDAS = new Set(['entrante', 'saliente']);
const TIPOS_VALIDOS       = new Set(['text', 'image', 'audio', 'video', 'document', 'location', 'sticker', 'reaction', 'template']);
const MAX_PHONE           = 60;
const MAX_CONTENIDO       = 4000;

export async function logMensaje(phone, direccion, tipo, contenido) {
  if (!phone || typeof phone !== 'string')  return;
  if (!DIRECCIONES_VALIDAS.has(direccion))  return;

  const phoneSafe     = phone.trim().substring(0, MAX_PHONE);
  const tipoSafe      = TIPOS_VALIDOS.has(tipo) ? tipo : 'text';
  const contenidoSafe = contenido ? String(contenido).trim().substring(0, MAX_CONTENIDO) : null;

  try {
    const db = getPool();

    const { rows } = await db.query(
      'SELECT id FROM whatsapp_conversaciones WHERE phone = $1 LIMIT 1',
      [phoneSafe]
    );
    const conversacionId = rows[0]?.id ?? null;

    await db.query(
      `INSERT INTO whatsapp_mensajes
         (conversacion_id, phone, direccion, tipo, contenido, created_at, updated_at)
       VALUES ($1, $2, $3, $4, $5, NOW(), NOW())`,
      [conversacionId, phoneSafe, direccion, tipoSafe, contenidoSafe]
    );

    if (conversacionId) {
      await db.query(
        'UPDATE whatsapp_conversaciones SET updated_at = NOW() WHERE id = $1',
        [conversacionId]
      );
    }
  } catch (err) {
    console.error('[store] logMensaje error:', err.message);
  }
}

export async function getMensajes(phone) {
  const { rows } = await getPool().query(
    `SELECT id, direccion, tipo, contenido, created_at
     FROM whatsapp_mensajes WHERE phone = $1 ORDER BY created_at ASC`,
    [phone]
  );
  return rows;
}

export async function getAllPhones() {
  const { rows } = await getPool().query(
    'SELECT DISTINCT phone FROM whatsapp_conversaciones ORDER BY phone'
  );
  return rows.map(r => r.phone);
}

let _intentsCache = null;
let _intentsCacheTs = 0;
const INTENTS_TTL = 5 * 60 * 1000;

export async function loadIntents() {
  if (_intentsCache && Date.now() - _intentsCacheTs < INTENTS_TTL) {
    return _intentsCache;
  }

  const { rows } = await getPool().query(
    `SELECT id, nombre, slug, dominio, prioridad,
            eventos, input_contexts, output_contexts,
            frases_entrenamiento, respuestas, accion, activo
     FROM web_intents WHERE activo = true
     ORDER BY dominio, orden, prioridad DESC`
  );

  _intentsCache = rows.map(r => ({
    name:            r.nombre,
    slug:            r.slug,
    domain:          r.dominio,
    priority:        r.prioridad,
    events:          parseJson(r.eventos, []),
    inputContexts:   parseJson(r.input_contexts, []),
    outputContexts:  parseJson(r.output_contexts, []),
    trainingPhrases: parseJson(r.frases_entrenamiento, []),
    responses:       parseJson(r.respuestas, []),
    action:          r.accion,
  }));

  _intentsCacheTs = Date.now();
  return _intentsCache;
}

export function invalidateIntentCache() {
  _intentsCache = null;
  _intentsCacheTs = 0;
}

function parseJson(val, fallback) {
  if (!val) return fallback;
  try { return JSON.parse(val); } catch { return fallback; }
}

export async function initCuentasTable() {
  await getPool().query(`
    CREATE TABLE IF NOT EXISTS whatsapp_cuentas (
      id      TEXT PRIMARY KEY,
      nombre  TEXT NOT NULL,
      phone   TEXT,
      estado  TEXT NOT NULL DEFAULT 'inactivo',
      created_at TIMESTAMPTZ DEFAULT NOW()
    )
  `);
}

export async function getCuentas() {
  const { rows } = await getPool().query(
    'SELECT id, nombre, phone, estado, created_at FROM whatsapp_cuentas ORDER BY created_at ASC'
  );
  return rows;
}

export async function getCuentaById(id) {
  const { rows } = await getPool().query(
    'SELECT id, nombre, phone, estado FROM whatsapp_cuentas WHERE id = $1',
    [id]
  );
  return rows[0] ?? null;
}

export async function createCuenta(id, nombre) {
  await getPool().query(
    'INSERT INTO whatsapp_cuentas (id, nombre, estado) VALUES ($1, $2, $3)',
    [id, nombre, 'inactivo']
  );
}

export async function deleteCuenta(id) {
  await getPool().query('DELETE FROM whatsapp_cuentas WHERE id = $1', [id]);
}
