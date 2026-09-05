import { setState, getPool } from '../store.js';
import { sendText, getBot, phone } from '../utils/wa.js';

export async function showBoletines(sock, jid) {
  const BOT = await getBot();
  const db = getPool();

  const { rows } = await db.query(
    `SELECT id_boletin, titulo_boletin, descripcion_boletin, fecha_publicacion
     FROM t_boletin
     WHERE estado = 1 AND deleted_at IS NULL
     ORDER BY fecha_publicacion DESC LIMIT 4`
  );

  if (rows.length === 0) {
    await sendText(sock, jid, '😔 No hay boletines publicados en este momento.\n\nEscribe *0* para volver al menú.');
    const { sendMenu } = await import('./menu.js');
    await sendMenu(sock, jid);
    return;
  }

  let texto = '📋 *Boletines de CENEFCO*\n\n';
  for (let i = 0; i < rows.length; i++) {
    const b = rows[i];
    const titulo = b.titulo_boletin || b.titulo_pagina || `Boletín ${i + 1}`;
    const fecha = b.fecha_publicacion ? new Date(b.fecha_publicacion).toLocaleDateString('es-BO') : '';
    texto += `*${i + 1}. ${titulo}*`;
    if (fecha) texto += `\n📅 ${fecha}`;
    if (b.descripcion_boletin) texto += `\n${b.descripcion_boletin.substring(0, 120)}`;
    texto += '\n\n';
  }

  await sendText(sock, jid, texto.trimEnd());
  await sendText(sock, jid, `🌐 Ver todos los boletines: ${BOT.web}/boletines\n\nResponde con el *número* para ver el detalle o *0* para el menú.`);
  await setState(phone(jid), 'boletines', { welcomed: true, boletin_ids: rows.map(r => r.id_boletin) });
}

export async function showDetalleBoletin(sock, jid, boletinId) {
  const db = getPool();
  const { rows } = await db.query(
    `SELECT * FROM t_boletin WHERE id_boletin = $1 AND deleted_at IS NULL LIMIT 1`,
    [boletinId]
  );
  const boletin = rows[0];

  if (!boletin) {
    await sendText(sock, jid, '⚠️ Boletín no encontrado.');
    await showBoletines(sock, jid);
    return;
  }

  const titulo = boletin.titulo_boletin || boletin.titulo_pagina || 'Boletín';
  const fecha = boletin.fecha_publicacion ? new Date(boletin.fecha_publicacion).toLocaleDateString('es-BO') : '';
  let texto = `📋 *${titulo}*\n`;
  if (fecha) texto += `📅 ${fecha}\n`;
  if (boletin.descripcion_boletin) texto += `\n${boletin.descripcion_boletin}`;

  await sendText(sock, jid, texto);
  await sendText(sock, jid, '¿Qué deseas hacer?\n1. 📋 Más boletines\n2. 🏠 Menú principal');
  await setState(phone(jid), 'boletin_detalle', { welcomed: true, boletin_id: boletinId });
}
