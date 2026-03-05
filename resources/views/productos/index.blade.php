<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">Inventario</h2>
                <p class="text-sm text-black-500 dark:text-gray-400">Panel General de Productos</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- SELECTOR DE VISTA --}}
                <div class="flex bg-white/10 dark:bg-gray-200 p-1 rounded-xl">
                    <button onclick="cambiarVista('tabla')" id="btnVistaTabla"
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ !request('vista') || request('vista') == 'tabla' ? 'bg-white text-black shadow-sm' : 'text-black/70 hover:text-black' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                            Tabla
                        </span>
                    </button>
                    <button onclick="cambiarVista('grid')" id="btnVistaGrid"
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('vista') == 'grid' ? 'bg-white text-black shadow-sm' : 'text-black/70 hover:text-black' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                            Grid
                        </span>
                    </button>
                </div>

                {{-- BOTÓN NUEVO PRODUCTO --}}
                <a href="{{ route('productos.create') }}"
                    class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow whitespace-nowrap">
                    + Nuevo Producto
                </a>
            </div>
        </div>
    </x-slot>

    {{-- ================= MODAL DE CONFIRMACIÓN ================= --}}
    <x-confirm-modal />

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">

            {{-- ================= BARRA DE ACCIONES MASIVAS ================= --}}
            <div id="bulkActionsBar"
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hidden items-center justify-between transition-all">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-gray-700 whitespace-nowrap">
                        <span id="selectedCount">0</span> productos seleccionados
                    </span>
                    <button onclick="selectAll()"
                        class="text-xs text-gray-500 hover:text-gray-700 underline whitespace-nowrap">
                        Seleccionar todos
                    </button>
                    <button onclick="deselectAll()"
                        class="text-xs text-gray-500 hover:text-gray-700 underline whitespace-nowrap">
                        Limpiar selección
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openBulkPriceModal()"
                        class="px-4 py-2 bg-black text-black text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition whitespace-nowrap">
                        Actualizar Precios
                    </button>
                </div>
            </div>

            {{-- ================= FILTROS INTELIGENTES ================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5" style="margin-top: 0px">
                <form method="GET" action="{{ route('productos.index') }}" class="flex flex-wrap items-center gap-4">
                    <input type="hidden" name="vista" id="vistaInput" value="{{ request('vista', 'tabla') }}">

                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                        placeholder="Buscar producto, código o modelo..."
                        class="flex-1 min-w-[260px] rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                    {{-- Mostrar eliminados --}}
                    <label class="flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap">
                        <input type="checkbox" name="eliminados" value="1"
                            {{ request('eliminados') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-black focus:ring-black">
                        Solo eliminados
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

                    <select name="stock"
                        class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Stock</option>
                        <option value="disponible" {{ request('stock') == 'disponible' ? 'selected' : '' }}>Disponible
                        </option>
                        <option value="bajo" {{ request('stock') == 'bajo' ? 'selected' : '' }}>Bajo</option>
                        <option value="agotado" {{ request('stock') == 'agotado' ? 'selected' : '' }}>Agotado</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 text-sm bg-black text-black rounded-xl hover:bg-gray-800 transition whitespace-nowrap">
                        Filtrar
                    </button>

                    @if (request()->anyFilled(['buscar', 'categoria', 'marca', 'proveedor', 'stock', 'eliminados']))
                        <a href="{{ route('productos.index', ['vista' => request('vista', 'tabla')]) }}"
                            class="text-sm text-red-500 hover:underline whitespace-nowrap ">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            {{-- ================= VISTA TABLA ================= --}}
            <div id="vistaTabla" class="{{ request('vista', 'tabla') == 'tabla' ? 'block' : 'hidden' }}">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left">
                                        <input type="checkbox" onclick="toggleAllCheckboxes(this)"
                                            class="rounded border-gray-300 text-black focus:ring-black">
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Producto</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                        Stock</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Costo
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Precio</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Margen %</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Ganancia</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($productos as $producto)
                                    @php
                                        $costo = $producto->precio_costo ?? 0;
                                        $venta = $producto->precio ?? 0;
                                        $ganancia = $venta - $costo;
                                        $margen = $costo > 0 ? ($ganancia / $costo) * 100 : 0;
                                    @endphp

                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <input type="checkbox"
                                                class="product-checkbox rounded border-gray-300 text-black focus:ring-black"
                                                value="{{ $producto->id }}" data-precio="{{ $venta }}"
                                                onclick="updateSelectedCount()">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900">{{ $producto->nombre }}</div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ $producto->codigo_barra ?? 'Sin Código' }} |
                                                {{ $producto->marca->nombre ?? 'Sin Marca' }} |
                                                {{ $producto->categoria->nombre ?? 'Sin Categoría' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="{{ $producto->stock <= $producto->stock_minimo ? 'text-red-500 font-bold' : 'text-gray-800' }}">
                                                {{ number_format($producto->stock, 0) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-medium text-gray-600 hover:text-black underline">
                                                ${{ number_format($costo, 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-semibold text-green-600">
                                            ${{ number_format($venta, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full
                                                {{ $margen >= 30 ? 'bg-green-100 text-green-700' : ($margen < 15 ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
                                                {{ number_format($margen, 1) }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium">
                                            ${{ number_format($ganancia, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2 text-black">
                                                @if (request('eliminados'))
                                                    <form action="{{ route('productos.restaurar', $producto->id) }}"
                                                        method="POST"
                                                        onsubmit="event.preventDefault(); openConfirmModal({
                                                            form: this,
                                                            title: 'Restaurar producto',
                                                            message: 'El producto volverá a estar activo en el sistema.',
                                                            buttonText: 'Restaurar',
                                                            buttonClass: 'bg-green-600 hover:bg-green-700 text-black',
                                                            icon: `<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/>
                                                            </svg>`
                                                        })"
                                                        class="inline">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                            class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                                            title="Restaurar">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('productos.edit', $producto) }}"
                                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"
                                                        title="Editar">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('productos.destroy', $producto) }}"
                                                        method="POST"
                                                        onsubmit="event.preventDefault(); openConfirmModal({
                                                            form: this,
                                                            title: 'Enviar a la papelera',
                                                            message: 'El producto será desactivado y podrá restaurarse luego.',
                                                            buttonText: 'Eliminar',
                                                            buttonClass: 'bg-red-600 hover:bg-red-700 text-black',
                                                            icon: `<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                                                                d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'/>
                                                            </svg>`
                                                        })"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                            title="Eliminar">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-20 text-center text-gray-400">No se
                                            encontraron productos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($productos->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $productos->appends(['vista' => request('vista', 'tabla')])->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ================= VISTA GRID ================= --}}
            <div id="vistaGrid" class="{{ request('vista') == 'grid' ? 'block' : 'hidden' }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-5">

                    @forelse($productos as $producto)
                        @php
                            $costo = $producto->precio_costo ?? 0;
                            $venta = $producto->precio ?? 0;
                            $ganancia = $venta - $costo;
                            $margen = $costo > 0 ? ($ganancia / $costo) * 100 : 0;
                        @endphp

                        <div
                            class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group relative overflow-hidden">

                            {{-- Checkbox --}}
                            <div class="absolute right-0 top-0 p-2" style="z-index: 10">
                                <input type="checkbox"
                                    class="product-checkbox rounded border-gray-300 text-black focus:ring-black"
                                    value="{{ $producto->id }}" data-precio="{{ $venta }}"
                                    onclick="updateSelectedCount()">
                            </div>

                            {{-- Imagen --}}
                            <div
                                class="h-40 bg-gradient-to-br from-gray-50 to-gray-100 relative flex items-center justify-center">

                                @if ($producto->imagen)
                                    <img src="{{ $producto->imagen }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="text-gray-300">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Badge stock --}}
                                <div class="absolute top-3 right-3">
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-bold rounded-full
                            {{ $producto->stock <= $producto->stock_minimo ? 'bg-red-500 text-black' : 'bg-emerald-500 text-black' }}">
                                        {{ number_format($producto->stock, 0) }} uds
                                    </span>
                                </div>
                            </div>

                            {{-- Contenido --}}
                            <div class="p-4 space-y-3">

                                {{-- Nombre --}}
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm truncate">
                                        {{ $producto->nombre }}
                                    </h3>
                                    <p class="text-[11px] text-gray-400 truncate">
                                        {{ $producto->marca->nombre ?? 'Sin marca' }} •
                                        {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                    </p>
                                </div>

                                {{-- Precios --}}
                                <div class="grid grid-cols-2 gap-4 text-sm">

                                    {{-- COSTO (abre modal) --}}
                                    <div class="bg-gray-50 rounded-xl p-3 hover:bg-gray-100 transition cursor-pointer"
                                        onclick="openModal({{ $producto->id }}, {{ $costo }}, '{{ addslashes($producto->nombre) }}')">

                                        <p class="text-[9px] text-gray-400 uppercase tracking-wide">
                                            Costo
                                        </p>

                                        <p class="font-medium text-gray-700">
                                            ${{ number_format($costo, 2, ',', '.') }}
                                        </p>
                                    </div>

                                    {{-- VENTA --}}
                                    <div class="bg-black text-black rounded-xl p-3 text-right">
                                        <p class="text-[9px] uppercase opacity-70 tracking-wide">
                                            Venta
                                        </p>
                                        <p class="font-semibold">
                                            ${{ number_format($venta, 2, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Margen + Ganancia --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">

                                    <span
                                        class="px-2.5 py-1 text-[10px] font-bold rounded-full
                            {{ $margen >= 30
                                ? 'bg-emerald-100 text-emerald-700'
                                : ($margen < 15
                                    ? 'bg-red-100 text-red-600'
                                    : 'bg-amber-100 text-amber-700') }}">

                                        {{ number_format($margen, 1) }}% margen
                                    </span>

                                    <div class="text-right">
                                        <p class="text-[9px] text-gray-400 uppercase">
                                            Ganancia
                                        </p>
                                        <p
                                            class="text-sm font-semibold
                                {{ $ganancia > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                            ${{ number_format($ganancia, 2, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Acciones --}}
                                <div class="pt-3 border-t border-gray-100">

                                    @if (request('eliminados'))
                                        <form action="{{ route('productos.restaurar', $producto->id) }}"
                                            method="POST"
                                            onsubmit="event.preventDefault(); openConfirmModal({
                                                            form: this,
                                                            title: 'Restaurar producto',
                                                            message: 'El producto volverá a estar activo en el sistema.',
                                                            buttonText: 'Restaurar',
                                                            buttonClass: 'bg-green-600 hover:bg-green-700 text-black',
                                                            icon: `<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/>
                                                            </svg>`
                                                        })"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                                title="Restaurar">
                                               Restaurar
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex rounded-xl overflow-hidden border border-gray-200">

                                            <a href="{{ route('productos.edit', $producto) }}"
                                                class="flex-1 text-center py-2 text-xs font-medium
                                           bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                                                Editar
                                            </a>

                                            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                                onsubmit="return confirm('¿Enviar a la papelera?')" class="flex-1">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="w-full py-2 text-xs font-medium
                                               bg-red-50 hover:bg-red-100
                                               text-red-600 transition border-l border-gray-200">
                                                    Eliminar
                                                </button>
                                            </form>

                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                    @empty

                        <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-20 text-center">
                            <p class="text-gray-400">No se encontraron productos.</p>
                        </div>
                    @endforelse

                </div>

                @if ($productos->hasPages())
                    <div class="mt-6 p-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
                        {{ $productos->appends(['vista' => 'grid'])->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ================= MODAL ACTUALIZACIÓN MASIVA DE PRECIOS ================= --}}
    <div id="modalPrecioMasivo"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white w-[420px] max-w-[90%] rounded-2xl p-6 shadow-2xl">
            <h3 class="text-lg font-bold mb-4">Actualización Masiva de Precios</h3>

            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-600">
                    <span id="bulkSelectedCount">0</span> productos seleccionados
                </span>
            </div>

            <form id="formPrecioMasivo" method="POST" action="{{ route('productos.bulk-update-prices') }}">
                @csrf
                <input type="hidden" name="productos" id="selectedProductosInput" value="">

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de actualización
                        </label>
                        <select name="tipo_actualizacion" id="tipoActualizacion"
                            class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black"
                            onchange="togglePriceInputs()">
                            <option value="fijo">Precio fijo</option>
                            <option value="porcentaje_aumento">Aumento porcentual (+%)</option>
                            <option value="porcentaje_disminucion">Disminución porcentual (-%)</option>
                        </select>
                    </div>

                    <div id="inputFijo" class="price-input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nuevo precio</label>
                        <input type="number" step="0.01" name="precio_fijo"
                            class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black"
                            placeholder="0.00">
                    </div>

                    <div id="inputPorcentaje" class="price-input-group hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Porcentaje</label>
                        <input type="number" step="0.1" name="porcentaje"
                            class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black"
                            placeholder="Ej: 10 para 10%">
                    </div>

                    <div class="bg-yellow-50 p-3 rounded-lg">
                        <p class="text-xs text-yellow-700">
                            <strong>Vista previa:</strong>
                            <span id="previewMessage">Selecciona productos y tipo de actualización</span>
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeBulkPriceModal()"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-800">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-black text-black rounded-xl text-sm font-semibold hover:bg-gray-800 transition">
                        Actualizar Precios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/productos.js') }}"></script>
</x-app-layout>
