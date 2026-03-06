<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">Clientes</h2>
                <p class="text-sm text-black-500 dark:text-gray-400">Gestión de clientes del sistema</p>
            </div>

            <a href="{{ route('clientes.create') }}"
                class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow whitespace-nowrap">
                + Nuevo Cliente
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">

            {{-- FILTROS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <form method="GET" action="{{ route('clientes.index') }}" class="flex flex-wrap items-center gap-4">

                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                        placeholder="Buscar cliente, documento o teléfono..."
                        class="flex-1 min-w-[260px] rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                    <select name="estado"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Estado</option>
                        <option value="activos" {{ request('estado') == 'activos' ? 'selected' : '' }}>
                            Activos
                        </option>
                        <option value="inactivos" {{ request('estado') == 'inactivos' ? 'selected' : '' }}>
                            Inactivos
                        </option>
                    </select>

                    <select name="deuda"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Cuenta corriente</option>
                        <option value="con_deuda" {{ request('deuda') == 'con_deuda' ? 'selected' : '' }}>
                            Con deuda
                        </option>
                        <option value="sin_deuda" {{ request('deuda') == 'sin_deuda' ? 'selected' : '' }}>
                            Sin deuda
                        </option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 text-sm bg-black text-black rounded-xl hover:bg-gray-800 transition whitespace-nowrap">
                        Filtrar
                    </button>

                    @if (request()->anyFilled(['buscar', 'estado', 'deuda']))
                        <a href="{{ route('clientes.index') }}"
                            class="text-sm text-red-500 hover:underline whitespace-nowrap">
                            Limpiar
                        </a>
                    @endif

                </form>
            </div>

            {{-- TABLA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Teléfono</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Contacto</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Deuda</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse($clientes as $cliente)

                                @php
                                    $rowColor = match (true) {
                                        !$cliente->activo => 'bg-gray-50',
                                        $cliente->saldo_cuenta_corriente > 0 => 'bg-red-50 hover:bg-red-100',
                                        default => 'hover:bg-gray-50',
                                    };
                                @endphp

                                <tr class="{{ $rowColor }} transition cursor-pointer hover:shadow-sm"
                                    onclick="window.location='{{ route('clientes.show', $cliente) }}'">

                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $cliente->nombre }}</div>

                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $cliente->ciudad ?? 'Sin ciudad' }} |
                                            {{ $cliente->provincia ?? 'Sin provincia' }} |
                                            ID {{ $cliente->id }} |
                                            CP {{ $cliente->codigo_postal ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $cliente->telefono ?? 'Sin teléfono' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-gray-700">
                                            {{ $cliente->direccion ?? 'Sin dirección' }}
                                        </div>

                                        <div class="text-xs text-gray-400">
                                            {{ $cliente->email ?? '' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        @if ($cliente->saldo_cuenta_corriente > 0)
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                ${{ number_format($cliente->saldo_cuenta_corriente, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                                Sin deuda
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        @if ($cliente->activo)
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                                Activo
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>

                                    {{-- ACCIONES --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2 text-black"
                                            onclick="event.stopPropagation()">

                                            {{-- EDITAR --}}
                                            <a href="{{ route('clientes.edit', $cliente) }}"
                                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"
                                                title="Editar">

                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>

                                            </a>

                                            {{-- DESACTIVAR --}}
                                            @if ($cliente->activo)

                                                <form action="{{ route('clientes.desactivar', $cliente) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="button"
                                                        onclick="openConfirmModal({
                                                            form: this.closest('form'),
                                                            title: 'Desactivar cliente',
                                                            message: '¿Seguro que deseas desactivar este cliente?',
                                                            buttonText: 'Desactivar',
                                                            buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                                                            icon: `<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                                                                    d='M18.364 5.636l-12.728 12.728M6.343 6.343l11.314 11.314'/>
                                                                </svg>`
                                                        })"
                                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                        title="Desactivar">

                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M18.364 5.636l-12.728 12.728M6.343 6.343l11.314 11.314"/>
                                                        </svg>

                                                    </button>
                                                </form>

                                            @else

                                                {{-- ACTIVAR --}}
                                                <form action="{{ route('clientes.activar', $cliente) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="button"
                                                        onclick="openConfirmModal({
                                                            form: this.closest('form'),
                                                            title: 'Activar cliente',
                                                            message: '¿Deseas volver a activar este cliente?',
                                                            buttonText: 'Activar',
                                                            buttonClass: 'bg-green-600 hover:bg-green-700 text-black',
                                                            icon: `<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                                                                    d='M5 13l4 4L19 7'/>
                                                                </svg>`
                                                        })"
                                                        class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                                        title="Activar">

                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M5 13l4 4L19 7"/>
                                                        </svg>

                                                    </button>
                                                </form>

                                            @endif

                                        </div>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                                        No se encontraron clientes.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

                @if ($clientes->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $clientes->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>

    {{-- MODAL DE CONFIRMACIÓN --}}
    @include('components.confirm-modal')

</x-app-layout>
