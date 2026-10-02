<?php

namespace App\Infrastructure\Shared\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

class AgentService
{
    private string $model;
    private int $maxTokens;
    private float $temperature;
    private int $maxInputChars;
    private int $rateLimit;

    private array $areas = [
        'derecho'    => ['derecho', 'juridico', 'legal', 'abogado', 'abogada', 'leyes', 'ley', 'normativa', 'legislacion', 'notarial', 'notario', 'fiscal', 'penal', 'civil', 'deportivo'],
        'salud'      => ['salud', 'medico', 'medicina', 'enfermeria', 'farmacia', 'clinico', 'nutricion', 'odontologia', 'psicologia'],
        'educacion'  => ['educacion', 'pedagogia', 'docencia', 'ensenanza', 'aprendizaje', 'maestro', 'docente', 'curricular', 'didactica'],
        'tecnologia' => ['tecnologia', 'informatica', 'sistemas', 'software', 'programacion', 'ti', 'datos', 'inteligencia artificial', 'redes', 'linux', 'windows', 'computacion', 'computadora', 'base de datos', 'web', 'internet', 'ciberseguridad', 'python', 'java', 'excel', 'office'],
        'negocios'   => ['negocios', 'empresa', 'administracion', 'gestion', 'finanzas', 'contabilidad', 'marketing', 'emprendimiento', 'comercio'],
        'deporte'    => ['deporte', 'deportivo', 'atletismo', 'futbol', 'actividad fisica', 'entrenamiento', 'cultura fisica'],
        'medio_ambiente' => ['medio ambiente', 'ambiental', 'ecologia', 'sostenibilidad', 'recursos naturales'],
    ];

    private array $forbidden = [
        'ignore previous', 'forget instructions', 'system prompt', 'ignore all',
        'new instructions', 'you are now', 'act as', 'pretend you', 'jailbreak',
        'dan mode', 'ignore your', 'disregard', 'override', 'bypass',
        'do anything now', 'developer mode', 'unrestricted mode', 'no restrictions',
        'ignore the above', 'forget the above', 'new persona', 'repeat after me', 'say exactly',
        'sin restricciones', 'ignora tus instrucciones', 'olvida tus instrucciones',
        'actua como', 'actúa como', 'finge ser', 'eres ahora', 'nuevo rol',
        'nueva personalidad', 'sin limites', 'sin límites', 'ignora todo', 'olvida todo',
        'modo desarrollador', 'modo sin restricciones', 'di exactamente', 'repite esto',
        'ignora las reglas', 'salta las reglas',
    ];

    public function __construct()
    {
        $this->model         = config('bot.ia.model', 'qwen2.5:7b');
        $this->maxTokens     = config('bot.ia.max_tokens', 512);
        $this->temperature   = config('bot.ia.temperature', 0.3);
        $this->maxInputChars = config('bot.ia.max_input_chars', 500);
        $this->rateLimit     = config('bot.ia.rate_limit', 20);
    }

    public function responder(string $phone, string $userInput, array $historial = []): ?string
    {
        $input = $this->sanitize($userInput);

        if ($input === '') {
            return '¿En qué puedo ayudarte? Escribe tu consulta o escribe *menú* para ver las opciones. 😊';
        }

        if ($this->esTrivial($input)) {
            return '¿Podrías ser más específico? No pude entender tu mensaje. Escribe tu consulta o escribe *menú* para ver las opciones disponibles.';
        }

        if ($this->esSpam($input)) {
            return 'Por favor escribe un mensaje con palabras para poder ayudarte mejor.';
        }

        if (! $this->checkRateLimit($phone)) {
            return '⏳ Estás enviando muchos mensajes. Por favor espera un momento.';
        }

        if ($this->esJailbreak($input)) {
            Log::warning('[AgentService] Intento de jailbreak bloqueado', [
                'phone' => $phone,
                'input' => $input,
            ]);

            return 'Lo siento, no puedo procesar esa solicitud. ¿En qué más puedo ayudarte?';
        }

        $area    = $this->detectarArea($input);
        $cacheKey = $area ? "bot_ia_contexto_{$area}" : 'bot_ia_contexto';
        $contexto = Cache::remember($cacheKey, 300, fn () => $this->buildContext($area));
        $prompt   = $this->buildSystemPrompt($contexto);

        $messages = $this->buildMessages($historial, $input);

        $inicio = microtime(true);
        $output = null;

        try {
            $response = Prism::text()
                ->using(Provider::Ollama, $this->model)
                ->withSystemPrompt($prompt)
                ->withMessages($messages)
                ->withMaxTokens($this->maxTokens)
                ->usingTemperature($this->temperature)
                ->asText();

            $output = trim($response->text);

            $this->logInteraccion(
                phone: $phone,
                input: $input,
                prompt: $prompt,
                output: $output,
                tokensIn: $response->usage->promptTokens ?? null,
                tokensOut: $response->usage->completionTokens ?? null,
                latencia: (int) ((microtime(true) - $inicio) * 1000),
                error: false,
            );

            return $output ?: null;

        } catch (\Throwable $e) {
            Log::error('[AgentService] Error Ollama', [
                'phone' => $phone,
                'input' => $input,
                'error' => $e->getMessage(),
            ]);

            $this->logInteraccion(
                phone: $phone,
                input: $input,
                prompt: $prompt,
                output: null,
                tokensIn: null,
                tokensOut: null,
                latencia: (int) ((microtime(true) - $inicio) * 1000),
                error: true,
            );

            return null;
        }
    }

    private function buildMessages(array $historial, string $inputActual): array
    {
        $messages = [];

        foreach ($historial as $msg) {
            $contenido = $msg['contenido'] ?? null;
            if (! $contenido) {
                continue;
            }
            if ($msg['direccion'] === 'entrante') {
                $messages[] = new UserMessage($this->sanitize($contenido));
            } elseif ($msg['direccion'] === 'saliente') {
                $messages[] = new AssistantMessage($contenido);
            }
        }

        $messages[] = new UserMessage($inputActual);

        return $messages;
    }

    private function detectarArea(string $input): ?string
    {
        $normalized = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $input) ?: $input);

        foreach ($this->areas as $area => $terminos) {
            foreach ($terminos as $termino) {
                if (str_contains($normalized, $termino)) {
                    return $area;
                }
            }
        }

        return null;
    }

    private function sanitize(string $input): string
    {
        $input = strip_tags($input);
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $input) ?? $input;
        $input = mb_substr(trim($input), 0, $this->maxInputChars);

        return $input;
    }

    private function esTrivial(string $input): bool
    {
        $sinEmojis    = preg_replace('/[\x{1F000}-\x{1FFFF}]|[\x{2600}-\x{27BF}]/u', '', $input) ?? $input;
        $sinEspeciales = preg_replace('/[^a-zA-ZáéíóúüñÁÉÍÓÚÜÑ\s]/u', '', $sinEmojis) ?? '';

        return mb_strlen(trim($sinEspeciales)) < 2;
    }

    private function esSpam(string $input): bool
    {
        if (preg_match('/^(.)\1{9,}$/u', $input)) {
            return true;
        }
        if (preg_match('/\b(\w+)\b(?:\s+\1\b){5,}/ui', $input)) {
            return true;
        }

        return false;
    }

    private function esJailbreak(string $input): bool
    {
        $normalized = strtolower($input);
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalized) ?: $normalized;
        $normalized = preg_replace('/\s+/', ' ', trim($normalized));

        foreach ($this->forbidden as $pattern) {
            if (str_contains($normalized, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function checkRateLimit(string $phone): bool
    {
        $key   = "wa_throttle_ia:{$phone}";
        $count = (int) Cache::get($key, 0);

        if ($count >= $this->rateLimit) {
            return false;
        }

        Cache::put($key, $count + 1, 60);

        return true;
    }

    private function buildContext(?string $area = null): string
    {
        $nombre    = config('bot.cenefco.nombre');
        $sigla     = config('bot.cenefco.sigla');
        $acred     = config('bot.cenefco.acreditacion');
        $pagos     = config('bot.cenefco.pagos');
        $contacto  = config('bot.cenefco.contacto');
        $ubicacion = config('bot.cenefco.ubicacion');
        $horarios  = collect(config('bot.cenefco.horarios'))
            ->map(fn ($h, $d) => "{$d}: {$h}")
            ->implode(', ');

        $programas = $this->fetchProgramas($area);

        if ($programas === '' && $area !== null) {
            $programas = $this->fetchProgramas(null);
        }

        $docentes = DB::table('web_docente_perfil')
            ->whereNull('deleted_at')
            ->where('mostrar_en_web', 1)
            ->where('estado', 'publicado')
            ->orderBy('orden')
            ->limit(8)
            ->get(['nombre_completo', 'titulo_academico', 'especialidad'])
            ->map(fn ($d) => "- {$d->nombre_completo}"
                .($d->titulo_academico ? " ({$d->titulo_academico})" : '')
                .($d->especialidad ? " — {$d->especialidad}" : '')
            )
            ->implode("\n");

        $eventos = DB::table('web_evento')
            ->whereNull('deleted_at')
            ->where('estado', 'publicado')
            ->where('fecha_inicio', '>=', now())
            ->orderBy('fecha_inicio')
            ->limit(5)
            ->get(['titulo', 'fecha_inicio', 'lugar', 'modalidad'])
            ->map(fn ($e) => "- {$e->titulo}"
                .($e->fecha_inicio ? ' ('.date('d/m/Y', strtotime($e->fecha_inicio)).')' : '')
                .($e->lugar ? " en {$e->lugar}" : '')
                .($e->modalidad ? " [{$e->modalidad}]" : '')
            )
            ->implode("\n");

        return <<<CONTEXT
        INSTITUCIÓN: {$nombre} ({$sigla})
        DIRECCIÓN: {$ubicacion['direccion']}
        HORARIOS DE ATENCIÓN: {$horarios}
        TELÉFONO: {$contacto['telefono']}
        EMAIL: {$contacto['email']}
        WEB: {$contacto['web']}

        ACREDITACIÓN E INSTITUCIONALIDAD:
        Aval universitario: {$acred['aval']}
        Resolución: {$acred['resolucion']}
        Autorizados por: {$acred['autoriza']}
        {$acred['descripcion']}

        MÉTODOS DE PAGO:
        {$pagos['metodos']}
        {$pagos['instrucciones']}
        {$pagos['nota']}

        PROGRAMAS Y CURSOS DISPONIBLES (con precios y detalles):
        {$programas}

        PLANTA DOCENTE:
        {$docentes}

        PRÓXIMOS EVENTOS:
        {$eventos}
        CONTEXT;
    }

    private function fetchProgramas(?string $area): string
    {
        $query = DB::table('t_programa as p')
            ->leftJoin('t_tipoprograma as tip', function ($join) {
                $join->on('p.id_tipoprograma', '=', 'tip.id_tipoprograma')
                    ->on('p.id_us_reg', '=', 'tip.id_us_reg');
            })
            ->where('p.estado', 1)
            ->whereNull('p.deleted_at')
            ->orderBy('p.orden');

        if ($area !== null && isset($this->areas[$area])) {
            $terminos = $this->areas[$area];
            $query->where(function ($q) use ($terminos) {
                foreach ($terminos as $termino) {
                    $q->orWhere('p.nombre_programa', 'like', "%{$termino}%")
                        ->orWhere('p.dirigido', 'like', "%{$termino}%")
                        ->orWhere('p.objetivo', 'like', "%{$termino}%");
                }
            });
        }

        return $query->get([
            'p.nombre_programa',
            'tip.nombre_tipoprograma',
            'p.dirigido',
            'p.inversion',
            'p.creditaje',
            'p.inicio_actividades',
            'p.finalizacion_actividades',
            'p.inicio_inscripciones',
            'p.objetivo',
            'p.nota',
        ])->map(function ($p) {
            $linea = "- {$p->nombre_programa}";
            if ($p->nombre_tipoprograma) {
                $linea .= " [{$p->nombre_tipoprograma}]";
            }
            if ($p->inversion) {
                $linea .= " | Precio: Bs. {$p->inversion}";
            }
            if ($p->creditaje) {
                $linea .= " | Créditos/horas: {$p->creditaje}";
            }
            if ($p->dirigido) {
                $linea .= " | Dirigido a: {$p->dirigido}";
            }
            if ($p->inicio_actividades && $p->inicio_actividades !== '2000-01-01') {
                $linea .= " | Inicio: ".date('d/m/Y', strtotime($p->inicio_actividades));
            }
            if ($p->finalizacion_actividades && $p->finalizacion_actividades !== '2000-01-01') {
                $linea .= " | Fin: ".date('d/m/Y', strtotime($p->finalizacion_actividades));
            }
            if ($p->inicio_inscripciones && $p->inicio_inscripciones !== '2000-01-01') {
                $linea .= " | Inscripciones desde: ".date('d/m/Y', strtotime($p->inicio_inscripciones));
            }
            if ($p->objetivo) {
                $linea .= "\n  Objetivo: {$p->objetivo}";
            }
            if ($p->nota) {
                $linea .= "\n  Nota: {$p->nota}";
            }

            return $linea;
        })->implode("\n");
    }

    private function buildSystemPrompt(string $contexto): string
    {
        $nombre = config('bot.cenefco.nombre');

        return <<<SYSTEM
        Eres el asistente virtual oficial de *{$nombre}*, un centro de formación continua y posgrado. Respondes preguntas de estudiantes y personas interesadas por WhatsApp.

        REGLAS ESTRICTAS:
        1. Responde ÚNICAMENTE con información del contexto provisto. Si no tienes el dato exacto, indica al usuario que consulte directamente con la institución o escriba "soporte".
        2. Responde SIEMPRE en español, de forma clara, amable y concisa. Si el usuario escribe en otro idioma, respóndele en español e indícale que solo atiendes en español.
        3. Nunca inventes datos, precios, fechas, temáticas ni requisitos que no estén en el contexto.
        4. Solo respondes preguntas sobre {$nombre}, sus programas, docentes, eventos, precios, inscripciones y acreditación. Si la pregunta es de otro tema, indica amablemente que solo puedes ayudar con temas académicos de {$nombre}.
        5. Usa formato simple apto para WhatsApp: listas con guiones, sin markdown complejo, sin tablas.
        6. Si el usuario quiere hablar con una persona o pagar, indícale que escriba "soporte" para que un asesor lo atienda.
        7. MÚLTIPLES PREGUNTAS: Si el usuario hace varias preguntas en un solo mensaje, respóndelas TODAS en orden, separando cada respuesta con una línea en blanco.
        8. RECOMENDACIONES POR PERFIL: Si el usuario menciona su profesión o área ("soy abogado", "trabajo en salud"), revisa el campo "Dirigido a" y "Objetivo" de cada programa y recomienda los más pertinentes con nombre y precio.
        9. PRECIOS: Si preguntan por precio/costo/inversión, responde con el precio del contexto. Si no está disponible, indica que consulten con un asesor.
        10. ACREDITACIÓN/AVAL: Si preguntan por aval, resolución ministerial, quién los autoriza o validez de títulos, usa la sección ACREDITACIÓN E INSTITUCIONALIDAD.
        11. PAGOS: Si preguntan por cómo pagar, QR, transferencia o cuotas, usa la sección MÉTODOS DE PAGO.
        12. Si te preguntan si eres humano, robot o IA: responde honestamente que eres el asistente virtual de {$nombre} y que para atención humana escriban "soporte".
        13. Si el usuario es grosero o agresivo: responde con calma y profesionalismo, sin igualarte a su tono.
        14. Si mencionan un área (derecho, salud, etc.) sin programas disponibles en el contexto: indícalo honestamente y sugiere contactar a un asesor.
        15. CONTINUIDAD: Si el historial muestra mensajes previos, úsalos para entender el contexto de la conversación (pronombres como "ese", "el mismo", "cuánto cuesta" se refieren a lo mencionado antes).

        CONTEXTO ACTUALIZADO DE LA INSTITUCIÓN:
        {$contexto}
        SYSTEM;
    }

    private function logInteraccion(
        string $phone,
        string $input,
        string $prompt,
        ?string $output,
        ?int $tokensIn,
        ?int $tokensOut,
        int $latencia,
        bool $error,
    ): void {
        try {
            DB::table('whatsapp_ia_logs')->insert([
                'phone'       => $phone,
                'input'       => $input,
                'prompt'      => mb_substr($prompt, 0, 5000),
                'output'      => $output ? mb_substr($output, 0, 2000) : null,
                'modelo'      => $this->model,
                'tokens_in'   => $tokensIn,
                'tokens_out'  => $tokensOut,
                'latencia_ms' => $latencia,
                'error'       => $error,
                'created_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[AgentService] No se pudo registrar interacción', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
