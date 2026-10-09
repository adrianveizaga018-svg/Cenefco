@extends('emails.layout', [
    'titulo'    => $n->aprobado ? 'Pago confirmado' : 'Revisa tu comprobante',
    'preheader' => $n->aprobado
        ? 'Confirmamos tu pago de Bs. '.number_format($n->monto, 2).'.'
        : 'No pudimos confirmar tu comprobante de Bs. '.number_format($n->monto, 2).'.',
])

@section('content')
    <p style="margin:0 0 6px; font-size:13px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:{{ $n->aprobado ? '#15803D' : '#B45309' }};">
        {{ $n->aprobado ? 'Pago confirmado' : 'Comprobante observado' }}
    </p>
    <h1 style="margin:0 0 14px; font-size:26px; line-height:1.25; color:#072D3A;">Hola, {{ $n->nombre }}</h1>

    @if ($n->aprobado)
        <p style="margin:0 0 16px;">Confirmamos tu pago para <strong>{{ $n->programa }}</strong>.</p>
    @else
        <p style="margin:0 0 16px;">No pudimos confirmar el comprobante que subiste para <strong>{{ $n->programa }}</strong>.</p>
    @endif

    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0"
           style="margin:0 0 20px; border:1px solid #DCE6EA; border-radius:12px; overflow:hidden;">
        <tr>
            <td style="padding:12px 16px; color:#64748B; font-size:14px;">Monto del comprobante</td>
            <td align="right" style="padding:12px 16px; font-weight:bold; color:#072D3A;">Bs. {{ number_format($n->monto, 2) }}</td>
        </tr>
        @if ($n->aprobado)
            <tr>
                <td style="padding:12px 16px; color:#64748B; font-size:14px; border-top:1px solid #DCE6EA;">Saldo pendiente del programa</td>
                <td align="right" style="padding:12px 16px; font-weight:bold; border-top:1px solid #DCE6EA; color:{{ $n->saldoPendiente > 0 ? '#B45309' : '#15803D' }};">
                    {{ $n->saldoPendiente > 0 ? 'Bs. '.number_format($n->saldoPendiente, 2) : 'Nada, estás al día' }}
                </td>
            </tr>
        @endif
    </table>

    @if (! $n->aprobado && $n->motivo)
        <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
            <tr>
                <td style="background:#FFF8EB; border-left:4px solid #F7931E; border-radius:8px; padding:12px 16px; font-size:14px; color:#7C4A03;">
                    <strong>Motivo:</strong> {{ $n->motivo }}<br>
                    Puedes subir un nuevo comprobante desde tu panel, en Mis Pagos.
                </td>
            </tr>
        </table>
    @endif

    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:4px 0 8px;">
                <a href="{{ $loginUrl }}" style="display:inline-block; background:#0B4258; color:#ffffff; text-decoration:none; font-weight:bold; font-size:16px; padding:15px 38px; border-radius:10px;">
                    Ver mis pagos
                </a>
            </td>
        </tr>
    </table>
@endsection
