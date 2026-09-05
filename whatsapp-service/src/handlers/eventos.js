import { setState, getPool } from '../store.js';
import { sendText, getBot, phone } from '../utils/wa.js';

export async function showEventos(sock, jid) {
  const BOT = await getBot();
  const db = getPool();

  const { rows } = await db.query(
    `SELECT id, titulo, entradilla, fecha_inicio, fecha_fin, lugar, modalidad, gratuito, precio
     FROM web_evento
     WHERE estado = 'publicado' AND fecha_inicio >= NOW() AND deleted_at IS NULL
     ORDER BY fecha_inicio ASC LIMIT 4`
  );

  if (rows.length === 0) {
    await sendText(sock, jid, '😔 No hay eventos próximos en este momento.\n\nEscribe *0* para volver al menú.');
    const { sendMenu } = await import('./menu.js');
    await sendMenu(sock, jid);
    return;
  }

  let texto = '📅 *Próximos eventos de CENEFCO*\n\n';
  for (let i = 0; i < rows.length; i++) {
    const e = rows[i];
    const fecha = new Date(e.fecha_inicio).toLocaleDateString('es-BO');
    texto += `*${i + 1}. ${e.titulo}*`;
    texto += `\n📅 ${fecha}`;
    if (e.lugar) texto += ` | 📍 ${e.lugar}`;
    if (e.modalidad) texto += `\n🔄 ${e.modalidad.charAt(0).toUpperCase() + e.modalidad.slice(1)}`;
    if (e.gratuito) {
      texto += ' | 🆓 Gratuito';
    } else if (e.precio) {
      texto += ` | 💵 Bs. ${parseFloat(e.precio).toFixed(2)}`;
    }
    if (e.entradilla) texto += `\n${e.entradilla.substring(0, 100)}`;
    texto += '\n\n';
  }

  await sendText(sock, jid, texto.trimEnd());
  await sendText(sock, jid, `🌐 Ver todos los eventos: ${BOT.web}/eventos\n\nResponde con el *número* para ver el detalle o *0* para el menú.`);
  await setState(phone(jid), 'eventos', { welcomed: true, evento_ids: rows.map(r => r.id) });
}

export async function showDetalleEvento(sock, jid, eventoId) {
  const db = getPool();
  const { rows } = await db.query(
    `SELECT * FROM web_evento WHERE id = $1 AND deleted_at IS NULL LIMIT 1`,
    [eventoId]
  );
  const evento = rows[0];

  if (!evento) {
    await sendText(sock, jid, '⚠️ Evento no encontrado.');
    await showEventos(sock, jid);
    return;
  }

  const fechaInicio = new Date(evento.fecha_inicio).toLocaleDateString('es-BO');
  let texto = `📅 *${evento.titulo}*\n\n`;
  texto += `📅 Fecha: ${fechaInicio}`;
  if (evento.fecha_fin) {
    const fechaFin = new Date(evento.fecha_fin).toLocaleDateString('es-BO');
    if (fechaFin !== fechaInicio) texto += ` al ${fechaFin}`;
  }
  texto += '\n';
  if (evento.lugar) texto += `📍 Lugar: ${evento.lugar}\n`;
  if (evento.modalidad) texto += `🔄 Modalidad: ${evento.modalidad}\n`;
  if (evento.gratuito) {
    texto += `💰 Entrada: Gratuita\n`;
  } else if (evento.precio) {
    texto += `💰 Precio: Bs. ${parseFloat(evento.precio).toFixed(2)}\n`;
  }
  if (evento.tipo) texto += `🏷️ Tipo: ${evento.tipo}\n`;
  if (evento.descripcion) texto += `\n${evento.descripcion.substring(0, 400)}\n`;
  if (evento.url_registro) texto += `\n🔗 Regístrate aquí: ${evento.url_registro}\n`;
  if (evento.url_transmision) texto += `📡 Transmisión: ${evento.url_transmision}\n`;

  await sendText(sock, jid, texto);
  await sendText(sock, jid, '¿Qué deseas hacer?\n1. 📅 Más eventos\n2. 🏠 Menú principal');
  await setState(phone(jid), 'evento_detalle', { welcomed: true, evento_id: eventoId });
}
