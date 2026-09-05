import 'dotenv/config';
import { createServer } from 'http';
import express from 'express';
import cors from 'cors';
import { Server as SocketIO } from 'socket.io';
import apiRouter    from './routes/api.js';
import cuentasRouter from './routes/cuentas.js';
import { testConnection, initCuentasTable, getCuentas, createCuenta } from './store.js';
import { setIoManager, initAllCuentas, connectCuenta, disconnectAndReconnect } from './manager.js';
import { randomUUID } from 'crypto';

const PORT          = process.env.PORT        || 3001;
const CLIENT_ORIGIN = process.env.CLIENT_ORIGIN || 'http://localhost:4200';

const app        = express();
const httpServer = createServer(app);

const io = new SocketIO(httpServer, {
  cors: { origin: CLIENT_ORIGIN, methods: ['GET', 'POST'] },
});

setIoManager(io);

io.on('connection', socket => {
  socket.on('disconnect', () => {});
});

app.use(cors({ origin: CLIENT_ORIGIN }));
app.use(express.json());
app.use('/', apiRouter);
app.use('/cuentas', cuentasRouter);
app.get('/health', (_req, res) => res.json({ ok: true }));

// Inicia/reconecta todas las cuentas activas sin reiniciar el proceso
app.post('/connect-active', async (_req, res) => {
  try {
    await initAllCuentas();
    res.json({ message: 'Cuentas activas inicializadas' });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

// Desvincula sesión y genera nuevo QR (no marca inactivo)
app.post('/disconnect-and-reconnect', async (req, res) => {
  try {
    const { id } = req.body;
    if (!id) return res.status(422).json({ message: 'Se requiere id de cuenta' });
    await disconnectAndReconnect(id);
    res.json({ message: 'Sesión desvinculada. Generando nuevo QR…' });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

httpServer.listen(PORT, async () => {
  console.log(`[WhatsApp Service] Escuchando en http://localhost:${PORT}`);
  try {
    await testConnection();
    await initCuentasTable();
  } catch (err) {
    console.error('[DB] Error al conectar:', err.message);
    process.exit(1);
  }
  console.log('[WhatsApp Service] Inicializando cuentas activas…');
  const cuentas = await getCuentas();
  if (cuentas.length === 0) {
    const id = randomUUID();
    await createCuenta(id, 'CENEFCO Bot');
    console.log('[WhatsApp Service] Cuenta por defecto creada, conectando…');
    await connectCuenta(id, 'CENEFCO Bot');
  } else {
    await initAllCuentas();
  }
});
