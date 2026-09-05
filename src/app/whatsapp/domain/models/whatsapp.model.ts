export interface WhatsappAsesor {
  id: number;
  nombre: string;
  telefono: string;
  email: string | null;
  especialidad: string | null;
  disponible: boolean;
  activo: boolean;
}

export interface WhatsappEtiqueta {
  id: number;
  nombre: string;
  color: string;
}

export interface WhatsappConversacion {
  id: number;
  phone: string;
  phone_display: string | null;
  nombre: string | null;
  estado: string;
  contexto: Record<string, any> | null;
  cliente_id: number | null;
  asesor_id: number | null;
  asesor: WhatsappAsesor | null;
  etiquetas: WhatsappEtiqueta[];
  updated_at: string | null;
  created_at: string | null;
}

export interface WhatsappMensaje {
  id: number;
  direccion: 'entrante' | 'saliente';
  tipo: string;
  contenido: string | null;
  created_at: string | null;
}

export type BaileysConnectionState = 'close' | 'connecting' | 'open' | 'qr';

export interface BaileysStatus {
  state: BaileysConnectionState;
  hasQr: boolean;
}

export type CuentaStatus = 'open' | 'connecting' | 'qr' | 'close';

export interface WhatsappCuenta {
  id: string;
  nombre: string;
  phone: string | null;
  status: CuentaStatus;
  hasQr: boolean;
  estado: 'activo' | 'inactivo';
  created_at: string | null;
}

export interface NluContextRef {
  name: string;
  lifespan: number;
}

export interface NluIntent {
  name: string;
  slug: string;
  domain: string;
  priority: number;
  events: string[];
  inputContexts: string[];
  outputContexts: NluContextRef[];
  trainingCount: number;
  trainingPhrases: string[];
  responsesCount: number;
  responses: string[];
  action: string;
}

export interface IntentRecord {
  id: number;
  nombre: string;
  slug: string;
  dominio: string;
  prioridad: number;
  eventos: string[];
  input_contexts: string[];
  output_contexts: NluContextRef[];
  frases_entrenamiento: string[];
  respuestas: string[];
  accion: string;
  activo: boolean;
  orden: number;
  created_at: string | null;
  updated_at: string | null;
}

export interface IntentPayload {
  nombre: string;
  slug: string;
  dominio: string;
  prioridad: number;
  eventos: string[];
  input_contexts: string[];
  output_contexts: NluContextRef[];
  frases_entrenamiento: string[];
  respuestas: string[];
  accion: string;
  activo: boolean;
  orden: number;
}

export interface NluTestResult {
  input: string;
  result: {
    intent: string;
    confidence: number;
    confidenceLabel: 'ALTA' | 'MEDIA' | 'BAJA' | 'MUY BAJA';
    source: 'static' | 'speech';
    action: string | null;
    directResponse: string | null;
    response: string | null;
    newContextStack: NluContextRef[];
    outputContexts: NluContextRef[];
  } | null;
}

export interface ChatbotMensajeDia {
  dia: string;
  entrantes: number;
  salientes: number;
}

export interface ChatbotStats {
  mensajes_por_dia: ChatbotMensajeDia[];
  total_conversaciones: number;
  en_soporte: number;
  activas_24h: number;
  entrantes_hoy: number;
  salientes_hoy: number;
}
