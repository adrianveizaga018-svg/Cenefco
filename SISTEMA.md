# CENEFCO — ¿Cómo funciona el sistema?

## Visión general

CENEFCO es una plataforma educativa que permite gestionar cursos, inscripciones, pagos, certificados y comunicación con estudiantes, todo desde un panel de administración.

---

## ¿Quiénes usan el sistema?

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│   👤 ADMINISTRADOR          👨‍🏫 PERSONAL ACADÉMICO          │
│   Gestiona todo el          Gestiona cursos,               │
│   sistema                   estudiantes y notas            │
│                                                             │
│   👩‍💼 AGENTE DE VENTAS       🌐 PORTAL WEB PÚBLICO           │
│   Atiende consultas         Página visible para            │
│   por WhatsApp              cualquier visitante            │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## Módulos del sistema

```
┌─────────────────────────────────────────────────────────────────────┐
│                        PANEL DE ADMINISTRACIÓN                      │
│                                                                     │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────────────┐   │
│  │  👥 USUARIOS  │  │  📚 CURSOS    │  │  💬 MENSAJERÍA        │   │
│  │               │  │               │  │                       │   │
│  │ Crear cuentas │  │ Crear y editar│  │ Chat por WhatsApp     │   │
│  │ Asignar roles │  │ cursos        │  │ Chat por Telegram     │   │
│  │ Ver permisos  │  │ Ver inscritos │  │ Envío masivo          │   │
│  │               │  │ Exportar listas│  │ Bot automático        │   │
│  └───────────────┘  └───────────────┘  └───────────────────────┘   │
│                                                                     │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────────────┐   │
│  │  💳 PAGOS     │  │  🎓 CERTIFIC. │  │  🌐 CONTENIDO WEB     │   │
│  │               │  │               │  │                       │   │
│  │ Registrar     │  │ Generar cert. │  │ Noticias / Eventos    │   │
│  │ pagos         │  │ con QR        │  │ Banners               │   │
│  │ Historial     │  │ Validar en    │  │ Preguntas frecuentes  │   │
│  │ Stripe online │  │ línea         │  │ Redes sociales        │   │
│  └───────────────┘  └───────────────┘  └───────────────────────┘   │
│                                                                     │
│  ┌───────────────┐  ┌───────────────┐                              │
│  │  📊 REPORTES  │  │  ⚙️ AJUSTES   │                              │
│  │               │  │               │                              │
│  │ Estadísticas  │  │ Configuración │                              │
│  │ Exportar PDF  │  │ del sitio     │                              │
│  │ Exportar Excel│  │               │                              │
│  └───────────────┘  └───────────────┘                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Ciclo de vida de un estudiante

```
   1. INTERÉS              2. INSCRIPCIÓN           3. CURSANDO
  ┌────────────┐          ┌────────────────┐       ┌──────────────┐
  │            │          │                │       │              │
  │ Visita la  │─────────►│ Llena formulario│──────►│ Accede a     │
  │ web o      │          │ de preinscr.   │       │ clases en    │
  │ escribe por│          │                │       │ Moodle (LMS) │
  │ WhatsApp   │          │ Sube documentos│       │              │
  │            │          │                │       │ Seguimiento  │
  └────────────┘          │ Realiza el pago│       │ de notas     │
                          └────────────────┘       └──────┬───────┘
                                                          │
   4. EGRESO                                             ▼
  ┌────────────┐                                  5. CERTIFICADO
  │            │                                 ┌──────────────┐
  │ Aparece en │◄────────────────────────────────│              │
  │ historial  │                                 │ Se genera    │
  │ y reportes │                                 │ certificado  │
  │            │                                 │ con código   │
  └────────────┘                                 │ QR validable │
                                                 └──────────────┘
```

---

## ¿Cómo funciona el chat con WhatsApp?

```
  ESTUDIANTE                   SISTEMA CENEFCO
  ──────────                   ───────────────

  Escribe un         ─────►    Bot responde
  mensaje                      automáticamente
                               (consultas, info
                                de cursos, horarios)
       │
       │ Si necesita
       │ atención humana
       ▼
  Sigue esperando   ◄─────    Agente recibe la
                              conversación en el
                              panel y responde

                              El agente puede:
                              ✓ Etiquetar la conv.
                              ✓ Marcarla como atendida
                              ✓ Enviar archivos
                              ✓ Usar plantillas
                              ✓ Enviar mensajes masivos
```

---

## Sistema de roles y accesos

```
  ADMINISTRADOR              ACADÉMICO               AGENTE
  ─────────────              ─────────               ──────
  Acceso total          Solo ve cursos          Solo ve
                        e inscripciones         WhatsApp y
  ✓ Usuarios                                    mensajes
  ✓ Roles               ✓ Cursos
  ✓ Todo lo demás       ✓ Estudiantes           ✓ Conversaciones
                        ✓ Notas                 ✓ Etiquetas
                        ✓ Certificados          ✓ Envíos
```

---

## Integraciones con servicios externos

```
  CENEFCO se conecta con:

  ┌──────────┐   Envío y recepción     ┌──────────────────┐
  │          │   de mensajes           │                  │
  │ CENEFCO  │◄───────────────────────►│   WhatsApp       │
  │          │                         │   Business API   │
  │          │   Videoclases           └──────────────────┘
  │          │◄───────────────────────►  Zoom
  │          │
  │          │   Cursos online         ┌──────────────────┐
  │          │◄───────────────────────►│   Moodle (LMS)   │
  │          │                         └──────────────────┘
  │          │   Cobros online
  │          │◄───────────────────────►  Stripe
  │          │
  │          │   Notificaciones
  │          │◄───────────────────────►  Telegram
  └──────────┘
```

---

## Resumen en una sola imagen

```
                        ┌─────────────────────┐
                        │   PORTAL PÚBLICO     │
                        │  (web de CENEFCO)    │
                        │                     │
                        │ Eventos · Noticias  │
                        │ FAQ · Banners       │
                        └──────────┬──────────┘
                                   │
                        ┌──────────▼──────────┐
                        │   PANEL CENEFCO      │
                        │  (administración)    │
                        └──┬────┬────┬────┬───┘
                           │    │    │    │
               ┌───────────┘    │    │    └───────────┐
               ▼                ▼    ▼                ▼
         ┌──────────┐  ┌──────────┐ ┌──────────┐  ┌──────────┐
         │ Usuarios │  │  Cursos  │ │  Pagos   │  │ WhatsApp │
         │ y Roles  │  │ y Certif.│ │ y Stripe │  │ y Bots   │
         └──────────┘  └──────────┘ └──────────┘  └──────────┘
                              │
                        ┌─────▼──────┐
                        │   Moodle   │
                        │    LMS     │
                        └────────────┘
```
