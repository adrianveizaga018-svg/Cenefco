import { setState, getPool } from '../store.js';
import { sendText, getBot, phone } from '../utils/wa.js';

export async function showNoticias(sock, jid) {
  const BOT = await getBot();
  const db = getPool();

  const { rows } = await db.query(
    `SELECT id_art, titulo, entradilla, fecha_publicacion FROM t_articulo
     WHERE estado_web = 'publicado' AND estado = 1 AND deleted_at IS NULL
     ORDER BY fecha_publicacion DESC LIMIT 4`
  );

  if (rows.length === 0) {
    await sendText(sock, jid, '😔 No hay noticias publicadas en este momento.\n\nEscribe *0* para volver al menú.');
    const { sendMenu } = await import('./menu.js');
    await sendMenu(sock, jid);
    return;
  }

  let texto = '📰 *Últimas noticias de CENEFCO*\n\n';
  for (let i = 0; i < rows.length; i++) {
    const n = rows[i];
    const fecha = n.fecha_publicacion ? new Date(n.fecha_publicacion).toLocaleDateString('es-BO') : '';
    texto += `*${i + 1}. ${n.titulo}*`;
    if (fecha) texto += `\n📅 ${fecha}`;
    if (n.entradilla) texto += `\n${n.entradilla.substring(0, 150)}`;
    texto += '\n\n';
  }

  await sendText(sock, jid, texto.trimEnd());
  await sendText(sock, jid, `🌐 Ver todas las noticias: ${BOT.web}/noticias\n\nResponde con el *número* para ver el detalle o *0* para el menú.`);
  await setState(phone(jid), 'noticias', { welcomed: true, noticia_ids: rows.map(r => r.id_art) });
}

export async function showDetalleNoticia(sock, jid, noticiaId) {
  const db = getPool();
  const { rows } = await db.query(
    `SELECT * FROM t_articulo WHERE id_art = $1 AND deleted_at IS NULL LIMIT 1`,
    [noticiaId]
  );
  const noticia = rows[0];

  if (!noticia) {
    await sendText(sock, jid, '⚠️ Noticia no encontrada.');
    await showNoticias(sock, jid);
    return;
  }

  const fecha = noticia.fecha_publicacion ? new Date(noticia.fecha_publicacion).toLocaleDateString('es-BO') : '';
  let texto = `📰 *${noticia.titulo}*\n`;
  if (fecha) texto += `📅 ${fecha}\n`;
  texto += `\n${noticia.entradilla ?? ''}`;

  await sendText(sock, jid, texto);
  await sendText(sock, jid, '¿Qué deseas hacer?\n1. 📰 Más noticias\n2. 🏠 Menú principal');
  await setState(phone(jid), 'noticia_detalle', { welcomed: true, noticia_id: noticiaId });
}
