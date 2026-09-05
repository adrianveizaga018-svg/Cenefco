import { getPool } from '../store.js';

// Cache TTL = 5 min para no golpear la BD en cada mensaje
const cache    = { data: [], ts: 0 };
const CACHE_MS = 5 * 60 * 1000;

/**
 * Carga (y cachea) todos los speeches activos desde la BD.
 * Exportada para ser usada por el NLU engine.
 */
export async function loadSpeeches() {
  if (Date.now() - cache.ts < CACHE_MS && cache.data.length > 0) return cache.data;
  const db = getPool();
  const { rows } = await db.query(
    `SELECT id, titulo, categoria, contenido, palabras_clave
     FROM web_speech_ventas
     WHERE activo = true
     ORDER BY orden ASC, id ASC`
  ).catch(() => ({ rows: [] }));
  cache.data = rows;
  cache.ts   = Date.now();
  return rows;
}

/**
 * Devuelve todos los speeches activos como texto para el contexto del LLM.
 */
export async function getSpeechesContext() {
  const speeches = await loadSpeeches().catch(() => []);
  if (!speeches.length) return null;

  return speeches.map(s => {
    const cat = s.categoria ? ` [${s.categoria}]` : '';
    const kw  = s.palabras_clave ? ` (palabras clave: ${s.palabras_clave})` : '';
    return `• ${s.titulo}${cat}${kw}:\n  ${s.contenido.replace(/\n/g, ' ')}`;
  }).join('\n\n');
}

/** Invalida el cache (p.ej. al editar desde el panel) */
export function invalidateSpeechCache() {
  cache.ts = 0;
}
