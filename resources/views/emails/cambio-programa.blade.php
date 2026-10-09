@extends('emails.layout', [
    'titulo'    => 'Tu cambio de programa',
    'preheader' => 'Ya estás inscrito/a en '.$n->programaNuevo.'.',
])

@section('content')
    <p style="margin:0 0 6px; font-size:13px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#1A6080;">
        Cambio de programa
    </p>
    <h1 style="margin:0 0 14px; font-size:26px; line-height:1.25; color:#072D3A;">Hola, {{ $n->nombre }}</h1>

    <p style="margin:0 0 16px;">Tu inscripción se cambió de programa:</p>

    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
        <tr>
            <td style="background:#F6F8F9; border-radius:8px; padding:12px 16px; color:#64748B; font-size:14px;">
                Antes: <span style="text-decoration:line-through;">{{ $n->programaAnterior }}</span>
            </td>
        </tr>
        <tr><td style="height:8px; line-height:8px;"></td></tr>
        <tr>
            <td style="background:#EAF4F8; border-left:4px solid #0B4258; border-radius:8px; padding:12px 16px; font-weight:bold; color:#0B4258;">
                🎓 Ahora: {{ $n->programaNuevo }}
            </td>
        </tr>
    </table>

    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0"
           style="margin:0 0 20px; border:1px solid #DCE6EA; border-radius:12px; overflow:hidden;">
        <tr>
            <td style="padding:12px 18px; font-size:14px; color:#334155;">Lo que ya pagaste (se te reconoce)</td>
            <td align="right" style="padding:12px 18px; font-size:14px; font-weight:bold; color:#009A4E;">Bs. {{ number_format($n->reconocido, 2) }}</td>
        </tr>
        @if ($n->saldoAFavor > 0)
            <tr>
                <td style="padding:12px 18px; font-size:14px; color:#334155; border-top:1px solid #E8EEF1;">Saldo a tu favor</td>
                <td align="right" style="padding:12px 18px; font-size:14px; font-weight:bold; color:#0B4258; border-top:1px solid #E8EEF1;">Bs. {{ number_format($n->saldoAFavor, 2) }}</td>
            </tr>
        @else
            <tr>
                <td style="padding:12px 18px; font-size:14px; color:#334155; border-top:1px solid #E8EEF1;">Te falta pagar</td>
                <td align="right" style="padding:12px 18px; font-size:14px; font-weight:bold; color:#072D3A; border-top:1px solid #E8EEF1;">Bs. {{ number_format($n->saldoPendiente, 2) }}</td>
            </tr>
        @endif
    </table>

    @php($pendientes = array_values(array_filter($n->cuotas, fn ($c) => $c['monto_pendiente'] > 0)))
    @if (count($pendientes))
        <p style="margin:0 0 8px; font-size:14px; font-weight:bold; color:#072D3A;">Tus próximas cuotas</p>
        <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
            @foreach ($pendientes as $c)
                <tr>
                    <td style="padding:8px 0; border-bottom:1px solid #E8EEF1; font-size:14px; color:#334155;">
                        {{ $c['descripcion'] }}
                        @if ($c['fecha_vencimiento'])
                            <span style="color:#64748B; font-size:12px;"> · vence {{ \Illuminate\Support\Carbon::parse($c['fecha_vencimiento'])->format('d/m/Y') }}</span>
                        @endif
                    </td>
                    <td align="right" style="padding:8px 0; border-bottom:1px solid #E8EEF1; font-size:14px; font-weight:bold; color:#072D3A;">
                        Bs. {{ number_format($c['monto_pendiente'], 2) }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if ($n->saldoAFavor > 0)
        <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
            <tr>
                <td style="background:#FFF8EB; border-left:4px solid #F7931E; border-radius:8px; padding:12px 16px; font-size:13px; color:#7C4A03;">
                    Registramos la devolución de tu saldo a favor. Te contactaremos cuando esté aprobada.
                </td>
            </tr>
        </table>
    @endif

    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:4px 0 8px;">
                <a href="{{ $loginUrl }}" style="display:inline-block; background:#0B4258; color:#ffffff; text-decoration:none; font-weight:bold; font-size:16px; padding:15px 38px; border-radius:10px;">
                    Ver mis cursos y pagos
                </a>
            </td>
        </tr>
    </table>
@endsection
