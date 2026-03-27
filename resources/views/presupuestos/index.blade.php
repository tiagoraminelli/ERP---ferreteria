<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">
                    Gestión de Presupuestos
                </h2>
                <p class="text-sm text-black-500 dark:text-gray-400">
                    Control y seguimiento de presupuestos realizados
                </p>
            </div>

            <a href="{{ route('presupuestos.create') }}"
                class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow whitespace-nowrap">
                + Nuevo Presupuesto
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
                        Los presupuestos tienen 5 estados:
                        <span class="font-semibold">Borrador</span> (en creación),
                        <span class="font-semibold">Enviado</span> (entregado al cliente),
                        <span class="font-semibold">Aprobado</span> (cliente aceptó),
                        <span class="font-semibold">Rechazado</span> (cliente no aceptó) y
                        <span class="font-semibold">Convertido</span> (pasó a venta).
                    </p>
                </div>

                <form method="GET" action="{{ route('presupuestos.index') }}"
                    class="flex flex-wrap items-center gap-4">

                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                        placeholder="Buscar presupuesto o cliente..."
                        class="flex-1 min-w-[260px] rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                    <select name="estado"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Estado</option>
                        <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>
                            Borrador
                        </option>
                        <option value="enviado" {{ request('estado') == 'enviado' ? 'selected' : '' }}>
                            Enviado
                        </option>
                        <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>
                            Aprobado
                        </option>
                        <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>
                            Rechazado
                        </option>
                        <option value="convertido" {{ request('estado') == 'convertido' ? 'selected' : '' }}>
                            Convertido
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

                    @if (request()->anyFilled(['buscar', 'estado', 'desde', 'hasta']))
                        <a href="{{ route('presupuestos.index') }}" class="text-sm text-red-500 hover:underline">
                            Limpiar
                        </a>
                    @endif

                </form>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800">{{ $totalPresupuestos }}</p>
                    <p class="text-xs text-gray-500">Total</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-600">{{ $presupuestosBorrador }}</p>
                    <p class="text-xs text-gray-500">Borrador</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $presupuestosEnviados }}</p>
                    <p class="text-xs text-gray-500">Enviados</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $presupuestosAprobados }}</p>
                    <p class="text-xs text-gray-500">Aprobados</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $presupuestosRechazados }}</p>
                    <p class="text-xs text-gray-500">Rechazados</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $presupuestosConvertidos }}</p>
                    <p class="text-xs text-gray-500">Convertidos</p>
                </div>
            </div>

            {{-- ================= TABLA ================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Presupuesto
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Cliente
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Total
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

                            @forelse($presupuestos as $presupuesto)
                                @php
                                    $rowClass = match ($presupuesto->estado) {
                                        'borrador' => 'bg-gray-50 hover:bg-gray-100',
                                        'enviado' => 'bg-blue-50 hover:bg-blue-100',
                                        'aprobado' => 'bg-green-50 hover:bg-green-100',
                                        'rechazado' => 'bg-red-50 hover:bg-red-100',
                                        'convertido' => 'bg-purple-50 hover:bg-purple-100',
                                        default => 'hover:bg-gray-50',
                                    };
                                @endphp

                                <tr class="{{ $rowClass }} transition cursor-pointer"
                                    onclick="window.location='{{ route('presupuestos.show', $presupuesto) }}'">

                                    {{-- Presupuesto --}}
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            Presupuesto #{{ $presupuesto->id }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Usuario: {{ $presupuesto->usuario->nombre ?? 'Sistema' }}
                                        </div>
                                    </td>

                                    {{-- Cliente --}}
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $presupuesto->cliente->nombre ?? 'Sin cliente' }}
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                        ${{ number_format($presupuesto->total, 2) }}
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-6 py-4 text-center">

                                        @php
                                            $badge = match ($presupuesto->estado) {
                                                'borrador' => 'bg-gray-100 text-gray-700',
                                                'enviado' => 'bg-blue-100 text-blue-700',
                                                'aprobado' => 'bg-green-100 text-green-700',
                                                'rechazado' => 'bg-red-100 text-red-600',
                                                'convertido' => 'bg-purple-100 text-purple-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp

                                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badge }}">
                                            {{ ucfirst($presupuesto->estado) }}
                                        </span>

                                    </td>

                                    {{-- Fecha --}}
                                    <td class="px-6 py-4 text-center text-gray-500">
                                        {{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d M Y') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-3" onclick="event.stopPropagation()">

                                            <a href="{{ route('presupuestos.show', $presupuesto) }}"
                                                class="px-3 py-1.5 text-xs font-medium rounded-lg
                                                bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                                Ver
                                            </a>

                                            @if ($presupuesto->estado === 'borrador')
                                                <a href="{{ route('presupuestos.edit', $presupuesto) }}"
                                                    class="px-3 py-1.5 text-xs font-medium rounded-lg
                                                    bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                                    Editar
                                                </a>
                                            @endif

                                            @if ($presupuesto->estado === 'aprobado')
                                                <form
                                                    action="{{ route('presupuestos.convertir-venta', $presupuesto) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-3 py-1.5 text-xs font-medium rounded-lg
                                                        bg-green-50 text-green-700 hover:bg-green-100 transition">
                                                        Convertir
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                                        No se encontraron presupuestos.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>

                </div>

                @if ($presupuestos->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $presupuestos->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
