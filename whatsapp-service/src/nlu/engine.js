import { normalizeText } from '../utils/text.js';
import { loadIntents } from '../store.js';
import { ContextManager } from './context-manager.js';
import { loadSpeeches } from '../handlers/speeches.js';

const THRESHOLD_STATIC = 0.28;
const THRESHOLD_SPEECH = 0.22;

const STOP_WORDS = new Set([
  'el','la','los','las','un','una','unos','unas',
  'de','del','en','a','al','y','o','que','se','por',
  'con','su','sus','me','te','le','nos','les',
  'mi','tu','lo','si','no','ya','pero','mas','muy',
  'bien','hay','ser','es','son','fue','era','esta','estan',
  'para','hacia','hasta','desde','entre','sobre','bajo',
  'hola','buenas','buenos','buen','favor','por favor',
  'quisiera','podria','puede','puedo','quiero','necesito',
  'tengo','tiene','tienen','saber','decir','dar','hacer',
]);

function tokenize(text) {
  return normalizeText(text)
    .split(/[\s,.:;!?¿¡()\-\/]+/)
    .filter(w => w.length >= 3 && !STOP_WORDS.has(w));
}

function jaccardSim(tokA, tokB) {
  if (!tokA.length || !tokB.length) return 0;
  const a = new Set(tokA);
  const b = new Set(tokB);
  let inter = 0;
  for (const t of a) { if (b.has(t)) inter++; }
  return inter / (a.size + b.size - inter);
}

function phraseContained(inputNorm, phraseNorm) {
  if (phraseNorm === inputNorm) return true;
  if (phraseNorm.length < 4) return false;
  if (inputNorm.includes(phraseNorm)) return true;
  if (inputNorm.length >= 5 && phraseNorm.includes(inputNorm)) return true;
  return false;
}

function scoreAgainstPhrases(inputTokens, inputNorm, phrases) {
  let best = 0;
  for (const rawPhrase of phrases) {
    if (!rawPhrase || rawPhrase.length < 2) continue;
    const phraseNorm = normalizeText(rawPhrase.trim());
    if (phraseContained(inputNorm, phraseNorm)) {
      best = Math.max(best, 0.92);
      continue;
    }
    const phraseTokens = tokenize(rawPhrase);
    const sim = jaccardSim(inputTokens, phraseTokens);
    let bonus = 0;
    if (phraseTokens.length > 0) {
      const pivot = phraseTokens[0];
      if (pivot.length >= 4 && inputNorm.includes(pivot)) bonus = 0.08;
    }
    best = Math.max(best, sim + bonus);
  }
  return Math.min(best, 1.0);
}

function pickResponse(responses) {
  if (!responses?.length) return null;
  return responses[Math.floor(Math.random() * responses.length)];
}

function applyAndTick(ctxMgr, outputContexts = []) {
  for (const oc of outputContexts) {
    ctxMgr.set(oc.name, oc.lifespan ?? 5);
  }
  ctxMgr.tick();
}

/**
 * Punto de entrada principal del motor NLU.
 *
 * @param {string}   text         - Mensaje del usuario
 * @param {Array}    contextStack - Stack de contextos activos (de la BD)
 * @param {string|null} event     - Evento programático ('WELCOME', null, …)
 *
 * @returns {Promise<{
 *   intent: string,
 *   confidence: number,
 *   source: 'static'|'speech',
 *   action?: string,
 *   response?: string,
 *   directResponse?: string,
 *   newContextStack: Array,
 *   outputContexts: Array
 * }|null>}
 */
export async function detectIntent(text, contextStack = [], event = null) {
  const ctxMgr      = new ContextManager(contextStack);
  const inputNorm   = normalizeText(text.trim());
  const inputTokens = tokenize(text);

  const INTENTS = await loadIntents();

  if (event) {
    for (const intent of INTENTS) {
      if (intent.events?.includes(event)) {
        applyAndTick(ctxMgr, intent.outputContexts);
        return {
          intent:          intent.slug,
          confidence:      1.0,
          source:          'static',
          action:          intent.action,
          directResponse:  pickResponse(intent.responses),
          newContextStack: ctxMgr.toArray(),
          outputContexts:  intent.outputContexts ?? [],
        };
      }
    }
  }

  let best = null;

  const eligible = INTENTS.filter(intent => {
    if (!intent.inputContexts?.length) return true;
    return intent.inputContexts.every(name => ctxMgr.has(name));
  });

  for (const intent of eligible) {
    const rawScore = scoreAgainstPhrases(inputTokens, inputNorm, intent.trainingPhrases);
    if (rawScore < THRESHOLD_STATIC) continue;

    const isContextual = (intent.inputContexts?.length ?? 0) > 0;
    const bestIsContextual = best?.isContextual ?? false;

    const beats =
      !best ||
      rawScore > best.rawScore ||
      (rawScore === best.rawScore && isContextual && !bestIsContextual) ||
      (rawScore === best.rawScore && isContextual === bestIsContextual && intent.priority > (best.priority ?? 0));

    if (beats) {
      best = {
        intent:         intent.slug,
        rawScore,
        confidence:     rawScore,
        source:         'static',
        action:         intent.action,
        priority:       intent.priority,
        isContextual,
        responses:      intent.responses ?? [],
        outputContexts: intent.outputContexts ?? [],
      };
    }
  }

  try {
    const speeches = await loadSpeeches();
    for (const speech of speeches) {
      const phrases = [
        ...(speech.palabras_clave ?? '').split(',').map(p => p.trim()),
        speech.titulo,
      ].filter(Boolean);

      const score = scoreAgainstPhrases(inputTokens, inputNorm, phrases);

      if (score >= THRESHOLD_SPEECH) {
        const minToWin = best ? best.confidence + 0.05 : THRESHOLD_SPEECH;
        if (score >= minToWin) {
          best = {
            intent:         `speech_${speech.id}`,
            confidence:     score,
            source:         'speech',
            response:       speech.contenido,
            outputContexts: [],
          };
        }
      }
    }
  } catch (err) {
    console.error('[NLU] Error cargando speeches:', err.message);
  }

  if (!best) return null;

  applyAndTick(ctxMgr, best.outputContexts);

  return {
    intent:          best.intent,
    confidence:      best.confidence,
    source:          best.source,
    action:          best.action,
    response:        best.response,
    directResponse:  best.source === 'static' ? pickResponse(best.responses) : null,
    newContextStack: ctxMgr.toArray(),
    outputContexts:  best.outputContexts ?? [],
  };
}

export function confidenceLabel(score) {
  if (score >= 0.85) return 'ALTA';
  if (score >= 0.55) return 'MEDIA';
  if (score >= 0.30) return 'BAJA';
  return 'MUY BAJA';
}
