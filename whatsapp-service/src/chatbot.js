/**
 * chatbot.js — Dispatcher principal
 *
 * Arquitectura completa inspirada en Dialogflow Essentials:
 *
 *  ① Events        → WELCOME en primer mensaje (equivale a "Events" de Dialogflow)
 *  ② Pre-checks    → soporte activo, menu triggers, sin texto, numérico
 *  ③ Filtros       → mensajes inapropiados, sin sentido, cortesías
 *  ④ NLU Engine    → detectIntent() con:
 *                    - Context filter (inputContexts activos)
 *                    - Jaccard similarity contra training phrases
 *                    - Speech intents dinámicos (BD)
 *  ⑤ Fulfillment   → directResponse (responses[]) o handler (action)
 *                    Aplica outputContexts y persiste context_stack
 *  ⑥ Fallback IA   → Ollama
 *  ⑦ Default       → respuestaFueraDeTema()
 */

import { getOrCreateConversacion, setState, mergeContexto, logMensaje } from './store.js';
import { responder as agentResponder } from './agent.js';
import { extractText, phone, sendText } from './utils/wa.js';
import { detectIntent, confidenceLabel } from './nlu/engine.js';
import {
  esMensajeInapropiado, esTextoSinSentido,
  detectarCortesia, respuestaFueraDeTema, respuestaInapropiada,
} from './filters/index.js';
import { sendMenu, dispatchKeyword, handleNumericInput } from './handlers/menu.js';

const MENU_TRIGGERS  = [
  '0', 'menu', 'menú', 'inicio', 'volver', 'start',
  'hola', 'hi', 'hello', 'buenas', 'buen dia', 'buenos dias',
  'buenas tardes', 'buenas noches', 'saludos', 'ey', 'oye',
  'que tal', 'como estas', 'empezar', 'comenzar',
];
const AREAS_TRIGGERS = ['areas', 'áreas', 'categorias', 'categorías'];

export async function handleMessage(sock, msg) {
  const jid   = msg.key.remoteJid;
  const from  = phone(jid);
  const text  = extractText(msg);
  const nombre = msg.pushName ?? null;

  // Si el JID es un número real (@s.whatsapp.net), lo guardamos como phone_display legible
  const phoneDisplay = jid.endsWith('@s.whatsapp.net')
    ? `+${jid.split('@')[0]}`
    : null;

  let conv;
  try {
    conv = await getOrCreateConversacion(from, nombre, phoneDisplay);
  } catch (err) {
    console.error('[Bot] Error BD getOrCreate:', err.message);
    await sendText(sock, jid, '⚠️ Error interno. Intenta de nuevo en un momento.');
    return;
  }

  const estado   = conv.estado ?? 'menu';
  const contexto = typeof conv.contexto === 'string'
    ? JSON.parse(conv.contexto)
    : (conv.contexto ?? {});

  try { await logMensaje(from, 'entrante', 'text', text); } catch (_) {}
  try { await sock.readMessages([msg.key]); } catch (_) {}

  try {
    await _dispatch(sock, jid, from, text, nombre, estado, contexto);
  } catch (err) {
    console.error('[Bot] Error no controlado:', err.message, err.stack);
    try {
      await sendText(sock, jid,
        '⚠️ Ocurrió un error inesperado. Escribe *menú* para continuar.'
      );
    } catch (_) {}
  }
}

async function _dispatch(sock, jid, from, text, nombre, estado, contexto) {

  const isFirstMessage = !contexto.welcomed;
  const event = isFirstMessage ? 'WELCOME' : null;

  // Primer mensaje: siempre mostrar el menú directamente, sin pasar por NLU ni Ollama
  if (isFirstMessage) {
    await sendMenu(sock, jid, nombre);
    await mergeContexto(from, { welcomed: true, context_stack: [] });
    return;
  }

  if (estado === 'soporte') {
    const intentos = (contexto.soporte_intentos ?? 0) + 1;
    if (intentos >= 2) {
      await sendText(sock, jid,
        `⚠️ Por el momento no hay asesores disponibles.\n\n` +
        `El asistente virtual retoma la atención. Escribe *menú* para ver las opciones.`
      );
      await setState(from, 'menu', { welcomed: true });
      return;
    }
    await setState(from, 'soporte', { ...contexto, soporte_intentos: intentos });
    const msgs = [
      '📩 Tu mensaje fue recibido. Un asesor de CENEFCO te atenderá pronto.',
      '📩 Seguimos esperando que un asesor quede disponible. Ten paciencia.',
    ];
    await sendText(sock, jid, msgs[intentos - 1] ?? msgs[0]);
    return;
  }

  if (MENU_TRIGGERS.includes(text.toLowerCase())) {
    await sendMenu(sock, jid, nombre);
    await mergeContexto(from, { welcomed: true, context_stack: [] });
    return;
  }

  if (AREAS_TRIGGERS.includes(text.toLowerCase())) {
    const { showAreas: _showAreas } = await import('./handlers/cursos.js');
    await _showAreas(sock, jid);
    await mergeContexto(from, { welcomed: true, context_stack: [] });
    return;
  }

  if (!text) {
    await sendMenu(sock, jid, nombre);
    return;
  }

  if (/^\d+$/.test(text)) {
    await handleNumericInput(sock, jid, text, estado, contexto);
    return;
  }

  if (!event) {
    if (esMensajeInapropiado(text)) {
      await sendText(sock, jid, respuestaInapropiada());
      return;
    }

    if (esTextoSinSentido(text)) {
      await sendText(sock, jid, respuestaFueraDeTema());
      return;
    }

    const cortesia = detectarCortesia(text);
    if (cortesia) {
      await sendText(sock, jid, cortesia);
      return;
    }
  }

  const contextStack = contexto.context_stack ?? [];

  let nluResult = null;
  try {
    nluResult = await detectIntent(text, contextStack, event);
  } catch (err) {
    console.error('[Bot] Error NLU:', err.message);
  }

  if (nluResult) {
    const ctxLog = nluResult.newContextStack?.map(c => `${c.name}(${c.lifespan})`).join(', ') || '—';
    console.log(
      `[NLU] intent="${nluResult.intent}" conf=${nluResult.confidence.toFixed(2)}` +
      ` (${confidenceLabel(nluResult.confidence)}) src=${nluResult.source}` +
      ` ctx=[${ctxLog}]`
    );

    const nextContexto = {
      ...contexto,
      welcomed:      true,
      context_stack: nluResult.newContextStack ?? [],
    };

    if (nluResult.source === 'speech') {
      await sendText(sock, jid, nluResult.response);
      await mergeContexto(from, { welcomed: true, context_stack: nluResult.newContextStack ?? [] });
      return;
    }

    if (nluResult.source === 'static') {
      if (nluResult.directResponse) {
        await sendText(sock, jid, nluResult.directResponse);
        await mergeContexto(from, { welcomed: true, context_stack: nluResult.newContextStack ?? [] });
        return;
      }
      await dispatchKeyword(sock, jid, nluResult.action, nombre);
      await mergeContexto(from, { welcomed: true, context_stack: nluResult.newContextStack ?? [] });
      return;
    }
  }

  try {
    try { await sock.sendPresenceUpdate('composing', jid); } catch (_) {}
    const respuesta = await agentResponder(from, text);
    try { await sock.sendPresenceUpdate('paused', jid); } catch (_) {}
    if (respuesta) {
      await sendText(sock, jid, respuesta);
      await mergeContexto(from, { welcomed: true });
      return;
    }
  } catch (err) {
    console.error('[Bot] Error IA:', err.message);
    try { await sock.sendPresenceUpdate('paused', jid); } catch (_) {}
  }

  await sendText(sock, jid, respuestaFueraDeTema());
  await setState(from, 'menu', { welcomed: true });
}
