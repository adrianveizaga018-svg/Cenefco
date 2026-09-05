import { setState, getPool } from '../store.js';
import { sendText, getBot, phone } from '../utils/wa.js';

export async function showDocentesLista(sock, jid) {
  const BOT = await getBot();
  const db = getPool();

  const { rows } = await db.query(
    `SELECT id, nombre_completo, titulo_academico, especialidad, tipo
     FROM web_docente_perfil
     WHERE mostrar_en_web = true AND estado = 'publicado' AND deleted_at IS NULL
     ORDER BY orden ASC, nombre_completo ASC
     LIMIT 6`
  );

  if (rows.length === 0) {
    await sendText(sock, jid,
      `👨‍🏫 Próximamente publicaremos los perfiles de nuestro equipo docente.\n\n` +
      `Visita: ${BOT.web}/docentes\n\nEscribe *0* para volver al menú.`
    );
    await setState(phone(jid), 'menu');
    return;
  }

  let texto = '👨‍🏫 *Equipo Docente de CENEFCO*\n\n';
  for (let i = 0; i < rows.length; i++) {
    const d = rows[i];
    texto += `*${i + 1}. ${d.nombre_completo}*`;
    if (d.titulo_academico) texto += `\n   ${d.titulo_academico}`;
    if (d.especialidad) texto += `\n   🎯 ${d.especialidad}`;
    texto += '\n\n';
  }

  texto += `🌐 Ver todos los docentes: ${BOT.web}/docentes\n\nResponde con el *número* para ver el perfil completo o *0* para el menú.`;

  await sendText(sock, jid, texto.trimEnd());
  await setState(phone(jid), 'docentes_lista', { welcomed: true, docente_ids: rows.map(r => r.id) });
}

export async function showDocenteDetalle(sock, jid, docenteId) {
  const db = getPool();
  const { rows } = await db.query(
    `SELECT * FROM web_docente_perfil WHERE id = $1 AND deleted_at IS NULL LIMIT 1`,
    [docenteId]
  );

  if (rows.length === 0) {
    await sendText(sock, jid, '⚠️ Perfil no encontrado.');
    await showDocentesLista(sock, jid);
    return;
  }

  const d = rows[0];
  let texto = `👨‍🏫 *${d.nombre_completo}*\n\n`;
  if (d.titulo_academico) texto += `🎓 ${d.titulo_academico}\n`;
  if (d.especialidad) texto += `🎯 Especialidad: ${d.especialidad}\n`;
  if (d.tipo && d.tipo !== 'docente') texto += `👤 Rol: ${d.tipo}\n`;
  if (d.biografia) texto += `\n${d.biografia.substring(0, 400)}${d.biografia.length > 400 ? '...' : ''}\n`;
  if (d.email_publico) texto += `\n📧 ${d.email_publico}`;
  if (d.linkedin_url) texto += `\n🔗 LinkedIn: ${d.linkedin_url}`;

  await sendText(sock, jid, texto);
  await sendText(sock, jid, '¿Qué deseas hacer?\n1. 👨‍🏫 Ver más docentes\n2. 🏠 Menú principal');
  await setState(phone(jid), 'docente_detalle', { welcomed: true, docente_id: docenteId });
}
