import { fileURLToPath } from 'url';
import { dirname, resolve } from 'path';
import { existsSync } from 'fs';
import { logMensaje, getBotSettings } from '../store.js';

const __dirname  = dirname(fileURLToPath(import.meta.url));
export const LOGO_PATH  = resolve(__dirname, '../assets/logo-cenefco.png');
export const FONDO_PATH = resolve(__dirname, '../assets/fondo-cenefco.jpg');

export function phone(jid) {
  const [userPart, domain] = jid.split('@');
  const number = userPart.split(':')[0];
  return domain ? `${number}@${domain}` : number;
}

const TYPING_MS_PER_CHAR = 18;
const TYPING_MIN_MS      = 400;
const TYPING_MAX_MS      = 2500;

export async function sendText(sock, jid, text) {
  const delay = Math.min(
    Math.max(text.length * TYPING_MS_PER_CHAR, TYPING_MIN_MS),
    TYPING_MAX_MS
  );
  try {
    await sock.sendPresenceUpdate('composing', jid);
    await new Promise(r => setTimeout(r, delay));
    await sock.sendPresenceUpdate('paused', jid);
  } catch (_) {}
  await sock.sendMessage(jid, { text });
  try { await logMensaje(phone(jid), 'saliente', 'text', text); } catch (_) {}
}

export async function sendImage(sock, jid, imagePath, caption = '') {
  const isUrl = imagePath.startsWith('http://') || imagePath.startsWith('https://');
  if (!isUrl && !existsSync(imagePath)) return;
  try {
    await sock.sendMessage(jid, { image: { url: imagePath }, caption });
    await logMensaje(phone(jid), 'saliente', 'image', caption || '[Imagen]');
  } catch (err) {
    console.error('[Bot] Error enviando imagen:', imagePath, err.message);
  }
}

export function extractText(msg) {
  return (
    msg.message?.conversation ||
    msg.message?.extendedTextMessage?.text ||
    msg.message?.listResponseMessage?.singleSelectReply?.selectedRowId ||
    msg.message?.buttonsResponseMessage?.selectedButtonId ||
    ''
  ).trim();
}

export async function getBot() {
  const s = await getBotSettings();
  return {
    ...s,
    // alias para compatibilidad con handlers que usan BOT.ubicacion.*
    ubicacion: {
      direccion:  s.direccion,
      referencia: s.referencia,
      maps_link:  s.maps,
      latitude:   s.latitud,
      longitude:  s.longitud,
    },
  };
}
