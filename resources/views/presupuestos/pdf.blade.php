<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            background: #f3f3f3;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ccc;
            padding: 20px;
        }

        .afip {
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 8px 0;
            font-size: 11px;
        }

        .afip strong {
            font-size: 16px;
            display: block;
        }

        .header, .section {
            padding: 10px 0;
            border-bottom: 1px solid #000;
            font-size: 11px;
        }

        .header .left {
            float: left;
            width: 60%;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header .left img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .header .left .empresa-info {
            font-size: 11px;
        }

        .header .right {
            float: right;
            width: 40%;
            text-align: right;
        }

        .header::after {
            content: "";
            display: table;
            clear: both;
        }

        .section {
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }

        table th, table td {
            border-bottom: 1px solid #000;
            padding: 5px 3px;
        }

        table th {
            text-transform: uppercase;
            font-size: 10px;
            color: #555;
        }

        .text-right {
            text-align: right;
        }

        .totales {
            float: right;
            width: 40%;
            margin-top: 10px;
            font-size: 11px;
        }

        .totales div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .total-final {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 13px;
        }

        .observaciones {
            padding: 10px 0;
            font-size: 11px;
        }

        .estado {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 10px;
            margin-top: 5px;
        }

        .estado.borrador { background: #f0f0f0; color: #555; }
        .estado.enviado { background: #dbeafe; color: #1e3a8a; }
        .estado.aprobado { background: #dcfce7; color: #166534; }
        .estado.rechazado { background: #fee2e2; color: #b91c1c; }
        .estado.convertido { background: #ede9fe; color: #5b21b6; }

        .terminos {
            font-size: 8px;
            background: #f3f3f3;
            padding: 10px;
            border-top: 1px solid #000;
            margin-top: 10px;
            line-height: 1.2;
        }

        @media print {
            body { background: #fff; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- AFIP -->
    <div class="afip">
        <strong>X</strong>
        DOCUMENTO NO VALIDO COMO FACTURA
    </div>

    <!-- HEADER -->
    <div class="header">
        <div class="left">
            <div class="empresa-info">
                <strong>FERRETERIA CARRIZO</strong><br>
                HOGAR Y LUZ<br>
                Saavedra 1271 - San Cristóbal<br>
                Tel: 3408-684371<br>
                CUIT: 30-12345678-9<br>
                IVA: Monotributo
            </div>
        </div>
        <div class="right">
            <strong>PRESUPUESTO</strong><br>
            N° {{ str_pad($presupuesto->id, 8, '0', STR_PAD_LEFT) }}<br>
            Fecha: {{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}<br>
            @if($presupuesto->fecha_validez)
                Validez: {{ \Carbon\Carbon::parse($presupuesto->fecha_validez)->format('d/m/Y') }}
            @endif
        </div>
    </div>

    <!-- CLIENTE -->
    <div class="section">
        <strong>Cliente:</strong> {{ $presupuesto->cliente->nombre ?? 'Consumidor Final' }}<br>
        @if(optional($presupuesto->cliente)->documento)
            Doc: {{ $presupuesto->cliente->documento }}<br>
        @endif
        @if(optional($presupuesto->cliente)->telefono)
            Tel: {{ $presupuesto->cliente->telefono }}
        @endif
    </div>

    <!-- TABLA -->
    <div class="section">
        <table>
            <thead>
                <tr>
                    <th align="left">Cant</th>
                    <th align="left">Producto</th>
                    <th align="right">Precio</th>
                    <th align="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach($presupuesto->detalles as $detalle)
                    @php
                        $sub = $detalle->cantidad * $detalle->precio;
                        $subtotal += $sub;
                    @endphp
                    <tr>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                        <td class="text-right">$ {{ number_format($detalle->precio, 2) }}</td>
                        <td class="text-right">$ {{ number_format($sub, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOTALES -->
        @php
            $total = $presupuesto->total ?? $subtotal;
            $descuento = $presupuesto->descuento_monto ?? 0;
        @endphp
        <div class="totales">
            <div>
                <span>Subtotal</span>
                <span>$ {{ number_format($subtotal, 2) }}</span>
            </div>
            @if($descuento > 0)
            <div style="color:red;">
                <span>Descuento</span>
                <span>- $ {{ number_format($descuento, 2) }}</span>
            </div>
            @endif
            <div class="total-final">
                <span>Total</span>
                <span>$ {{ number_format($total, 2) }}</span>
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>

    <!-- OBSERVACIONES -->
    @if($presupuesto->notas)
    <div class="observaciones">
        <strong>Observaciones:</strong><br>
        {{ $presupuesto->notas }}
    </div>
    @endif

    <!-- ESTADO -->
    @php
        $estadoColor = $presupuesto->estado ?? 'borrador';
    @endphp
    <div class="estado {{ $estadoColor }}">
        Estado: {{ strtoupper($presupuesto->estado) }}
    </div>

    <!-- TERMINOS -->
    <div class="terminos">
        <strong>Términos y condiciones</strong><br>
        • Documento no válido como factura.<br>
        • Comprobante tipo "X" según AFIP/ARCA.<br>
        • Precios sujetos a modificación sin previo aviso.<br>
        • Validez: {{ $presupuesto->validez_dias ?? 30 }} días.<br>
        • Conservar por 2 años.<br>
        • Requiere emisión de comprobante fiscal para concretar operación.
    </div>

</div>

</body>
</html>
