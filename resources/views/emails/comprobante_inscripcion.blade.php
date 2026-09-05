@extends('emails.layout')

@section('content')
    <h2>Comprobante de inscripción</h2>
    <p>Estimado/a <strong>{{ $venta->estudiante_nombre ?? 'Estudiante' }}</strong>,</p>
    <p>
        Adjuntamos su comprobante de inscripción al programa
        <strong>{{ $venta->nombre_programa ?? '—' }}</strong> (N° {{ $venta->id_ins }}).
        El documento incluye el detalle de sus pagos y el código de conducta del programa.
    </p>

    <table>
        <tr>
            <th>N° de Inscripción</th>
            <td>#{{ $venta->id_ins }}</td>
        </tr>
        <tr>
            <th>Programa</th>
            <td>{{ $venta->nombre_programa ?? '—' }}</td>
        </tr>
        <tr>
            <th>Período</th>
            <td>{{ $venta->periodo ?? '—' }} @if($venta->gestion) — Gestión {{ $venta->gestion }} @endif</td>
        </tr>
    </table>

    <p>Ante cualquier consulta, no dude en comunicarse con secretaría académica.</p>
    <p>Saludos cordiales,<br>CENEFCO — Centro Nacional de Educación y Formación Continua</p>
@endsection
