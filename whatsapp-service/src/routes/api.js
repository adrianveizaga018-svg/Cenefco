import { Router } from 'express';
import QRCode from 'qrcode';
import multer from 'multer';
import {
  getInstances,
  disconnectCuenta as disconnectCuentaManager,
  disconnectAndReconnect,
  sendFromCuenta,
} from '../manager.js';
import {
  listConversaciones,
  getMensajes,
  getAllPhones,
  marcarAtendido,
  getConversacion,
  logMensaje,
  getPool,
  loadIntents,
  invalidateIntentCache,
  invalidateSettingsCache,
  invalidateConfigSitioCache,
} from '../store.js';
import { detectIntent, confidenceLabel } from '../nlu/engine.js';

const upload = multer({ storage: multer.memoryStorage() });
const router = Router();

function getAggregateStatus() {
  const instances = getInstances();
  for (const [, inst] of instances) {
    if (inst.status === 'open')       return { state: 'open',       hasQr: false };
  }
  for (const [, inst] of instances) {
    if (inst.status === 'qr')         return { state: 'qr',         hasQr: true  };
  }
  for (const [, inst] of instances) {
    if (inst.status === 'connecting') return { state: 'connecting', hasQr: false };
  }
  return { state: 'close', hasQr: false };
}

function getFirstQrInstance() {
  for (const [, inst] of getInstances()) {
    if (inst.status === 'qr' && inst.qr) return inst;
  }
  return null;
}

function getFirstOpenId() {
  for (const [id, inst] of getInstances()) {
    if (inst.status === 'open') return id;
  }
  return null;
}


router.get('/status', (_req, res) => {
  res.json(getAggregateStatus());
});

router.get('/qr', async (_req, res) => {
  const inst = getFirstQrInstance();
  if (!inst) {
    return res.status(404).json({ message: 'No hay código QR disponible. El bot puede ya estar conectado.' });
  }
  try {
    const dataUrl = await QRCode.toDataURL(inst.qr, { width: 300, margin: 2 });
    res.json({ qr: dataUrl });
  } catch {
    res.status(500).json({ message: 'Error al generar el código QR' });
  }
});

router.get('/qr-view', async (_req, res) => {
  const inst = getFirstQrInstance();
  if (!inst) {
    return res.send(`<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:40px">
      <h2>Sin QR disponible</h2><p>El bot ya está conectado o aún no generó el QR.</p>
      <script>setTimeout(()=>location.reload(),3000)</script>
    </body></html>`);
  }
  try {
    const dataUrl = await QRCode.toDataURL(inst.qr, { width: 300, margin: 2 });
    res.send(`<!DOCTYPE html><html><head><title>WhatsApp QR — CENEFCO</title></head>
      <body style="font-family:sans-serif;text-align:center;padding:40px;background:#f0f0f0">
        <h2>Escanea con WhatsApp — CENEFCO</h2>
        <p>WhatsApp → Dispositivos vinculados → Vincular dispositivo</p>
        <img src="${dataUrl}" style="border:8px solid white;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.2)"/>
        <p style="color:#888;font-size:13px">Esta página se recarga automáticamente cada 20 segundos</p>
        <script>setTimeout(()=>location.reload(),20000)</script>
      </body></html>`);
  } catch {
    res.status(500).send('Error al generar el QR');
  }
});

router.post('/disconnect', async (_req, res) => {
  try {
    const instances = getInstances();

    if (instances.size > 0) {
      // Hay instancias en memoria: hacer logout de Baileys (el handler connection.close
      // detectará loggedOut y limpiará auth + reconectará para generar QR nuevo)
      for (const [, inst] of instances) {
        try {
          if (inst.status === 'open') {
            await inst.sock.logout();
          } else {
            // No está abierta: desconectar y reconectar manualmente
            inst.sock.ws?.close();
          }
        } catch (_) {}
      }
    } else {
      // Sin instancias en memoria: limpiar auth y reconectar manualmente
      await disconnectAndReconnect(null);
    }

    res.json({ message: 'Sesiones desvinculadas. Generando QR nuevo…' });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.get('/conversaciones', async (req, res) => {
  try {
    const { query = '', estado = '', pageIndex = 1, pageSize = 15 } = req.query;
    const result = await listConversaciones({
      query,
      estado,
      page: parseInt(pageIndex),
      pageSize: parseInt(pageSize),
    });
    res.json(result);
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.get('/conversaciones/:phone/mensajes', async (req, res) => {
  try {
    const { phone } = req.params;
    const conv = await getConversacion(phone);
    if (!conv) return res.status(404).json({ message: 'Conversación no encontrada' });

    const mensajes = await getMensajes(phone);
    res.json({
      data: mensajes,
      phone: conv.phone,
      nombre: conv.nombre,
      estado: conv.estado,
    });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.post('/conversaciones/:phone/atendido', async (req, res) => {
  try {
    await marcarAtendido(req.params.phone);
    res.json({ message: 'Conversación marcada como atendida' });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.post('/enviar', async (req, res) => {
  const { phone, mensaje, cuenta_id } = req.body;
  if (!phone || !mensaje) {
    return res.status(422).json({ message: 'Se requiere phone y mensaje' });
  }
  const id = cuenta_id ?? getFirstOpenId();
  if (!id) return res.status(503).json({ message: 'No hay ninguna cuenta de WhatsApp conectada' });
  try {
    await sendFromCuenta(id, phone, mensaje);
    await logMensaje(phone, 'saliente', 'text', mensaje);
    res.json({ message: 'Mensaje enviado correctamente' });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.post('/enviar-masivo', async (req, res) => {
  const { phones, mensaje, cuenta_id } = req.body;
  if (!phones?.length || !mensaje) {
    return res.status(422).json({ message: 'Se requieren phones y mensaje' });
  }
  const id = cuenta_id ?? getFirstOpenId();
  if (!id) return res.status(503).json({ message: 'No hay ninguna cuenta de WhatsApp conectada' });

  let exitosos = 0;
  const detalleFallidos = [];

  for (const phone of phones) {
    try {
      await sendFromCuenta(id, phone, mensaje);
      await logMensaje(phone, 'saliente', 'text', mensaje);
      exitosos++;
    } catch (err) {
      detalleFallidos.push({ phone, error: err.message });
    }
  }

  res.json({ exitosos, fallidos: detalleFallidos.length, detalle_fallidos: detalleFallidos });
});

router.post('/enviar-media', upload.single('archivo'), async (req, res) => {
  const { phones, tipo, caption = '', filename = '', cuenta_id } = req.body;
  const archivo = req.file;

  if (!phones || !archivo) {
    return res.status(422).json({ message: 'Se requieren phones y archivo' });
  }

  const id = cuenta_id ?? getFirstOpenId();
  if (!id) return res.status(503).json({ message: 'No hay ninguna cuenta de WhatsApp conectada' });

  const phoneList = Array.isArray(phones) ? phones : [phones];
  let exitosos = 0;
  const detalleFallidos = [];

  for (const phone of phoneList) {
    try {
      const inst = getInstances().get(id);
      if (!inst || inst.status !== 'open') throw new Error(`Cuenta ${id} no está conectada`);
      const jid = phone.includes('@') ? phone : `${phone.replace(/\D/g, '')}@s.whatsapp.net`;
      if (tipo === 'image') {
        await inst.sock.sendMessage(jid, { image: archivo.buffer, caption });
      } else if (tipo === 'document') {
        await inst.sock.sendMessage(jid, { document: archivo.buffer, mimetype: archivo.mimetype, fileName: filename || archivo.originalname, caption });
      }
      await logMensaje(phone.replace(/\D/g, ''), 'saliente', tipo, caption || `[${tipo}]`);
      exitosos++;
    } catch (err) {
      detalleFallidos.push({ phone, error: err.message });
    }
  }

  res.json({ exitosos, fallidos: detalleFallidos.length, detalle_fallidos: detalleFallidos });
});

router.get('/stats', async (_req, res) => {
  try {
    const db = getPool();

    const [mensajesDiaRes, generalRes, hoyRes] = await Promise.all([
      db.query(`
        SELECT TO_CHAR(DATE(created_at), 'YYYY-MM-DD') AS dia,
               COUNT(*) FILTER (WHERE direccion = 'entrante') AS entrantes,
               COUNT(*) FILTER (WHERE direccion = 'saliente') AS salientes
        FROM whatsapp_mensajes
        WHERE created_at >= NOW() - INTERVAL '7 days'
        GROUP BY dia ORDER BY dia
      `),
      db.query(`
        SELECT
          COUNT(*)                                                     AS total_conversaciones,
          COUNT(*) FILTER (WHERE estado = 'soporte')                   AS en_soporte,
          COUNT(*) FILTER (WHERE updated_at >= NOW() - INTERVAL '24 hours') AS activas_24h
        FROM whatsapp_conversaciones
      `),
      db.query(`
        SELECT
          COUNT(*) FILTER (WHERE direccion = 'entrante') AS entrantes_hoy,
          COUNT(*) FILTER (WHERE direccion = 'saliente') AS salientes_hoy
        FROM whatsapp_mensajes WHERE created_at >= CURRENT_DATE
      `),
    ]);

    const g = generalRes.rows[0];
    const h = hoyRes.rows[0];

    res.json({
      mensajes_por_dia: mensajesDiaRes.rows.map(r => ({
        dia: r.dia,
        entrantes: parseInt(r.entrantes ?? 0),
        salientes: parseInt(r.salientes ?? 0),
      })),
      total_conversaciones: parseInt(g.total_conversaciones ?? 0),
      en_soporte:           parseInt(g.en_soporte ?? 0),
      activas_24h:          parseInt(g.activas_24h ?? 0),
      entrantes_hoy:        parseInt(h.entrantes_hoy ?? 0),
      salientes_hoy:        parseInt(h.salientes_hoy ?? 0),
    });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});


router.post('/test-nlu', async (req, res) => {
  const { text, context_stack = [] } = req.body;
  if (!text?.trim()) return res.status(422).json({ message: 'text requerido' });
  try {
    const result = await detectIntent(text.trim(), context_stack);
    res.json({
      input: text.trim(),
      result: result ? {
        intent:          result.intent,
        confidence:      result.confidence,
        confidenceLabel: confidenceLabel(result.confidence),
        source:          result.source,
        action:          result.action ?? null,
        directResponse:  result.directResponse ?? null,
        response:        result.response ?? null,
        newContextStack: result.newContextStack ?? [],
        outputContexts:  result.outputContexts ?? [],
      } : null,
    });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.get('/intents', async (_req, res) => {
  try {
    const intents = await loadIntents();
    res.json({
      intents: intents.map(i => ({
        name:           i.name,
        slug:           i.slug,
        domain:         i.domain,
        priority:       i.priority,
        events:         i.events ?? [],
        inputContexts:  i.inputContexts ?? [],
        outputContexts: i.outputContexts ?? [],
        trainingCount:  i.trainingPhrases.length,
        trainingPhrases: i.trainingPhrases,
        responsesCount: (i.responses ?? []).length,
        responses:      i.responses ?? [],
        action:         i.action,
      })),
    });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});


router.post('/cache/flush', (_req, res) => {
  invalidateIntentCache();
  invalidateSettingsCache();
  invalidateConfigSitioCache();
  res.json({ message: 'Caché de intents, settings y config-sitio invalidada.' });
});

router.get('/phones', async (_req, res) => {
  try {
    const phones = await getAllPhones();
    res.json({ phones });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

export default router;
