<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">
                    Gestión de Ventas
                </h2>
                <p class="text-sm text-black-500 dark:text-gray-400">
                    Control y registro de ventas realizadas
                </p>
            </div>

            <a href="{{ route('ventas.create') }}"
                class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow whitespace-nowrap">
                + Nueva Venta
            </a>
        </div>
    </x-slot>

    <x-confirm-modal />

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">



            {{-- ================= FILTROS ================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">

                 {{-- AVISO DE DATOS POR DEFECTO --}}
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded-xl p-4 mb-6">
                        <p class="text-sm font-semibold mb-1">
                            Información
                        </p>
                        <p class="text-xs leading-relaxed">
                            Para entender el saldo restante, se han predefinido ciertos datos en el sistema:
                            <span class="font-semibold">Si el saldo es: $0, el cliente no debe dinero.</span>,
                            <span class="font-semibold">Si el saldo de positivo, tiene pendiente de pago dicho monto aun, Ejemplo: $6000 </span> y
                            <span class="font-semibold">Si el saldo es negativo, pago de más, por lo tanto hay que devolver la diferencia.</span>.

                        </p>
                    </div>


                <form method="GET" action="{{ route('ventas.index') }}" class="flex flex-wrap items-center gap-4">

                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                        placeholder="Buscar venta o cliente..."
                        class="flex-1 min-w-[260px] rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                    <select name="estado"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Estado</option>
                        <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>
                            Completada
                        </option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>
                            Pendiente
                        </option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>
                            Cancelada
                        </option>
                    </select>

                    <select name="metodo_pago"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Método de pago</option>
                        <option value="cuenta_corriente"
                            {{ request('metodo_pago') == 'cuenta_corriente' ? 'selected' : '' }}>
                            Cuenta Corriente
                        </option>
                        <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>
                            Efectivo
                        </option>
                        <option value="tarjeta_debito"
                            {{ request('metodo_pago') == 'tarjeta_debito' ? 'selected' : '' }}>
                            Débito
                        </option>
                        <option value="tarjeta_credito"
                            {{ request('metodo_pago') == 'tarjeta_credito' ? 'selected' : '' }}>
                            Crédito
                        </option>
                        <option value="transferencia"
                            {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>
                            Transferencia
                        </option>
                    </select>

                    <input type="date" name="desde" value="{{ request('desde') }}"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                    <input type="date" name="hasta" value="{{ request('hasta') }}"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                    <button type="submit"
                        class="px-4 py-2 text-sm bg-black text-black rounded-xl hover:bg-gray-800 transition">
                        Filtrar
                    </button>

                    @if (request()->anyFilled(['buscar', 'estado', 'metodo_pago', 'desde', 'hasta']))
                        <a href="{{ route('ventas.index') }}" class="text-sm text-red-500 hover:underline">
                            Limpiar
                        </a>
                    @endif

                </form>
            </div>

            {{-- ================= TABLA ================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Venta
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Cliente
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Total
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Saldo Restante
                                </th>

                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Método Pago
                                </th>

                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Estado
                                </th>

                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Fecha
                                </th>

                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse($ventas as $venta)
                                @php
                                    // Calcular saldo restante de ventas con total - monto_pagado
                                    $saldoRestante = $venta->total - $venta->monto_pagado;

                                    $rowClass = match ($venta->estado) {
                                        'completada' => 'bg-emerald-50 hover:bg-emerald-100',
                                        'pendiente' => 'bg-yellow-50 hover:bg-yellow-100',
                                        'cancelada' => 'bg-red-50 hover:bg-red-100',
                                        default => 'hover:bg-gray-50',
                                    };
                                @endphp

                                <tr class="{{ $rowClass }} transition cursor-pointer"
                                    onclick="window.location='{{ route('ventas.show', $venta) }}'">

                                    {{-- Venta --}}
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            Venta #{{ $venta->id }}
                                        </div>

                                        <div class="text-xs text-gray-400 mt-1">
                                            Usuario: {{ $venta->usuario->name ?? 'Sistema' }}
                                        </div>
                                    </td>

                                    {{-- Cliente --}}
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $venta->cliente->nombre ?? 'Consumidor Final' }}
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                        ${{ number_format($venta->total, 2) }}
                                    </td>

                                    {{-- Saldo Restante --}}
                                    <td class="px-6 py-4 text-right font-semibold {{ $saldoRestante > 0 ? 'text-red-600' : ($saldoRestante < 0 ? 'text-green-600' : 'text-gray-800') }}">
                                        ${{ number_format($saldoRestante, 2) }}
                                    </td>

                                    {{-- Metodo Pago --}}
                                    <td class="px-6 py-4 text-center text-gray-600">
                                        {{ str_replace('_', ' ', ucfirst($venta->metodo_pago)) }}
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-6 py-4 text-center">

                                        @php
                                            $badge = match ($venta->estado) {
                                                'completada' => 'bg-emerald-100 text-emerald-700',
                                                'pendiente' => 'bg-yellow-100 text-yellow-700',
                                                'cancelada' => 'bg-red-100 text-red-600',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp

                                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badge }}">
                                            {{ ucfirst($venta->estado) }}
                                        </span>

                                    </td>

                                    {{-- Fecha --}}
                                    <td class="px-6 py-4 text-center text-gray-500">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d M Y') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-3" onclick="event.stopPropagation()">

                                            <a href="{{ route('ventas.show', $venta) }}"
                                                class="px-3 py-1.5 text-xs font-medium rounded-lg
                                                bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                                Ver
                                            </a>

                                            <a href="{{ route('ventas.edit', $venta) }}"
                                                class="px-3 py-1.5 text-xs font-medium rounded-lg
                                                bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                                Editar
                                            </a>

                                        </div>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7" class="px-6 py-20 text-center text-gray-400">
                                        No se encontraron ventas.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>

                </div>

                @if ($ventas->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $ventas->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
