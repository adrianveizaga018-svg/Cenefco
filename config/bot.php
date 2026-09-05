<?php

return [

    'cenefco' => [
        'nombre' => env('BOT_cenefco_NOMBRE', 'CENEFCO'),
        'sigla' => env('BOT_cenefco_SIGLA', 'CENEFCO'),

        'horarios' => [
            'Lunes a Viernes' => '8:00 AM - 12:00 PM · 2:30 PM - 6:30 PM',
            'Sábado' => '8:00 AM - 12:00 PM',
            'Domingo' => 'Cerrado',
        ],

        'ubicacion' => [
            'direccion' => env('BOT_UBICACION_DIRECCION', 'Plaza Principal s/n'),
            'referencia' => env('BOT_UBICACION_REFERENCIA', 'Frente a la Plaza Principal'),
            'maps_link' => env('BOT_MAPS_LINK', 'https://maps.google.com/?q=cenefco'),
            'latitude' => (float) env('BOT_LATITUD', -17.3895),
            'longitude' => (float) env('BOT_LONGITUD', -66.1568),
        ],

        'contacto' => [
            'telefono' => env('BOT_TELEFONO', '+591 2 XXXXXXX'),
            'email' => env('BOT_EMAIL', 'info@cenefco.gob.bo'),
            'web' => env('BOT_WEB', 'https://www.cenefco.gob.bo'),
        ],

        'acreditacion' => [
            'aval' => env('BOT_AVAL', 'Universidad Mayor de San Simón (UMSS)'),
            'resolucion' => env('BOT_RESOLUCION', 'Resolución Ministerial N° XXXX/XXXX'),
            'autoriza' => env('BOT_AUTORIZA', 'Ministerio de Educación del Estado Plurinacional de Bolivia'),
            'descripcion' => env('BOT_ACREDITACION_DESC', 'Todos nuestros programas cuentan con aval universitario y resolución ministerial que garantizan su validez académica y legal.'),
        ],

        'pagos' => [
            'metodos'       => env('BOT_METODOS_PAGO', 'Transferencia bancaria, depósito, QR de pago'),
            'instrucciones' => env('BOT_PAGO_INSTRUCCIONES', 'Para realizar el pago, comunícate con nuestros asesores o visítanos en oficina. También puedes solicitar el QR escribiendo "soporte".'),
            'nota'          => env('BOT_PAGO_NOTA', 'El pago puede realizarse en cuotas. Consulta con un asesor para más detalles.'),
            'qr_image_url'  => env('BOT_QR_IMAGE_URL'),  
        ],
    ],

    'keywords' => [
        'saludo'     => ['hola', 'buenas', 'buenos', 'buen dia', 'buen día', 'hi', 'hello', 'saludos', 'inicio', 'menu', 'menú', 'tardes', 'noches', 'dias'],
        'programas'  => ['programa', 'programas', 'curso', 'cursos', 'diplomado', 'diplomados', 'especializacion', 'especialización', 'maestria', 'maestría', 'posgrado', 'oferta', 'oferta academica', 'que tienen', 'que ofrecen', 'que programas', 'educativos', 'cuentan'],
        'eventos'    => ['evento', 'eventos', 'actividad', 'actividades', 'agenda', 'conferencia', 'taller', 'seminario', 'congreso'],
        'docentes'   => ['docente', 'docentes', 'profesor', 'profesores', 'catedratico', 'facilitador', 'planta docente'],
        'faqs'       => ['pregunta', 'preguntas', 'frecuentes', 'duda', 'dudas', 'ayuda', 'faq'],
        'inscripcion'=> ['inscripcion', 'inscripción', 'inscribirme', 'inscribir', 'pre-inscripcion', 'preinscripcion', 'registro', 'postular', 'postulacion', 'requisito', 'requisitos', 'me interesa', 'interesado', 'interesada', 'quiero estudiar', 'quiero el'],
        'consulta'   => ['consultar', 'mi solicitud', 'estado inscripcion', 'verificar inscripcion', 'numero ci', 'buscar'],
        'horario'    => ['horario', 'horarios', 'hora', 'abierto', 'atienden', 'atencion', 'atención'],
        'ubicacion'  => ['ubicacion', 'ubicación', 'direccion', 'dirección', 'donde', 'lugar', 'mapa', 'llegar'],
        'pago'       => ['pago', 'pagar', 'precio', 'precios', 'costo', 'costos', 'cuanto cuesta', 'cuánto cuesta', 'cuanto vale', 'qr', 'transferencia', 'deposito', 'depositar', 'inversion', 'inversión', 'cuota', 'cuotas'],
        'acreditacion'=> ['aval', 'acreditacion', 'acreditación', 'resolucion', 'resolución', 'ministerial', 'autoriza', 'autorizan', 'quien los autoriza', 'validez', 'reconocido', 'oficial', 'de donde', 'informacion'],
        'soporte'    => ['asesor', 'asesora', 'hablar', 'persona', 'humano', 'operador', 'contactar', 'comunicar', 'llamada', 'llamar'],
    ],

    'ia' => [
        'host' => env('OLLAMA_HOST', 'http://localhost:11434'),
        'model' => env('OLLAMA_MODEL', 'qwen2.5:7b'),
        'max_tokens' => (int) env('AI_MAX_TOKENS', 512),
        'temperature' => (float) env('AI_TEMPERATURE', 0.3),
        'timeout' => (int) env('AI_TIMEOUT_SECONDS', 30),
        'max_input_chars' => (int) env('AI_MAX_INPUT_CHARS', 500),
        'rate_limit' => 20,
    ],

];
