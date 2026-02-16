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
                    <button onclick="cambiarVista('tabla')"
                        id="btnVistaTabla"
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ !request('vista') || request('vista') == 'tabla' ? 'bg-white text-black shadow-sm' : 'text-white/70 hover:text-white' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Tabla
                        </span>
                    </button>
                    <button onclick="cambiarVista('grid')"
                        id="btnVistaGrid"
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('vista') == 'grid' ? 'bg-white text-black shadow-sm' : 'text-white/70 hover:text-white' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            Grid
                        </span>
                    </button>
                </div>

                <a href="{{ route('productos.create') }}"
                    class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow">
                    + Nuevo Producto
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 space-y-6">

            {{-- ================= KPIs ERP COMPACTOS ================= --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Total Productos --}}
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between lg:justify-start lg:gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="text-right lg:text-left">
                            <p class="text-[8px] lg:text-[10px] text-gray-400 uppercase tracking-wider">Total</p>
                            <p class="text-sm lg:text-xl font-bold text-gray-900">{{ number_format($totalProductos) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Activos --}}
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between lg:justify-start lg:gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right lg:text-left">
                            <p class="text-[8px] lg:text-[10px] text-gray-400 uppercase tracking-wider">Activos</p>
                            <p class="text-sm lg:text-xl font-bold text-green-600">{{ $productosActivos }}</p>
                        </div>
                    </div>
                </div>

                {{-- Stock Bajo --}}
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between lg:justify-start lg:gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $stockBajoCount > 0 ? 'bg-red-50' : 'bg-gray-50' }} flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $stockBajoCount > 0 ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="text-right lg:text-left">
                            <p class="text-[8px] lg:text-[10px] text-gray-400 uppercase tracking-wider">Stock Bajo</p>
                            <p class="text-sm lg:text-xl font-bold {{ $stockBajoCount > 0 ? 'text-red-500' : 'text-gray-800' }}">
                                {{ $stockBajoCount }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Valor Inventario --}}
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between lg:justify-start lg:gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right lg:text-left">
                            <p class="text-[8px] lg:text-[10px] text-gray-400 uppercase tracking-wider">Valor Inv.</p>
                            <p class="text-sm lg:text-xl font-bold text-gray-900">${{ number_format($valorInventario, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= FILTROS INTELIGENTES ================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <form method="GET" action="{{ route('productos.index') }}" class="flex flex-wrap items-center gap-4">
                    <input type="hidden" name="vista" id="vistaInput" value="{{ request('vista', 'tabla') }}">

                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                        placeholder="Buscar producto, código o modelo..."
                        class="flex-1 min-w-[260px] rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                    <select name="categoria" class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Categoría</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <select name="marca" class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Marca</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}" {{ request('marca') == $marca->id ? 'selected' : '' }}>
                                {{ $marca->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <select name="proveedor" class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Proveedor</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" {{ request('proveedor') == $proveedor->id ? 'selected' : '' }}>
                                {{ $proveedor->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <select name="stock" class="rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                        <option value="">Stock</option>
                        <option value="disponible" {{ request('stock') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="bajo" {{ request('stock') == 'bajo' ? 'selected' : '' }}>Bajo</option>
                        <option value="agotado" {{ request('stock') == 'agotado' ? 'selected' : '' }}>Agotado</option>
                    </select>

                    <button type="submit" class="text-sm text-red-500 hover:underline">
                        Filtrar
                    </button>

                    @if (request()->anyFilled(['buscar', 'categoria', 'marca', 'proveedor', 'stock']))
                        <a href="{{ route('productos.index', ['vista' => request('vista', 'tabla')]) }}" class="text-sm text-red-500 hover:underline">
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
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Producto</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Stock</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Costo</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Precio</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Margen %</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Ganancia</th>
                                    <th class="px-6 py-4 text-right"></th>
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
                                            <div class="font-semibold text-gray-900">{{ $producto->nombre }}</div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ $producto->codigo_barra ?? 'Sin Código' }} |
                                                {{ $producto->marca->nombre ?? 'Sin Marca' }} |
                                                {{ $producto->categoria->nombre ?? 'Sin Categoría' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="{{ $producto->stock <= $producto->stock_minimo ? 'text-red-500 font-bold' : 'text-gray-800' }}">
                                                {{ number_format($producto->stock, 0) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button onclick="openModal({{ $producto->id }}, {{ $costo }}, '{{ $producto->nombre }}')"
                                                class="font-medium text-gray-600 hover:text-black underline">
                                                ${{ number_format($costo, 2, ',', '.') }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                            ${{ number_format($venta, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $margen >= 30 ? 'bg-green-100 text-green-700' : ($margen < 15 ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
                                                {{ number_format($margen, 1) }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium">
                                            ${{ number_format($ganancia, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                                onsubmit="return confirm('Enviar a papelera?')">
                                                @csrf @method('DELETE')
                                                <button class="text-gray-400 hover:text-red-600 transition">🗑</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-20 text-center text-gray-400">No se encontraron productos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($productos->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">{{ $productos->appends(['vista' => request('vista', 'tabla')])->links() }}</div>
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
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all overflow-hidden group">
                            <div class="h-32 bg-gradient-to-br from-gray-50 to-gray-100 relative">
                                @if($producto->imagen)
                                    <img src="{{ $producto->imagen }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full
                                        {{ $producto->stock <= $producto->stock_minimo ? 'bg-red-500 text-white' : 'bg-green-500 text-white' }}">
                                        {{ number_format($producto->stock, 0) }} uds
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-1 truncate">{{ $producto->nombre }}</h3>
                                <div class="text-[10px] text-gray-400 mb-3 space-y-0.5">
                                    <div class="truncate">{{ $producto->codigo_barra ?? 'Sin código' }}</div>
                                    <div>{{ $producto->marca->nombre ?? 'Sin marca' }} • {{ $producto->categoria->nombre ?? 'Sin categoría' }}</div>
                                </div>
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-[8px] text-gray-400 uppercase">Costo</p>
                                        <button onclick="openModal({{ $producto->id }}, {{ $costo }}, '{{ $producto->nombre }}')"
                                            class="text-xs font-medium text-gray-600 hover:text-black underline">
                                            ${{ number_format($costo, 2, ',', '.') }}
                                        </button>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[8px] text-gray-400 uppercase">Venta</p>
                                        <p class="text-sm font-bold text-gray-900">${{ number_format($venta, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div>
                                        <span class="px-2 py-1 text-[9px] font-bold rounded-full
                                            {{ $margen >= 30 ? 'bg-green-100 text-green-700' : ($margen < 15 ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ number_format($margen, 1) }}% margen
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[8px] text-gray-400 uppercase">Ganancia</p>
                                        <p class="text-xs font-medium {{ $ganancia > 0 ? 'text-green-600' : 'text-red-500' }}">
                                            ${{ number_format($ganancia, 2, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-2 mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('productos.edit', $producto) }}"
                                       class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                          onsubmit="return confirm('Enviar a papelera?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-20 text-center">
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

    {{-- ================= MODAL COSTO ================= --}}
    <div id="modalCosto" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold mb-2">Actualizar Costo</h3>
            <p id="modalProductName" class="text-sm text-gray-400 mb-4"></p>
            <form id="formCosto" method="POST">
                @csrf @method('PUT')
                <input type="number" step="0.01" name="precio_costo" id="inputCosto"
                    class="w-full rounded-xl border-gray-200 text-lg font-semibold mb-6 focus:ring-black focus:border-black">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-800">Cancelar</button>
                    <button type="submit" class="px-6 py-2 bg-black text-white rounded-xl text-sm font-semibold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function cambiarVista(vista) {
            // Actualizar URL con el parámetro de vista
            const url = new URL(window.location.href);
            url.searchParams.set('vista', vista);
            window.location.href = url.toString();
        }

        function openModal(id, costo, nombre) {
            document.getElementById('modalCosto').classList.remove('hidden');
            document.getElementById('modalCosto').classList.add('flex');
            document.getElementById('inputCosto').value = costo;
            document.getElementById('modalProductName').innerText = nombre;
            document.getElementById('formCosto').action = `/productos/${id}`;
        }

        function closeModal() {
            document.getElementById('modalCosto').classList.add('hidden');
            document.getElementById('modalCosto').classList.remove('flex');
        }
    </script>

</x-app-layout>
