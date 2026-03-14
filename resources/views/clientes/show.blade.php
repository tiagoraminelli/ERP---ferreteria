<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">

            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">
                    {{ $cliente->nombre }}
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
        .erp-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            align-items: start;
        }

        @media(max-width:1024px) {
            .erp-grid {
                grid-template-columns: 1fr;
            }
        }

        .erp-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .stat-card {
            background: white;
            padding: 18px;
            border-radius: 14px;
            border: 1px solid #eee;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
    </style>


    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">


            <div class="erp-grid">


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

                            </div>

                        </div>


                        <div class="text-right">

                            <p class="text-xs text-gray-400 uppercase mb-1">
                                Estado
                            </p>

                            @if ($cliente->activo)
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">
                                    Activo
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-600">
                                    Inactivo
                                </span>
                            @endif

                        </div>

                    </div>

                </div>



                {{-- STATS ERP --}}
                <div class="erp-stats">

                    <div class="stat-card">
                        <p class="text-xs text-gray-400 uppercase">Total Compras</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">
                            {{ $totalCompras }}
                        </p>
                    </div>

                    <div class="stat-card">
                        <p class="text-xs text-gray-400 uppercase">Monto Comprado</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">
                            ${{ number_format($montoTotal, 2) }}
                        </p>
                    </div>

                    <div class="stat-card">
                        <p class="text-xs text-gray-400 uppercase">Ticket Promedio</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">
                            ${{ number_format($ticketPromedio, 2) }}
                        </p>
                    </div>

                    <div class="stat-card">
                        <p class="text-xs text-gray-400 uppercase">Última Compra</p>
                        <p class="text-sm font-bold text-gray-800 mt-1">
                            {{ $ultimaCompra ? $ultimaCompra->created_at->format('d/m/Y') : '-' }}
                        </p>
                    </div>

                    <div class="stat-card">
                        <p class="text-xs text-gray-400 uppercase">Saldo Cuenta</p>

                        <p
                            class="text-xl font-bold
{{ $saldo > 0 ? 'text-red-600' : ($saldo < 0 ? 'text-green-600' : 'text-gray-800') }}">
                            ${{ number_format($saldo, 2) }}
                        </p>

                    </div>

                    <div class="stat-card">
                        <p class="text-xs text-gray-400 uppercase">Crédito Disponible</p>
                        <p class="text-xl font-bold text-gray-800">
                            ${{ number_format($creditoDisponible, 2) }}
                        </p>
                    </div>

                </div>


            </div>



            {{-- HISTORIAL COMPRAS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">
                        Historial de Compras
                    </h3>
                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 border-b border-gray-100">

                            <tr>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Venta
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Fecha
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Total
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Pagado
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Saldo
                                </th>

                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Estado
                                </th>

                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Acciones
                                </th>


                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @forelse($ventas as $venta)
                                @php
                                    $saldoVenta = $venta->total - $venta->monto_pagado;
                                @endphp

                                <tr onclick="window.location='{{ route('ventas.show', $venta->id) }}'"
                                    class="hover:bg-gray-50 transition cursor-pointer">

                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        #{{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}
                                    </td>

                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $venta->created_at->format('d/m/Y') }}
                                    </td>

                                    <td class="px-6 py-4 text-right font-semibold">
                                        ${{ number_format($venta->total, 2) }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-green-600 font-semibold">
                                        ${{ number_format($venta->monto_pagado, 2) }}
                                    </td>

                                    <td
                                        class="px-6 py-4 text-right font-semibold
{{ $saldoVenta > 0 ? 'text-red-600' : 'text-gray-700' }}">
                                        ${{ number_format($saldoVenta, 2) }}
                                    </td>

                                    <td class="px-6 py-4 text-center">

                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full
{{ $venta->estado === 'completada'
    ? 'bg-emerald-100 text-emerald-700'
    : ($venta->estado === 'pendiente'
        ? 'bg-yellow-100 text-yellow-700'
        : 'bg-red-100 text-red-600') }}">
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
                                    <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                                        Sin compras registradas
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

                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">
                        Movimientos Cuenta Corriente
                    </h3>
                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 border-b border-gray-100">

                            <tr>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Fecha
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Tipo
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Venta
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Monto
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Descripción
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse($movimientos as $mov)
                                <tr class="hover:bg-gray-50 transition">

                                    <td class="px-6 py-4">
                                        {{ $mov->created_at->format('d/m/Y') }}
                                    </td>

                                    <td class="px-6 py-4 capitalize">
                                        {{ $mov->tipo }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ $mov->venta_id ? '#' . str_pad($mov->venta_id, 8, '0', STR_PAD_LEFT) : '-' }}
                                    </td>

                                    <td
                                        class="px-6 py-4 text-right font-semibold
{{ $mov->monto > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ${{ number_format($mov->monto, 2) }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ $mov->descripcion ?? '-' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center text-gray-400">
                                        Sin movimientos
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
