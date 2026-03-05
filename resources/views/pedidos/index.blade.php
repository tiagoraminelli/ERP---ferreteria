<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">
                    Gestión de Pedidos
                </h2>
                <p class="text-sm text-black-500 dark:text-gray-400">
                    Control y seguimiento de compras
                </p>
            </div>

            <a href="{{ route('pedidos.create') }}"
                class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow whitespace-nowrap">
                + Nuevo Pedido
            </a>
        </div>
    </x-slot>

    <x-confirm-modal />

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">

            {{-- ================= FILTROS ================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <form method="GET" action="{{ route('pedidos.index') }}" class="flex flex-wrap items-center gap-4">

                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                        placeholder="Buscar pedido, código o proveedor..."
                        class="flex-1 min-w-[260px] rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                    <label class="flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap">
                        <input type="checkbox" name="ocultos" value="1" {{ request('ocultos') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-black focus:ring-black">
                        Solo ocultos
                    </label>

                    <select name="categoria"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Categoría</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <select name="estado"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Estado</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente
                        </option>
                        <option value="comprado" {{ request('estado') == 'comprado' ? 'selected' : '' }}>Comprado
                        </option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado
                        </option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 text-sm bg-black text-black rounded-xl hover:bg-gray-800 transition">
                        Filtrar
                    </button>

                    @if (request()->anyFilled(['buscar', 'categoria', 'estado', 'ocultos']))
                        <a href="{{ route('pedidos.index') }}" class="text-sm text-red-500 hover:underline">
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
                                    Pedido
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Cantidad
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Observaciones
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Estado
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Fecha de Creación
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">

                            @forelse($pedidos as $pedido)

                                @php
                                    $rowClass = match ($pedido->estado) {
                                        'pendiente' => 'bg-yellow-50 hover:bg-yellow-100',
                                        'comprado' => 'bg-emerald-50 hover:bg-emerald-100',
                                        'cancelado' => 'bg-red-50 hover:bg-red-100',
                                        default => 'hover:bg-gray-50',
                                    };
                                @endphp

                                <tr class="{{ $rowClass }} transition cursor-pointer"
                                    onclick="window.location='{{ route('pedidos.edit', $pedido) }}'">

                                    {{-- Pedido --}}
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $pedido->nombre }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $pedido->codigo ?? 'Sin código' }} |
                                            {{ $pedido->categoria->nombre ?? 'Sin categoría' }} |
                                            {{ $pedido->proveedor ?? 'Sin proveedor' }}

                                        </div>
                                    </td>

                                    {{-- Cantidad --}}
                                    <td class="px-6 py-4 text-center font-medium text-gray-700">
                                        {{ $pedido->cantidad }}
                                    </td>

                                    {{-- Observaciones --}}
                                    <td class="px-6 py-4 text-right text-sm text-gray-500">
                                        {{ Str::limit($pedido->observaciones, 50, '...') }}
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $badge = match ($pedido->estado) {
                                                'pendiente' => 'bg-yellow-100 text-yellow-700',
                                                'comprado' => 'bg-emerald-100 text-emerald-700',
                                                'cancelado' => 'bg-red-100 text-red-600',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp

                                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badge }}">
                                            {{ ucfirst($pedido->estado) }}
                                        </span>
                                    </td>

                                    {{-- Fecha de Creación --}}
                                    <td class="px-6 py-4 text-center text-gray-500">
                                        {{ $pedido->created_at->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-3">

                                            {{-- ===================== --}}
                                            {{-- PEDIDO CANCELADO --}}
                                            {{-- ===================== --}}
                                            @if ($pedido->estado === 'cancelado')
                                                <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST"
                                                    onclick="event.stopPropagation()"
                                                    onsubmit="event.preventDefault(); openConfirmModal({
                    form: this,
                    title: 'Eliminar pedido',
                    message: 'El pedido será eliminado de forma definitiva.',
                    buttonText: 'Eliminar',
                    buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                    icon: `<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                        d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'/>
                    </svg>`
                })">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="px-3 py-1.5 text-xs font-medium rounded-lg
                           bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                {{-- ===================== --}}
                                                {{-- PEDIDO ACTIVO --}}
                                                {{-- ===================== --}}

                                                {{-- Comprar --}}
                                                @if ($pedido->estado === 'pendiente')
                                                    <form action="{{ route('pedidos.comprar', $pedido) }}"
                                                        method="POST" onclick="event.stopPropagation()"
                                                        onsubmit="event.preventDefault(); openConfirmModal({
                        form: this,
                        title: 'Marcar como comprado',
                        message: 'El pedido cambiará a estado (COMPRADO).',
                        buttonText: 'Confirmar',
                        buttonClass: 'bg-emerald-600 hover:bg-emerald-700 text-white',
                        icon: `<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/>
                        </svg>`
                    })">

                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                            class="px-3 py-1.5 text-xs font-medium rounded-lg
                               bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
                                                            Comprado
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Restaurar los pedidos ocultos --}}

                                                @if ($pedido->oculto || $pedido->estado === 'cancelado')
                                                    <form action="{{ route('pedidos.restaurar', $pedido) }}"
                                                        method="POST" onclick="event.stopPropagation()"
                                                        onsubmit="event.preventDefault(); openConfirmModal({
          form: this,
          title: 'Restaurar pedido',
          message: 'El pedido volverá a estar activo.',
          buttonText: 'Restaurar',
          buttonClass: 'bg-green-600 hover:bg-green-700 text-white',
          icon: `<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
              <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/>
          </svg>`
      })">

                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                            class="px-3 py-1.5 text-xs font-medium rounded-lg
               bg-green-50 text-green-700 hover:bg-green-100 transition">
                                                            Restaurar
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Cancelar --}}
                                                @if ($pedido->estado !== 'cancelado')
                                                    <form action="{{ route('pedidos.cancelar', $pedido) }}"
                                                        method="POST" onclick="event.stopPropagation()"
                                                        onsubmit="event.preventDefault(); openConfirmModal({
                        form: this,
                        title: 'Cancelar pedido',
                        message: 'El pedido quedará (CANCELADO).',
                        buttonText: 'Cancelar',
                        buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                        icon: `<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                            d='M6 18L18 6M6 6l12 12'/>
                        </svg>`
                    })">

                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                            class="px-3 py-1.5 text-xs font-medium rounded-lg
                               bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                            Cancelado
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                        </div>
                                    </td>


                                </tr>

                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center text-gray-400">
                                        No se encontraron pedidos.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                @if ($pedidos->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $pedidos->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
