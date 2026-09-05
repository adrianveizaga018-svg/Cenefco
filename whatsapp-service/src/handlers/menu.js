import { setState } from '../store.js';
import { sendText, sendImage, getBot, phone, LOGO_PATH } from '../utils/wa.js';
import { showAreas, showCursosPorArea, showCursosLista, showCursoDetalle } from './cursos.js';
import { showInscripciones } from './inscripciones.js';
import { showPagosInfo } from './pagos.js';
import { showDocentesLista, showDocenteDetalle } from './docentes.js';
import { showNoticias, showDetalleNoticia } from './noticias.js';
import { showBoletines, showDetalleBoletin } from './boletines.js';
import { showEventos, showDetalleEvento } from './eventos.js';
import { handleHorario, handleUbicacion, handlePresentacion, handleSoporte } from './misc.js';

export async function sendMenu(sock, jid, nombre = null) {
  const BOT = await getBot();
  const saludo = nombre ? `¡Hola, *${nombre}*!` : '¡Hola!';
  await sendText(sock, jid,
    `👋 ${saludo} Bienvenido/a al asistente virtual de *${BOT.nombre}*.\n\n` +
    `🎓 Centro de Formación Continua — Cochabamba, Bolivia\n\n` +
    `¿En qué puedo ayudarte hoy?\n\n` +
    `1️⃣  📚 Cursos y programas\n` +
    `2️⃣  📝 Inscripciones\n` +
    `3️⃣  💳 Pagos y costos\n` +
    `4️⃣  👨‍🏫 Docentes\n` +
    `5️⃣  📰 Noticias\n` +
    `6️⃣  📋 Boletines\n` +
    `7️⃣  📅 Eventos\n` +
    `8️⃣  🕐 Horarios y ubicación\n` +
    `9️⃣  📞 Soporte\n\n` +
    `Responde con el *número* de la opción.`
  );
  try { await sendImage(sock, jid, LOGO_PATH); } catch (_) {}
  await setState(phone(jid), 'menu', { welcomed: true });
}

export async function dispatchKeyword(sock, jid, intent, nombre) {
  switch (intent) {
    case 'presentacion':  await handlePresentacion(sock, jid); break;
    case 'saludo':        await sendMenu(sock, jid, nombre); break;
    case 'cursos':        await showAreas(sock, jid); break;
    case 'areas':         await showAreas(sock, jid); break;
    case 'inscripciones': await showInscripciones(sock, jid); break;
    case 'pagos':         await showPagosInfo(sock, jid); break;
    case 'docentes':      await showDocentesLista(sock, jid); break;
    case 'noticias':      await showNoticias(sock, jid); break;
    case 'boletines':     await showBoletines(sock, jid); break;
    case 'eventos':       await showEventos(sock, jid); break;
    case 'horario':       await handleHorario(sock, jid); break;
    case 'ubicacion':     await handleUbicacion(sock, jid); break;
    case 'soporte':       await handleSoporte(sock, jid); break;
    default:              await sendMenu(sock, jid, nombre); break;
  }
}

export async function handleNumericInput(sock, jid, text, estado, contexto) {
  const n = parseInt(text);

  if (estado === 'multi_intent') {
    const intents = contexto?.intents ?? [];
    const intent = intents[n - 1];
    if (!intent) { await sendMenu(sock, jid); return; }
    await dispatchKeyword(sock, jid, intent, null);
    return;
  }

  if (estado === 'menu') {
    const opciones = ['cursos', 'inscripciones', 'pagos', 'docentes', 'noticias', 'boletines', 'eventos', 'horario', 'soporte'];
    const intent = opciones[n - 1];
    if (!intent) { await sendMenu(sock, jid); return; }
    await dispatchKeyword(sock, jid, intent, null);
    return;
  }

  // Selección de área
  if (estado === 'areas_lista') {
    const areaIds = contexto?.area_ids ?? [];
    if (n < 1 || n > areaIds.length) {
      await sendText(sock, jid, `❌ Opción no válida. Elige un número del 1 al ${areaIds.length} o *0* para el menú.`);
      return;
    }
    const areaId = areaIds[n - 1];
    await showCursosPorArea(sock, jid, areaId);
    return;
  }

  // Selección de curso dentro de un área
  if (estado === 'cursos_lista') {
    const ids = contexto?.curso_ids ?? [];
    const id = ids[n - 1];
    if (!id) {
      await sendText(sock, jid, `❌ Opción no válida. Elige un número del 1 al ${ids.length}, escribe *áreas* para volver o *0* para el menú.`);
      return;
    }
    await showCursoDetalle(sock, jid, id);
    return;
  }

  if (estado === 'curso_detalle') {
    if (n === 1) { await showInscripciones(sock, jid); return; }
    if (n === 2) {
      const areaId = contexto?.area_id ?? null;
      if (areaId !== null) {
        await showCursosPorArea(sock, jid, areaId);
      } else {
        await showAreas(sock, jid);
      }
      return;
    }
    await sendMenu(sock, jid);
    return;
  }

  if (estado === 'noticias') {
    const ids = contexto?.noticia_ids ?? [];
    const id = ids[n - 1];
    if (!id) { await sendText(sock, jid, `❌ Opción no válida. Elige del 1 al ${ids.length} o *0* para el menú.`); return; }
    await showDetalleNoticia(sock, jid, id);
    return;
  }

  if (estado === 'noticia_detalle') {
    if (n === 1) { await showNoticias(sock, jid); return; }
    await sendMenu(sock, jid);
    return;
  }

  if (estado === 'boletines') {
    const ids = contexto?.boletin_ids ?? [];
    const id = ids[n - 1];
    if (!id) { await sendText(sock, jid, `❌ Opción no válida. Elige del 1 al ${ids.length} o *0* para el menú.`); return; }
    await showDetalleBoletin(sock, jid, id);
    return;
  }

  if (estado === 'boletin_detalle') {
    if (n === 1) { await showBoletines(sock, jid); return; }
    await sendMenu(sock, jid);
    return;
  }

  if (estado === 'eventos') {
    const ids = contexto?.evento_ids ?? [];
    const id = ids[n - 1];
    if (!id) { await sendText(sock, jid, `❌ Opción no válida. Elige del 1 al ${ids.length} o *0* para el menú.`); return; }
    await showDetalleEvento(sock, jid, id);
    return;
  }

  if (estado === 'evento_detalle') {
    if (n === 1) { await showEventos(sock, jid); return; }
    await sendMenu(sock, jid);
    return;
  }

  if (estado === 'docentes_lista') {
    const ids = contexto?.docente_ids ?? [];
    const id = ids[n - 1];
    if (!id) { await sendText(sock, jid, `❌ Opción no válida. Elige del 1 al ${ids.length} o *0* para el menú.`); return; }
    await showDocenteDetalle(sock, jid, id);
    return;
  }

  if (estado === 'docente_detalle') {
    if (n === 1) { await showDocentesLista(sock, jid); return; }
    await sendMenu(sock, jid);
    return;
  }

  await sendMenu(sock, jid);
}
