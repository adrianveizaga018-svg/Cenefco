import { setState } from '../store.js';
import { sendText, getBot, phone } from '../utils/wa.js';

export async function handleHorario(sock, jid) {
  const BOT = await getBot();
  let texto = `🕐 *Horario de atención — ${BOT.nombre}*\n\n`;
  if (BOT.horarios) texto += `${BOT.horarios}\n\n`;
  if (BOT.telefono) texto += `📞 Teléfono: ${BOT.telefono}\n`;
  if (BOT.whatsapp) texto += `💬 WhatsApp: ${BOT.whatsapp}\n`;
  if (BOT.email)    texto += `📧 Email: ${BOT.email}\n`;
  texto += `\nEscribe *0* para volver al menú principal.`;
  await sendText(sock, jid, texto);
  await setState(phone(jid), 'menu', { welcomed: true });
}

export async function handleUbicacion(sock, jid) {
  const BOT = await getBot();
  let texto = `📍 *Ubicación — ${BOT.nombre}*\n\n`;
  if (BOT.ubicacion.direccion)  texto += `🏢 ${BOT.ubicacion.direccion}\n`;
  if (BOT.ciudad)               texto += `🏙️ ${BOT.ciudad}\n`;
  if (BOT.ubicacion.referencia) texto += `📌 Referencia: ${BOT.ubicacion.referencia}\n`;
  if (BOT.ubicacion.maps_link && BOT.ubicacion.maps_link !== 'https://maps.google.com') {
    texto += `\n🗺️ Ver en mapa: ${BOT.ubicacion.maps_link}\n`;
  }
  texto += `\nEscribe *0* para volver al menú principal.`;
  await sendText(sock, jid, texto);
  await setState(phone(jid), 'menu', { welcomed: true });
}

export async function handlePresentacion(sock, jid) {
  const BOT = await getBot();
  await sendText(sock, jid,
    `ℹ️ *¿Quién soy?*\n\n` +
    `Soy el asistente virtual de *${BOT.nombre} — Centro de Formación Continua*.\n\n` +
    `Puedo ayudarte con información sobre:\n` +
    `• 📚 Cursos y programas académicos\n` +
    `• 📝 Proceso de inscripción\n` +
    `• 💳 Pagos y costos\n` +
    `• 👨‍🏫 Docentes\n` +
    `• 📰 Noticias y boletines\n` +
    `• 📅 Eventos y actividades\n` +
    `• 🕐 Horarios y ubicación\n\n` +
    `Escribe *menú* para ver todas las opciones.`
  );
  await setState(phone(jid), 'menu', { welcomed: true });
}

export async function handleSoporte(sock, jid) {
  const BOT = await getBot();
  await sendText(sock, jid,
    `📞 *Soporte y atención personalizada*\n\n` +
    `Un asesor de *CENEFCO* se comunicará contigo a la brevedad.\n\n` +
    `También puedes contactarnos directamente:\n` +
    `📞 ${BOT.telefono}\n` +
    `📧 ${BOT.email}\n` +
    `🌐 ${BOT.web}\n\n` +
    `Tu mensaje ha sido registrado. Escribe lo que necesitas y te atenderemos.`
  );
  await setState(phone(jid), 'soporte', { welcomed: true, soporte_intentos: 0 });
}
