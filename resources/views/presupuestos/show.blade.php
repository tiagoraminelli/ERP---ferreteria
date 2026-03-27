<x-app-layout>

    <x-slot name="header">
    <div class="flex justify-between items-center">
        {{-- Logo + info empresa --}}
        <div class="flex items-center gap-3">
            <img src="{{ asset('img/logo.png') }}" class="w-10 h-10 object-contain" alt="Logo">
            <div class="text-sm leading-tight">
                <p class="font-bold text-white dark:text-gray-900">FERRETERÍA CARRIZO</p>
                <p class="text-gray-200 dark:text-gray-400 text-xs">HOGAR Y LUZ</p>
                <p class="text-gray-200 dark:text-gray-400 text-[10px]">Saavedra 1271 - San Cristóbal</p>
            </div>
        </div>

        {{-- Título + acciones --}}
        <div class="flex flex-col items-end">
            <h2 class="text-2xl font-bold text-white dark:text-gray-900">
                Presupuesto #{{ str_pad($presupuesto->id, 8, '0', STR_PAD_LEFT) }}
            </h2>
            <p class="text-xs text-gray-200 dark:text-gray-400">
                Fecha: {{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}
                @if($presupuesto->fecha_validez)
                    | Validez: {{ \Carbon\Carbon::parse($presupuesto->fecha_validez)->format('d/m/Y') }}
                @endif
            </p>

            <div class="mt-2 flex gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-gray-500 text-white text-xs font-bold uppercase rounded">
                    Imprimir Pantalla
                </button>
                <a href="{{ route('presupuestos.pdf', $presupuesto) }}" target="_blank"
                   class="px-4 py-2 bg-black text-white text-xs font-bold uppercase rounded hover:bg-gray-800">
                   Descargar PDF Minimalista
                </a>
                <a href="{{ route('presupuestos.index') }}"
                   class="px-4 py-2 bg-gray-700 text-white text-xs font-bold uppercase rounded hover:bg-gray-600">
                   ← Volver
                </a>
            </div>
        </div>
    </div>
</x-slot>

    <div class="presupuesto py-6 bg-gray-100 min-h-screen">
        <div class="max-w-3xl mx-auto bg-white border">

            @php
                $subtotal = 0;
                foreach ($presupuesto->detalles as $detalle) {
                    $subtotal += $detalle->cantidad * $detalle->precio;
                }
                $total = $presupuesto->total ?? $subtotal;
                $descuento = $presupuesto->descuento_monto ?? 0;
            @endphp

            {{-- 🔥 CABECERA AFIP LIMPIA --}}
            <div class="text-center border-b py-3">
                <p class="text-lg font-bold tracking-widest">X</p>
                <p class="text-xs font-bold tracking-wide">
                    DOCUMENTO NO VALIDO COMO FACTURA
                </p>
            </div>

            {{-- HEADER --}}
            <div class="p-6 border-b">
                <div class="flex justify-between items-start">

                    {{-- EMPRESA --}}
                    <div class="flex gap-3">
                        <img src="{{ asset('img/logo.png') }}" class="w-16 h-16 object-contain" alt="Logo">

                        <div class="text-[11px] text-gray-700 leading-tight">
                            <p class="font-bold text-sm">FERRETERIA CARRIZO</p>
                            <p class="text-gray-500">HOGAR Y LUZ</p>

                            <div class="mt-1 text-[10px] space-y-[2px]">
                                <p>Saavedra 1271 - San Cristóbal</p>
                                <p>Tel: 3408-684371</p>
                                <p>CUIT: 30-12345678-9</p>
                                <p>IVA: Monotributo</p>
                            </div>
                        </div>
                    </div>

                    {{-- DOCUMENTO --}}
                    <div class="text-right">
                        <p class="font-bold text-lg">PRESUPUESTO</p>
                        <p class="text-sm text-gray-600">
                            N° {{ str_pad($presupuesto->id, 8, '0', STR_PAD_LEFT) }}
                        </p>

                        <div class="text-[10px] text-gray-500 mt-2">
                            <p>Fecha: {{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}</p>

                            @if ($presupuesto->fecha_validez)
                                <p>Validez: {{ \Carbon\Carbon::parse($presupuesto->fecha_validez)->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            {{-- CLIENTE --}}
            <div class="p-6 border-b grid grid-cols-2 gap-6 text-[11px]">

                <div>
                    <p class="font-semibold text-gray-500 uppercase mb-1">Cliente</p>

                    <p class="font-medium text-gray-800">
                        {{ $presupuesto->cliente->nombre ?? 'Consumidor Final' }}
                    </p>

                    @if (optional($presupuesto->cliente)->documento)
                        <p>Doc: {{ $presupuesto->cliente->documento }}</p>
                    @endif

                    @if (optional($presupuesto->cliente)->telefono)
                        <p>Tel: {{ $presupuesto->cliente->telefono }}</p>
                    @endif
                </div>

                <div>
                    <p class="font-semibold text-gray-500 uppercase mb-1">Condiciones</p>

                    <p>Validez: {{ $presupuesto->validez_dias ?? 30 }} días</p>
                    <p>Pago: Efectivo / Transferencia / Tarjeta</p>
                    <p>Moneda: ARS</p>
                </div>

            </div>

            {{-- TABLA --}}
            <div class="p-6">

                <table class="w-full text-[11px]">
                    <thead>
                        <tr class="border-b text-gray-500 uppercase text-[10px]">
                            <th class="py-2 text-left">Cant</th>
                            <th class="py-2 text-left">Producto</th>
                            <th class="py-2 text-right">Precio</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($presupuesto->detalles as $detalle)
                            @php
                                $subtotalDetalle = $detalle->cantidad * $detalle->precio;
                            @endphp

                            <tr class="border-b">
                                <td class="py-2">{{ $detalle->cantidad }}</td>

                                <td class="py-2">
                                    {{ $detalle->producto->nombre ?? 'Producto eliminado' }}
                                </td>

                                <td class="py-2 text-right">
                                    $ {{ number_format($detalle->precio, 2) }}
                                </td>

                                <td class="py-2 text-right font-medium">
                                    $ {{ number_format($subtotalDetalle, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- TOTALES --}}
                <div class="mt-5 flex justify-end mt-2">
                    <div class="w-64 text-[11px] space-y-1">

                        <div class="flex justify-between">
                            <span>Subtotal - </span>
                            <span>$ {{ number_format($subtotal, 2) }}</span>
                        </div>

                        @if ($descuento > 0)
                            <div class="flex justify-between text-red-600">
                                <span>Descuento -</span>
                                <span>- $ {{ number_format($descuento, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between border-t pt-2 font-bold text-[13px]">
                            <span>Total - </span>
                            <span>$ {{ number_format($total, 2) }}</span>
                        </div>

                    </div>
                </div>

            </div>

            {{-- OBSERVACIONES --}}
            @if ($presupuesto->notas)
                <div class="px-6 pb-4 text-[11px] mb-4">
                    <p class="font-semibold text-gray-500 uppercase mb-1">Observaciones</p>
                    <p>{{ $presupuesto->notas }}</p>
                </div>
            @endif

            {{-- TERMINOS --}}
            <div class="px-6 py-4 text-[8px] text-gray-600 border-t bg-gray-100 leading-snug">

                <p class="font-semibold uppercase tracking-wide mb-1">Términos y condiciones</p>

                <p>• El presente documento se emite en carácter de presupuesto y no posee validez fiscal conforme
                    normativa vigente.</p>

                <p>• Identificado como comprobante tipo "X" según disposiciones de AFIP/ARCA, destinado exclusivamente a
                    uso informativo.</p>

                <p>• Los precios indicados podrán ser modificados sin previo aviso y están sujetos a disponibilidad de
                    stock al momento de la confirmación.</p>

                <p>• La validez del presente presupuesto es de {{ $presupuesto->validez_dias ?? 30 }} días desde su
                    fecha de emisión ({{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}).</p>

                <p>• La aceptación del mismo implica conformidad con las condiciones comerciales, formas de pago y
                    plazos de entrega establecidos.</p>

                <p>• Este documento deberá ser conservado por el emisor durante un plazo mínimo de 2 (dos) años,
                    conforme normativa aplicable.</p>

                <p>• Para concretar la operación deberá emitirse el comprobante fiscal correspondiente.</p>

            </div>

        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .presupuesto,
            .presupuesto * {
                visibility: visible;
            }

            .presupuesto {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                background: white;
            }


        }
    </style>

</x-app-layout>
