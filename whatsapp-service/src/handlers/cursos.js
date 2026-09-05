import { setState, getPool } from '../store.js';
import { sendText, sendImage, getBot, phone } from '../utils/wa.js';

const NUM_EMOJIS = ['1️⃣','2️⃣','3️⃣','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣','9️⃣','🔟'];

function numEmoji(i) {
  return NUM_EMOJIS[i] ?? `${i + 1}.`;
}


export async function showAreas(sock, jid) {
  const BOT = await getBot();
  const db  = getPool();

  // Categorías que tienen al menos un programa publicado
  const { rows: categorias } = await db.query(`
    SELECT c.id, c.nombre, COUNT(p.id_programa) AS total
    FROM web_categoria_programa c
    JOIN t_programa p ON p.categoria_web_id = c.id
      AND p.estado_web IN ('publicado', 'activo')
      AND p.estado = 1
    WHERE c.activo = true
    GROUP BY c.id, c.nombre, c.orden
    HAVING COUNT(p.id_programa) > 0
    ORDER BY c.orden ASC, c.nombre ASC
  `).catch(() => ({ rows: [] }));

  // Programas sin categoría asignada
  const { rows: sinCat } = await db.query(`
    SELECT COUNT(*) AS total
    FROM t_programa
    WHERE estado_web IN ('publicado', 'activo')
      AND estado = 1
      AND (categoria_web_id IS NULL
           OR categoria_web_id NOT IN (SELECT id FROM web_categoria_programa WHERE activo = true))
  `).catch(() => ({ rows: [{ total: 0 }] }));

  const sinCatTotal = parseInt(sinCat[0]?.total ?? 0);

  if (categorias.length === 0 && sinCatTotal === 0) {
    await sendText(sock, jid, '😔 No hay cursos disponibles en este momento.\n\n🌐 ' + BOT.web + '/cursos\n\nEscribe *0* para el menú.');
    return;
  }

  const lista = [...categorias];
  if (sinCatTotal > 0) {
    lista.push({ id: 0, nombre: 'Otros programas', total: sinCatTotal });
  }

  let texto = `📚 *Áreas de formación — CENEFCO*\n\nElige el área que te interesa:\n\n`;
  for (let i = 0; i < lista.length; i++) {
    const c = lista[i];
    texto += `${numEmoji(i)} *${c.nombre}*  (${c.total} ${parseInt(c.total) === 1 ? 'curso' : 'cursos'})\n`;
  }
  texto += `\n🌐 ${BOT.web}/cursos\n\nResponde con el *número* del área o *0* para el menú.`;

  await sendText(sock, jid, texto.trimEnd());
  await setState(phone(jid), 'areas_lista', {
    welcomed: true,
    area_ids: lista.map(c => c.id),
  });
}

// ─── 2. Programas por categoría ──────────────────────────────────────────────

export async function showCursosPorArea(sock, jid, categoriaId) {
  const BOT = await getBot();
  const db  = getPool();

  let rows, categoriaNombre;

  if (categoriaId === 0) {
    categoriaNombre = 'Otros programas';
    const res = await db.query(`
      SELECT id_programa, nombre_programa, estado_web,
             inicio_actividades, finalizacion_actividades, inversion
      FROM t_programa
      WHERE estado_web IN ('publicado', 'activo')
        AND estado = 1
        AND (categoria_web_id IS NULL
             OR categoria_web_id NOT IN (SELECT id FROM web_categoria_programa WHERE activo = true))
      ORDER BY orden ASC, nombre_programa ASC
      LIMIT 8
    `).catch(() => ({ rows: [] }));
    rows = res.rows;
  } else {
    const catRes = await db.query(
      'SELECT nombre FROM web_categoria_programa WHERE id = $1 LIMIT 1',
      [categoriaId]
    ).catch(() => ({ rows: [] }));
    categoriaNombre = catRes.rows[0]?.nombre ?? 'Área';

    const res = await db.query(`
      SELECT id_programa, nombre_programa, estado_web,
             inicio_actividades, finalizacion_actividades, inversion
      FROM t_programa
      WHERE categoria_web_id = $1
        AND estado_web IN ('publicado', 'activo')
        AND estado = 1
      ORDER BY orden ASC, nombre_programa ASC
      LIMIT 8
    `, [categoriaId]).catch(() => ({ rows: [] }));
    rows = res.rows;
  }

  if (rows.length === 0) {
    await sendText(sock, jid, `😔 No hay cursos disponibles en *${categoriaNombre}* en este momento.\n\nEscribe *áreas* para ver otras áreas o *0* para el menú.`);
    await showAreas(sock, jid);
    return;
  }

  let texto = `📚 *${categoriaNombre}*\n\n`;
  for (let i = 0; i < rows.length; i++) {
    const p = rows[i];
    texto += `${numEmoji(i)} *${p.nombre_programa}*\n`;
    if (p.inicio_actividades && String(p.inicio_actividades) !== '2000-01-01') {
      const inicio = new Date(p.inicio_actividades).toLocaleDateString('es-BO');
      texto += `   🗓️ Inicio: ${inicio}\n`;
    }
    if (p.inversion) texto += `   💰 ${p.inversion}\n`;
    texto += '\n';
  }
  texto += `Responde con el *número* para ver el detalle, *áreas* para volver, o *0* para el menú.`;

  await sendText(sock, jid, texto.trimEnd());
  await setState(phone(jid), 'cursos_lista', {
    welcomed:    true,
    curso_ids:   rows.map(r => r.id_programa),
    area_id:     categoriaId,
  });
}

// ─── 3. Detalle de un programa ───────────────────────────────────────────────

export async function showCursoDetalle(sock, jid, programaId) {
  const BOT = await getBot();
  const db  = getPool();

  const { rows } = await db.query(`
    SELECT p.id_programa, p.nombre_programa,
           p.descripcion, p.inversion, p.dirigido, p.requisitos,
           p.inicio_actividades, p.finalizacion_actividades,
           p.inicio_inscripciones,
           p.url_whatsapp, p.url_whatsapp2,
           p.foto, p.imagen_banner_url,
           c.nombre AS categoria_nombre
    FROM t_programa p
    LEFT JOIN web_categoria_programa c ON c.id = p.categoria_web_id
    WHERE p.id_programa = $1
      AND p.estado_web IN ('publicado', 'activo')
      AND p.estado = 1
    LIMIT 1
  `, [programaId]);

  if (rows.length === 0) {
    await sendText(sock, jid, '⚠️ Curso no encontrado.');
    await showAreas(sock, jid);
    return;
  }

  const p = rows[0];

  // Enviar imagen del curso si existe (banner preferido, sino foto principal)
  const fotoRaw = p.imagen_banner_url || p.foto;
  if (fotoRaw) {
    const fotoUrl = fotoRaw.startsWith('http') ? fotoRaw : `${BOT.web}/storage/${fotoRaw}`;
    await sendImage(sock, jid, fotoUrl, p.nombre_programa);
  }

  let texto = `📚 *${p.nombre_programa}*\n`;
  if (p.categoria_nombre) texto += `🏷️ Área: ${p.categoria_nombre}\n`;
  texto += '\n';

  if (p.inicio_actividades && String(p.inicio_actividades) !== '2000-01-01') {
    texto += `🗓️ Inicio: ${new Date(p.inicio_actividades).toLocaleDateString('es-BO')}\n`;
  }
  if (p.finalizacion_actividades && String(p.finalizacion_actividades) !== '2000-01-01') {
    texto += `🏁 Fin: ${new Date(p.finalizacion_actividades).toLocaleDateString('es-BO')}\n`;
  }
  if (p.inicio_inscripciones && String(p.inicio_inscripciones) !== '2000-01-01') {
    texto += `📝 Inscripciones desde: ${new Date(p.inicio_inscripciones).toLocaleDateString('es-BO')}\n`;
  }

  if (p.inversion)  texto += `\n💰 *Inversión:* ${p.inversion}\n`;
  if (p.dirigido)   texto += `\n👤 *Dirigido a:* ${p.dirigido.substring(0, 200)}\n`;
  if (p.requisitos) texto += `\n📋 *Requisitos:* ${p.requisitos.substring(0, 200)}\n`;

  if (p.descripcion) {
    const desc = p.descripcion.replace(/<[^>]*>/g, '').trim();
    if (desc) texto += `\nℹ️ ${desc.substring(0, 300)}\n`;
  }

  if (p.url_whatsapp) {
    texto += `\n💬 *Grupo WhatsApp:* ${p.url_whatsapp}\n`;
  }
  if (p.url_whatsapp2) {
    texto += `💬 *Grupo WhatsApp 2:* ${p.url_whatsapp2}\n`;
  }

  await sendText(sock, jid, texto.trimEnd());
  await sendText(sock, jid,
    '¿Qué deseas hacer?\n' +
    '1️⃣  📝 Información de inscripción\n' +
    '2️⃣  📚 Ver más cursos del área\n' +
    '3️⃣  🏠 Menú principal'
  );
  await setState(phone(jid), 'curso_detalle', { welcomed: true, programa_id: programaId });
}

// ─── Alias para compatibilidad con código existente ──────────────────────────

export async function showCursosLista(sock, jid) {
  return showAreas(sock, jid);
}
