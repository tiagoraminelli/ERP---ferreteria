<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">

            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">
                    {{ $cliente->nombre }} | #{{ str_pad($cliente->id, 3, '0', STR_PAD_LEFT) }} |
                    {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                </h2>

                <p class="text-sm text-black-500 dark:text-gray-400">
                    Perfil completo del cliente
                </p>
            </div>

            <a href="{{ route('clientes.index') }}"
                class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow whitespace-nowrap">
                ← Volver
            </a>

        </div>
    </x-slot>

    <style>
        .stat-card {
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            border-color: #d1d5db;
        }
    </style>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">

            {{-- PERFIL CLIENTE Y STATS --}}
            <div class="space-y-6">

                {{-- PERFIL CLIENTE --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-start">
                        <div class="space-y-4 w-full">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Información del Cliente
                            </h3>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-semibold text-gray-700">Documento</span>
                                    <p>{{ $cliente->documento ?? '-' }}</p>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">CUIT</span>
                                    <p>{{ $cliente->cuit ?? '-' }}</p>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">Condición IVA</span>
                                    <p>{{ $cliente->condicion_iva ?? '-' }}</p>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">Teléfono</span>
                                    <p>{{ $cliente->telefono ?? '-' }}</p>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">Email</span>
                                    <p>{{ $cliente->email ?? '-' }}</p>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">Ciudad</span>
                                    <p>{{ $cliente->ciudad ?? '-' }}</p>
                                </div>

                                <div class="col-span-2 md:col-span-3">
                                    <span class="font-semibold text-gray-700">Dirección</span>
                                    <p>{{ $cliente->direccion ?? '-' }}</p>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">Fecha Alta</span>
                                    <p>{{ $cliente->created_at->format('d/m/Y') }}</p>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">Límite de Crédito</span>
                                    <p class="font-bold text-gray-800">
                                        ${{ number_format($cliente->limite_credito ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STATS CARDS CON CÁLCULOS REALES DESDE DETALLES --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Total Compras --}}
                    <div class="stat-card bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <p class="text-xs text-gray-400 uppercase">Total Compras</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">{{ $totalCompras }}</p>
                        <p class="text-xs text-gray-400 mt-1">ventas realizadas</p>
                    </div>

                    {{-- Monto Comprado (desde detalles) --}}
                    <div class="stat-card bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <p class="text-xs text-gray-400 uppercase">Monto Comprado</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">${{ number_format($montoTotal, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-1">suma de productos</p>
                    </div>

                    {{-- Ticket Promedio --}}
                    <div class="stat-card bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <p class="text-xs text-gray-400 uppercase">Ticket Promedio</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">${{ number_format($ticketPromedio, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-1">promedio por compra</p>
                    </div>

                    {{-- Última Compra --}}
                    <div class="stat-card bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <p class="text-xs text-gray-400 uppercase">Última Compra</p>
                        <p class="text-sm font-bold text-gray-800 mt-1">
                            {{ $ultimaCompra ? $ultimaCompra->created_at->format('d/m/Y') : '-' }}
                        </p>
                        @if ($ultimaCompra)
                            @php
                                $totalUltimaCompra = 0;
                                foreach ($ultimaCompra->detalles as $detalle) {
                                    $totalUltimaCompra += $detalle->subtotal ?? $detalle->cantidad * $detalle->precio;
                                }
                            @endphp
                            <p class="text-xs text-gray-400 mt-1">${{ number_format($totalUltimaCompra, 2) }}</p>
                        @endif
                    </div>

                    {{-- Saldo Cuenta Corriente --}}
                    <div class="stat-card bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <p class="text-xs text-gray-400 uppercase">Saldo Cta. Cte.</p>
                        <p
                            class="text-xl font-bold mt-1 {{ $saldo > 0 ? 'text-red-600' : ($saldo < 0 ? 'text-green-600' : 'text-gray-800') }}">
                            ${{ number_format($saldo, 2) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            @if ($saldo > 0)
                                deuda pendiente
                            @elseif($saldo < 0)
                                saldo a favor
                            @else
                                sin movimientos
                            @endif
                        </p>
                    </div>

                    {{-- Saldo Pendiente en Ventas (desde detalles) --}}
                    <div class="stat-card bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <p class="text-xs text-gray-400 uppercase">Saldo en Ventas</p>
                        <p
                            class="text-xl font-bold mt-1 {{ $saldoVentas > 0 ? 'text-red-600' : ($saldoVentas < 0 ? 'text-green-600' : 'text-gray-800') }}">
                            ${{ number_format($saldoVentas, 2) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">pagos parciales pendientes</p>
                    </div>

                    {{-- Crédito Disponible --}}
                    <div class="stat-card bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <p class="text-xs text-gray-400 uppercase">Crédito Disponible</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">${{ number_format($creditoDisponible, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-1">de ${{ number_format($cliente->limite_credito ?? 0, 2) }}
                        </p>
                    </div>

                    {{-- Total Pagado --}}
                    <div class="stat-card bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <p class="text-xs text-gray-400 uppercase">Total Pagado</p>
                        <p class="text-xl font-bold text-green-600 mt-1">${{ number_format($totalPagado, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-1">historial de pagos</p>
                    </div>
                </div>
            </div>

            {{-- HISTORIAL COMPRAS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-700">Historial de Compras</h3>
                    <span class="text-xs text-gray-500">Excluye cuenta corriente</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Venta</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Método
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Total
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Pagado
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Saldo
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Estado
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($ventas as $venta)
                                @php
                                    $totalDetalles = 0;
                                    foreach ($venta->detalles as $detalle) {
                                        $totalDetalles += $detalle->subtotal ?? $detalle->cantidad * $detalle->precio;
                                    }

                                    $saldoVenta = $totalDetalles - $venta->monto_pagado;
                                    $porcentajePagado =
                                        $totalDetalles > 0 ? round(($venta->monto_pagado / $totalDetalles) * 100) : 0;

                                    // NUEVO
                                    if ($saldoVenta > 0) {
                                        $estadoPago = 'deuda';
                                    } elseif ($saldoVenta == 0) {
                                        $estadoPago = 'saldado';
                                    } else {
                                        $estadoPago = 'excedente';
                                    }
                                @endphp

                                <tr onclick="window.location='{{ route('ventas.show', $venta->id) }}'"
                                    class="hover:bg-gray-50 transition cursor-pointer">
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        #{{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $venta->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-gray-100 rounded-lg text-xs">
                                            {{ $venta->metodo_pago }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold">
                                        ${{ number_format($totalDetalles, 2) }}</td>

                                    <td class="px-6 py-4 text-right text-green-600 font-semibold">
                                        ${{ number_format($venta->monto_pagado, 2) }}</td>

                                    <td class="px-6 py-4 text-right font-semibold">

                                        <!-- DEUDA -->
                                        @if ($estadoPago === 'deuda')
                                            <span class="text-red-600">
                                                ${{ number_format($saldoVenta, 2) }}
                                            </span>
                                            <span class="text-xs text-gray-400 block">
                                                {{ $porcentajePagado }}% pagado
                                            </span>

                                            <!-- SALDADO -->
                                        @elseif($estadoPago === 'saldado')
                                            <span class="text-emerald-600 font-bold">
                                                ✔ Saldado
                                            </span>

                                            <!-- EXCEDENTE -->
                                        @else
                                            <span class="text-blue-600 font-bold">
                                                +${{ number_format(abs($saldoVenta), 2) }}
                                            </span>
                                            <span class="text-xs text-blue-400 block">
                                                Pago de más
                                            </span>
                                        @endif

                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full {{ $venta->estado === 'completada' ? 'bg-emerald-100 text-emerald-700' : ($venta->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-600') }}">
                                            {{ ucfirst($venta->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('ventas.edit', $venta->id) }}"
                                            onclick="event.stopPropagation();"
                                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition cursor-pointer text-center"
                                            title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                </path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-20 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p>Sin compras registradas</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t">
                    {{ $ventas->appends(request()->query())->links() }}
                </div>
            </div>

            {{-- CUENTA CORRIENTE --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-700">Movimientos Cuenta Corriente</h3>
                    <span class="text-xs text-gray-500">Saldo actual: ${{ number_format($saldo, 2) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Fecha
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Referencia</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Monto
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Descripción</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($movimientos as $mov)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded-lg text-xs {{ $mov->tipo === 'debito' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                            {{ ucfirst($mov->tipo) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($mov->referencia_id)
                                            <a href="{{ route('ventas.show', $mov->referencia_id) }}"
                                                class="text-blue-600 hover:text-blue-800 hover:underline">
                                                #{{ str_pad($mov->referencia_id, 8, '0', STR_PAD_LEFT) }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right font-semibold {{ $mov->monto > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ${{ number_format(abs($mov->monto), 2) }}
                                        <span class="text-xs text-gray-400 block">
                                            {{ $mov->monto > 0 ? 'debe' : 'haber' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate">
                                        {{ $mov->descripcion ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('ventas.show', $mov->referencia_id) }}"
                                            onclick="event.stopPropagation();"
                                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition cursor-pointer text-center"
                                            title="Ver venta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p>Sin movimientos en cuenta corriente</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t">
                    {{ $movimientos->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
