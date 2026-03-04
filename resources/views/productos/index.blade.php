<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">Inventario</h2>
                <p class="text-sm text-white-500 dark:text-gray-400">Panel General de Productos</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- SELECTOR DE VISTA --}}
                <div class="flex bg-white/10 dark:bg-gray-200 p-1 rounded-xl">
                    <button onclick="cambiarVista('tabla')" id="btnVistaTabla"
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ !request('vista') || request('vista') == 'tabla' ? 'bg-white text-black shadow-sm' : 'text-white/70 hover:text-white' }}">
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
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('vista') == 'grid' ? 'bg-white text-black shadow-sm' : 'text-white/70 hover:text-white' }}">
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
                    class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow">
                    + Nuevo Producto
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 space-y-6">

            {{-- ================= BARRA DE ACCIONES MASIVAS ================= --}}
            <div id="bulkActionsBar"
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hidden items-center justify-between transition-all">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-gray-700">
                        <span id="selectedCount">0</span> productos seleccionados
                    </span>
                    <button onclick="selectAll()" class="text-xs text-gray-500 hover:text-gray-700 underline">
                        Seleccionar todos
                    </button>
                    <button onclick="deselectAll()" class="text-xs text-gray-500 hover:text-gray-700 underline">
                        Limpiar selección
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openBulkPriceModal()"
                        class="px-4 py-2 bg-black text-black text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition">
                        Actualizar Precios
                    </button>
                </div>
            </div>

            {{-- ================= FILTROS INTELIGENTES ================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
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
                        class="px-4 py-2 text-sm bg-black text-black rounded-xl hover:bg-gray-800 transition">
                        Filtrar
                    </button>

                    @if (request()->anyFilled(['buscar', 'categoria', 'marca', 'proveedor', 'stock', 'eliminados']))
                        <a href="{{ route('productos.index', ['vista' => request('vista', 'tabla')]) }}"
                            class="text-sm text-red-500 hover:underline">
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
                                            <span
                                                class="font-medium text-gray-600 hover:text-black underline">
                                                ${{ number_format($costo, 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
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
                                                    <form action="{{ route('productos.restaurar', $producto) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-black text-xs font-medium rounded-lg transition">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                            Restaurar
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('productos.edit', $producto) }}"
                                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"
                                                        title="Editar">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                                        onsubmit="return confirm('¿Enviar a la papelera?')" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                            title="Eliminar">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($productos as $producto)
                        @php
                            $costo = $producto->precio_costo ?? 0;
                            $venta = $producto->precio ?? 0;
                            $ganancia = $venta - $costo;
                            $margen = $costo > 0 ? ($ganancia / $costo) * 100 : 0;
                        @endphp
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all overflow-hidden group relative">
                            {{-- Checkbox para selección masiva --}}
                            <div class="absolute top-2 left-2 z-10">
                                <input type="checkbox"
                                    class="product-checkbox rounded border-gray-300 text-black focus:ring-black"
                                    value="{{ $producto->id }}" data-precio="{{ $venta }}"
                                    onclick="updateSelectedCount()">
                            </div>

                            <div class="h-32 bg-gradient-to-br from-gray-50 to-gray-100 relative">
                                @if ($producto->imagen)
                                    <img src="{{ $producto->imagen }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2">
                                    <span
                                        class="px-2 py-1 text-[10px] font-bold rounded-full
                                        {{ $producto->stock <= $producto->stock_minimo ? 'bg-red-500 text-white' : 'bg-green-500 text-white' }}">
                                        {{ number_format($producto->stock, 0) }} uds
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-1 truncate">{{ $producto->nombre }}</h3>
                                <div class="text-[10px] text-gray-400 mb-3 space-y-0.5">
                                    <div class="truncate">{{ $producto->codigo_barra ?? 'Sin código' }}</div>
                                    <div>{{ $producto->marca->nombre ?? 'Sin marca' }} •
                                        {{ $producto->categoria->nombre ?? 'Sin categoría' }}</div>
                                </div>
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-[8px] text-gray-400 uppercase">Costo</p>
                                        <button
                                            onclick="openModal({{ $producto->id }}, {{ $costo }}, '{{ $producto->nombre }}')"
                                            class="text-xs font-medium text-gray-600 hover:text-black underline">
                                            ${{ number_format($costo, 2, ',', '.') }}
                                        </button>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[8px] text-gray-400 uppercase">Venta</p>
                                        <p class="text-sm font-bold text-gray-900">
                                            ${{ number_format($venta, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div>
                                        <span
                                            class="px-2 py-1 text-[9px] font-bold rounded-full
                                            {{ $margen >= 30 ? 'bg-green-100 text-green-700' : ($margen < 15 ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ number_format($margen, 1) }}% margen
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[8px] text-gray-400 uppercase">Ganancia</p>
                                        <p
                                            class="text-xs font-medium {{ $ganancia > 0 ? 'text-green-600' : 'text-red-500' }}">
                                            ${{ number_format($ganancia, 2, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- ACCIONES EN GRID --}}
                                <div class="mt-4 pt-3 border-t border-gray-100">
                                    @if (request('eliminados'))
                                        <form action="{{ route('productos.restaurar', $producto) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Restaurar Producto
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex items-center justify-between">
                                            <a href="{{ route('productos.edit', $producto) }}"
                                                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-l-lg transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                                Editar
                                            </a>
                                            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                                onsubmit="return confirm('¿Enviar a la papelera?')" class="flex-1">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-r-lg border-l border-red-200 transition">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-20 text-center">
                            <p class="text-gray-400">No se encontraron productos.</p>
                        </div>
                    @endforelse
                </div>
                @if ($productos->hasPages())
                    <div class="mt-6 px-6 py-4 bg-white rounded-2xl shadow-sm border border-gray-100">
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
