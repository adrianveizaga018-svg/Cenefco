import { setState, getPool } from '../store.js';
import { sendText, getBot, phone } from '../utils/wa.js';

export async function showInscripciones(sock, jid) {
  const BOT = await getBot();
  const db = getPool();

  const { rows: cursosAbiertos } = await db.query(
    `SELECT i.id_imp, m.nombre AS nombre_curso, m.nombremat, i.cupo, i.periodo, i.gestion
     FROM t_imparte i
     JOIN t_materia m ON m.id_mat = i.id_mat AND m.id_us_reg = i.id_us_reg
     WHERE i.estado = 1 AND i.inscripcion_auto = 1
     ORDER BY i.imparte_fecha_inicio DESC
     LIMIT 5`
  );

  let texto = `📝 *Proceso de Inscripción en CENEFCO*\n\n`;

  if (cursosAbiertos.length > 0) {
    texto += `*Cursos con inscripción abierta:*\n`;
    for (let i = 0; i < cursosAbiertos.length; i++) {
      const c = cursosAbiertos[i];
      const nombre = c.nombre_curso || c.nombremat || `Curso ${i + 1}`;
      texto += `${i + 1}. ${nombre}`;
      if (c.periodo) texto += ` (${c.periodo}${c.gestion ? '/' + c.gestion : ''})`;
      texto += '\n';
    }
    texto += '\n';
  }

  texto += `*¿Cómo inscribirte?*\n`;
  texto += `1️⃣ Ingresa a nuestra plataforma web: ${BOT.web}\n`;
  texto += `2️⃣ Selecciona el curso o programa de tu interés\n`;
  texto += `3️⃣ Completa el formulario de preinscripción\n`;
  texto += `4️⃣ Acércate a nuestras oficinas para formalizar tu inscripción\n\n`;
  texto += `📌 *Requisitos generales:*\n`;
  texto += `• Fotocopia del CI (Carnet de Identidad)\n`;
  texto += `• Fotografía reciente (tamaño carnet)\n`;
  texto += `• Pago de matrícula según el curso elegido\n\n`;
  texto += `📞 Para más información: ${BOT.telefono}\n`;
  texto += `📧 ${BOT.email}\n\n`;
  texto += `Escribe *0* para volver al menú principal.`;

  await sendText(sock, jid, texto);
  await setState(phone(jid), 'menu');
}

export async function showPreinscripcion(sock, jid) {
  const BOT = await getBot();
  await sendText(sock, jid,
    `📝 *Preinscripción en línea*\n\n` +
    `Puedes completar tu preinscripción desde nuestra plataforma web:\n\n` +
    `🌐 ${BOT.web}/inscripcion\n\n` +
    `O visítanos directamente en nuestras oficinas:\n` +
    `📍 ${BOT.direccion}\n` +
    `🕐 ${BOT.horarios}\n\n` +
    `¿Necesitas ayuda con el proceso? Escribe *soporte* para hablar con un asesor.`
  );
  await setState(phone(jid), 'menu');
}
