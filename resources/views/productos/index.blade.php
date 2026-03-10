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
                    {{-- <button onclick="cambiarVista('grid')" id="btnVistaGrid"
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('vista') == 'grid' ? 'bg-white text-black shadow-sm' : 'text-black/70 hover:text-black' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                            Grid
                        </span>
                    </button> --}}
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
                                                value="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"
                                                data-precio="{{ $venta }}" data-costo="{{ $costo }}"
                                                data-stock="{{ $producto->stock }}"
                                                data-margen="{{ $margen }}" onclick="updateSelectedCount()">
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
                                            <span class="font-medium text-gray-600">
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
                                                            buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
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


        </div>
    </div>

    <div id="modalPrecioMasivo"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4" >
        <div class="bg-white rounded-2xl shadow-2xl flex flex-col m-auto overflow-hidden"
            style="max-height: 80vh; max-width: 800px;">

            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-white">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Actualización Masiva</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <span id="modalSelectedCount" class="font-bold text-blue-600">0</span> productos en cola para
                        modificar
                    </p>
                </div>
                <button onclick="closeBulkPriceModal()"
                    class="p-2 hover:bg-gray-100 rounded-full text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto p-6 bg-gray-50/50">
                <form id="bulkUpdateForm">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <div class="flex flex-col space-y-4">
                            <label class="text-sm font-bold text-gray-700">Productos Seleccionados</label>

                            {{-- <div class="bg-white p-1 rounded-xl shadow-sm border border-gray-200">
                                <select id="selectProductos" class="w-full" multiple="multiple"></select>
                            </div> --}}

                            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
                                <div id="bulkProductList" class="divide-y divide-gray-100 overflow-y-auto"
                                    style="height: 380px;">
                                </div>
                            </div>

                            <button type="button" onclick="clearProductList()"
                                class="text-xs text-red-500 hover:underline w-fit flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Limpiar lista de selección
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-5">
                                <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Configuración</h4>

                                <div>
                                    <label class="text-xs font-semibold text-gray-500 mb-2 block">¿Qué valor quieres
                                        cambiar?</label>
                                    <select name="campo" id="campoActualizar"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black">
                                        <option value="precio">💰 Precio de Venta</option>
                                        <option value="precio_costo">📦 Precio de Costo</option>
                                        <option value="margen_ganancia">📊 Margen de Ganancia %</option>
                                        <option value="stock">📈 Stock Total</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 mb-2 block">Operación</label>
                                        <select name="tipo" id="tipoOperacion"
                                            class="w-full rounded-xl border-gray-200 text-sm focus:ring-black">
                                            <option value="fijo">Fijo (=)</option>
                                            <option value="sumar">Aumento (+%)</option>
                                            <option value="restar">Descuento (-%)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 mb-2 block">Valor</label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                                                id="valorSimbolo">$</span>
                                            <input type="number" name="valor" id="valorOperacion" step="0.01"
                                                class="w-full rounded-xl border-gray-200 text-sm pl-8 focus:ring-black"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-900 rounded-xl p-4 text-white">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                        <span class="text-[10px] uppercase font-bold text-gray-400">Resumen de
                                            cambios</span>
                                    </div>
                                    <div id="previewContent" class="text-sm space-y-1">
                                        <p id="previewOperation" class="font-medium text-black">Seleccione una
                                            operación</p>
                                    </div>
                                </div>
                            </div>

                            <div id="validationMessage"
                                class="hidden bg-red-50 border border-red-100 text-red-600 rounded-xl p-4 text-xs">
                                <span id="validationText"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-white flex justify-end gap-3">
                <button type="button" onclick="closeBulkPriceModal()"
                    class="px-5 py-2.5 text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-xl transition">Cancelar</button>
                <button type="button" onclick="submitBulkUpdate()"
                    class="px-5 py-2.5 text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-xl transition">
                    Confirmar Cambios
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <script>
        // Inicialización de Select2 para el modal mejorado
        $(document).ready(function() {
            initSelect2();
        });

        function initSelect2() {
            if ($('#selectProductos').length) {
                $('#selectProductos').select2({
                    placeholder: 'Buscar y agregar productos...',
                    width: '100%',
                    allowClear: true,
                    dropdownParent: $('#modalPrecioMasivo'),
                    language: {
                        noResults: function() {
                            return "No se encontraron productos";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    }
                });
            }
        }
    </script>

    <script src="{{ asset('js/productos.js') }}"></script>
</x-app-layout>
