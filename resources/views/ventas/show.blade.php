<x-app-layout>

    {{-- BOTONES FUERA DEL TICKET --}}
    <div class="flex justify-end gap-2 mb-4 no-print">
        <button onclick="window.print()"
            class="px-5 py-2 bg-black text-white text-xs font-bold uppercase rounded-xl hover:bg-gray-800">
            Imprimir
        </button>

        <a href="{{ route('ventas.index') }}"
            class="px-5 py-2 bg-black text-white text-xs font-bold uppercase rounded-xl hover:bg-gray-800">
            ← Volver
        </a>
    </div>

    {{-- CONTENEDOR DEL TICKET --}}
    <div class="ticket py-8 bg-gray-100 min-h-screen">

        <div class="max-w-4xl mx-auto px-6">

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden"
                style="max-width:400px;margin:0 auto;font-family:'Courier New',monospace">

                {{-- CALCULO REAL DESDE DETALLES --}}
                @php
                    $subtotal = 0;
                    foreach ($venta->detalles as $detalle) {
                        $subtotal += $detalle->cantidad * $detalle->precio;
                    }
                    $total = $subtotal;
                    $pagado = $venta->monto_pagado ?? 0;
                    $cambio = max(0, $pagado - $total);
                    $saldo = max(0, $total - $pagado);
                @endphp

                @php
                    $mensajeContable = null;
                    $colorMensaje = 'text-gray-700';
                    $fondoMensaje = 'bg-gray-100';
                    if ($pagado == 0) {
                        $mensajeContable = 'VENTA REGISTRADA SIN PAGO';
                        $colorMensaje = 'text-yellow-700';
                        $fondoMensaje = 'bg-yellow-100';
                    } elseif ($pagado == $total) {
                        $mensajeContable = 'PAGO COMPLETO';
                        $colorMensaje = 'text-green-700';
                        $fondoMensaje = 'bg-green-100';
                    } elseif ($pagado > $total) {
                        $mensajeContable = 'PAGO MAYOR AL TOTAL - CAMBIO ENTREGADO';
                        $colorMensaje = 'text-blue-700';
                        $fondoMensaje = 'bg-blue-100';
                    } elseif ($pagado < $total) {
                        $mensajeContable = 'PAGO INCOMPLETO - SALDO PENDIENTE';
                        $colorMensaje = 'text-red-700';
                        $fondoMensaje = 'bg-red-100';
                    }
                @endphp

                {{-- HEADER TICKET --}}
                <div class="p-6 text-center">
                    <h2 class="text-lg font-bold uppercase">FERRETERIA CARRIZO</h2>
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-16 h-16 mx-auto mb-2">
                    <p class="text-xs">HOGAR Y LUZ</p>
                    <p class="text-xs">SAAVEDRA 1271</p>
                    <p class="text-xs">Tel: 3408-684371</p>
                    <p class="text-xs">CUIT: 30-12345678-9</p>
                    <div class="border-t border-dashed my-4"></div>
                    <div class="text-xs text-red-600 font-bold border border-red-200 bg-red-50 p-1 rounded">
                        DOCUMENTO NO VÁLIDO COMO FACTURA
                    </div>
                    <p class="text-sm font-bold mt-2">BOLETA</p>
                    <p class="text-xs">N° {{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-xs">FECHA: {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                    <div class="border-t border-dashed my-4"></div>
                    <div class="text-left text-xs">
                        <p><strong>CLIENTE:</strong> {{ $venta->cliente->nombre ?? 'Consumidor Final' }}</p>
                        @if (optional($venta->cliente)->documento)
                            <p><strong>DOC:</strong> {{ $venta->cliente->documento }}</p>
                        @endif
                        @if (optional($venta->cliente)->direccion)
                            <p><strong>DIR:</strong> {{ $venta->cliente->direccion }}</p>
                        @endif
                        <p><strong>VENDEDOR:</strong> {{ optional($venta->usuario)->nombre ?? 'Sistema' }}</p>
                    </div>
                </div>

                {{-- DETALLE PRODUCTOS --}}
                <div class="px-6 pb-4">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">CANT</th>
                                <th class="text-left py-2">DESCRIPCIÓN</th>
                                <th class="text-right py-2">PRECIO</th>
                                <th class="text-right py-2">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($venta->detalles as $detalle)
                                @php $sub = $detalle->cantidad * $detalle->precio; @endphp
                                <tr class="border-b border-dashed">
                                    <td class="py-2">{{ number_format($detalle->cantidad, 2) }}</td>
                                    <td class="py-2">{{ $detalle->producto->nombre ?? 'Producto' }}</td>
                                    <td class="text-right py-2">$ {{ number_format($detalle->precio, 2) }}</td>
                                    <td class="text-right py-2">$ {{ number_format($sub, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="border-t border-dashed my-4"></div>

                    {{-- TOTALES --}}
                    <div class="text-sm space-y-1">
                        <div class="flex justify-between"><span>SUBTOTAL</span><span>$
                                {{ number_format($subtotal, 2) }}</span></div>
                        <div class="border-t my-2"></div>
                        <div class="flex justify-between font-bold"><span>TOTAL</span><span>$
                                {{ number_format($total, 2) }}</span></div>
                    </div>

                    <div class="border-t border-dashed my-4"></div>

                    {{-- PAGOS --}}
                    <div class="text-xs space-y-1">
                        <div class="flex justify-between"><span>FORMA DE PAGO</span><span
                                class="uppercase">{{ $venta->metodo_pago ?? 'EFECTIVO' }}</span></div>
                        @if ($pagado > 0)
                            <div class="flex justify-between"><span>PAGADO</span><span>$
                                    {{ number_format($pagado, 2) }}</span></div>
                        @endif
                        @if ($cambio > 0)
                            <div class="flex justify-between"><span>CAMBIO</span><span>$
                                    {{ number_format($cambio, 2) }}</span></div>
                        @endif
                        @if ($saldo > 0)
                            <div class="flex justify-between font-bold text-red-600"><span>SALDO</span><span>$
                                    {{ number_format($saldo, 2) }}</span></div>
                        @endif
                    </div>

                    @if ($mensajeContable)
                        <div
                            class="mt-4 p-2 text-center text-xs font-bold {{ $colorMensaje }} {{ $fondoMensaje }} rounded">
                            {{ $mensajeContable }}
                        </div>
                    @endif

                    @if ($venta->observaciones)
                        <div class="border-t border-dashed my-4 pt-2 text-xs">
                            <strong>OBSERVACIONES</strong>
                            <p>{{ $venta->observaciones }}</p>
                        </div>
                    @endif
                </div>

                {{-- FOOTER --}}
                <div class="p-4 text-center text-xs text-gray-600">
                    <div class="border-t border-dashed mb-3"></div>
                    <p>* Válido para monotributistas y consumidor final</p>
                    <p>* No discrimina IVA - Ley 24.977</p>
                    <p>* CAE: 12345678901234</p>
                    <p class="mt-2">¡Gracias por su compra!</p>
                    <p class="mt-1 text-[10px]">Original: Cliente &nbsp;&nbsp; Duplicado: Emisor</p>
                </div>

            </div>

        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .ticket,
            .ticket * {
                visibility: visible;
            }

            .ticket {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 400px;
                box-shadow: none;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }

            body {
                margin: 0;
            }

            .ticket {
                width: 80mm !important;
                max-width: 80mm !important;
            }

            /* OCULTAR BOTONES EN IMPRESIÓN */
            .no-print {
                display: none !important;
            }
        }
    </style>

</x-app-layout>
