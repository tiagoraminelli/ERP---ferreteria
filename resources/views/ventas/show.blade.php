<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white dark:text-gray-900">
                    Venta #{{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="text-sm text-black-500 dark:text-gray-400">
                    {{ $venta->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <div class="flex gap-2">
                <button onclick="window.print()"
                    class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow whitespace-nowrap">
                    Imprimir
                </button>

                <a href="{{ route('ventas.index') }}"
                    class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow whitespace-nowrap">
                    ← Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" style="background-color: #f3f4f6; min-height: 100vh;">
        <div class="max-w-4xl mx-auto px-6">

            {{-- TICKET / BOLETA --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden" style="max-width: 400px; margin: 0 auto; font-family: 'Courier New', monospace;">

                {{-- HEADER --}}
                <div class="p-6 border-b border-gray-100 text-center">
                    {{-- Logo y nombre --}}
                    <div class="flex justify-center mb-2">
                        <div style="width: 70px; height: 70px; background-color: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 10px; text-align: center; border: 1px dashed #d1d5db; margin: 0 auto;">
                            <img src="{{asset('img/logo.png')}}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 uppercase">FERRETERIA CARRIZO</h2>
                    <p class="text-xs text-gray-500 mt-1">HOGAR Y LUZ</p>
                    <p class="text-xs text-gray-500">SAAVEDRA 1271</p>
                    <p class="text-xs text-gray-500">Tel: 54 9 3408 68-4371</p>
                    <p class="text-xs text-gray-500 mt-1">CUIT: 30-12345678-9</p>

                    <div class="border-t border-dashed border-gray-200 my-4"></div>

                    {{-- Aclaración legal --}}
                    <div class="text-[0.55rem] text-red-600 font-bold italic border border-red-200 bg-red-50 p-1.5 rounded-lg mb-3">
                        DOCUMENTO NO VÁLIDO COMO FACTURA
                    </div>

                    <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">BOLETA</p>
                    <p class="text-xs text-gray-600 mt-1">N° {{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-xs text-gray-600">FECHA: {{ $venta->created_at->format('d/m/Y H:i') }}</p>

                    <div class="border-t border-dashed border-gray-200 my-4"></div>

                    <div class="text-left text-xs text-gray-600">
                        <p><span class="font-bold text-gray-700">CLIENTE:</span> {{ $venta->cliente->nombre }}</p>
                        @if($venta->cliente->documento)
                            <p class="mt-1"><span class="font-bold text-gray-700">DOC:</span> {{ $venta->cliente->documento }}</p>
                        @endif
                        @if($venta->cliente->cuit)
                            <p class="mt-1"><span class="font-bold text-gray-700">CUIT:</span> {{ $venta->cliente->cuit }}</p>
                        @endif
                        @if($venta->cliente->direccion)
                            <p class="mt-1"><span class="font-bold text-gray-700">DIR:</span> {{ $venta->cliente->direccion }}</p>
                        @endif
                        @if($venta->cliente->condicion_iva)
                            <p class="mt-1"><span class="font-bold text-gray-700">IVA:</span> {{ $venta->cliente->condicion_iva }}</p>
                        @endif
                    </div>

                    <div class="border-t border-dashed border-gray-200 my-4"></div>

                    <p class="text-xs text-left text-gray-600">
                        <span class="font-bold text-gray-700">VENDEDOR:</span> {{ $venta->usuario->nombre ?? $venta->usuario->username }}
                    </p>
                </div>

                {{-- DETALLE --}}
                <div class="p-6 border-b border-gray-100">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 text-gray-600 font-bold">CANT</th>
                                <th class="text-left py-2 text-gray-600 font-bold">DESCRIPCIÓN</th>
                                <th class="text-right py-2 text-gray-600 font-bold">PRECIO</th>
                                <th class="text-right py-2 text-gray-600 font-bold">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $detalle)
                                <tr class="border-b border-dashed border-gray-100">
                                    <td class="py-2 text-gray-600">{{ number_format($detalle->cantidad, 3) }}</td>
                                    <td class="py-2 text-gray-600">{{ $detalle->producto->nombre }}</td>
                                    <td class="text-right py-2 text-gray-600">$ {{ number_format($detalle->precio, 2) }}</td>
                                    <td class="text-right py-2 text-gray-800 font-medium">$ {{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>

                                @if($detalle->descuento_porcentaje > 0 || $detalle->descuento_monto > 0)
                                    <tr class="text-gray-400 text-[0.6rem]">
                                        <td></td>
                                        <td colspan="3" class="text-right py-1">
                                            Descuento:
                                            @if($detalle->descuento_porcentaje > 0)
                                                {{ $detalle->descuento_porcentaje }}%
                                            @endif
                                            @if($detalle->descuento_monto > 0)
                                                @if($detalle->descuento_porcentaje > 0) + @endif
                                                $ {{ number_format($detalle->descuento_monto, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>

                    <div class="border-t border-dashed border-gray-200 my-4"></div>

                    {{-- SUBTOTAL --}}
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">SUBTOTAL:</span>
                        <span class="text-gray-800 font-medium">$ {{ number_format($venta->total, 2) }}</span>
                    </div>

                    <div class="border-t border-gray-200 my-3"></div>

                    {{-- TOTAL --}}
                    <div class="flex justify-between text-base font-bold">
                        <span class="text-gray-800">TOTAL:</span>
                        <span class="text-green-600">$ {{ number_format($venta->total, 2) }}</span>
                    </div>

                    {{-- PAGOS --}}
                    <div class="mt-4 space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">FORMA DE PAGO:</span>
                            <span class="text-gray-800 font-medium uppercase">{{ $venta->metodo_pago }}</span>
                        </div>

                        @if($venta->monto_pagado > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">MONTO PAGADO:</span>
                                <span class="text-green-600">$ {{ number_format($venta->monto_pagado, 2) }}</span>
                            </div>
                        @endif

                        @if($venta->saldo_pendiente > 0)
                            <div class="flex justify-between mt-2">
                                <span class="text-gray-600 font-bold">SALDO PENDIENTE:</span>
                                <span class="text-red-600 font-bold">$ {{ number_format($venta->saldo_pendiente, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    @if($venta->observaciones)
                        <div class="mt-4 pt-3 border-t border-dashed border-gray-200 text-xs">
                            <span class="font-bold text-gray-700">OBSERVACIONES:</span><br>
                            <span class="text-gray-600">{{ $venta->observaciones }}</span>
                        </div>
                    @endif
                </div>

                {{-- FOOTER --}}
                <div class="p-4 text-center text-xs text-gray-500">
                    <div class="border-t border-dashed border-gray-200 mb-3"></div>

                    {{-- Leyendas fiscales --}}
                    <p class="mb-1 text-[0.55rem]">* Válido para monotributistas y consumidores finales</p>
                    <p class="mb-1 text-[0.55rem]">* No discrimina IVA - Ley 24.977</p>
                    <p class="mb-2 text-[0.55rem]">* CAE: 12345678901234</p>

                    <p class="text-xs font-medium mt-2">¡Gracias por su compra!</p>

                    <div class="flex justify-center gap-3 text-[0.5rem] mt-2">
                        <span>Original: Cliente</span>
                        <span>Duplicado: Emisor</span>
                    </div>

                    @if($venta->estado === 'anulada')
                        <p class="text-red-600 font-bold mt-2 text-sm">*** COMPROBANTE ANULADO ***</p>
                    @endif
                </div>

            </div>



        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .bg-white, .bg-white * {
                visibility: visible;
            }
            .bg-white {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 400px !important;
                margin: 0 auto !important;
                box-shadow: none !important;
            }
            header, .no-print {
                display: none !important;
            }
            .text-green-600 {
                color: #000 !important;
                font-weight: bold !important;
            }
        }
    </style>

</x-app-layout>
