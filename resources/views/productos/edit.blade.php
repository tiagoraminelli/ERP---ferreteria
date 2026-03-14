<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Editar Producto</h1>
                <p class="text-sm text-white-500 dark:text-gray-400">
                    Modificación del artículo en el sistema
                </p>
            </div>

            <a href="{{ route('productos.index') }}"
                class="px-4 py-2 bg-white/10 text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-white/20 transition">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="flex justify-center">
            <div class="w-full max-w-3xl px-6 mt-2">

                <form action="{{ route('productos.update', $producto) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- ERRORES --}}
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">
                            <p class="text-sm font-semibold mb-2">
                                Se encontraron errores en el formulario:
                            </p>
                            <ul class="text-sm list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    {{-- GRID PRINCIPAL --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        {{-- COLUMNA IZQUIERDA --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                            <div class="mb-6 pb-4 border-b border-gray-100">
                                <h2 class="text-sm font-semibold text-gray-700">
                                    Información del Producto
                                </h2>
                                <p class="text-xs text-gray-400">
                                    Actualice los campos necesarios
                                </p>
                            </div>

                            <div class="space-y-5">

                                {{-- Nombre --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>

                                    <input type="text"
                                        name="nombre"
                                        value="{{ old('nombre', $producto->nombre) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black"
                                        required>
                                </div>


                                {{-- Descripción --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Descripción
                                    </label>

                                    <textarea name="descripcion"
                                        rows="3"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                </div>


                                {{-- Categoría --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Categoría
                                    </label>

                                    <select name="categoria_id"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                                        <option value="">Seleccionar categoría</option>

                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}"
                                                {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>


                                {{-- Unidad --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Unidad de Medida <span class="text-red-500">*</span>
                                    </label>

                                    <select name="unidad_medida_id"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                                        <option value="">Seleccionar unidad</option>

                                        @foreach ($unidades as $unidad)
                                            <option value="{{ $unidad->id }}"
                                                {{ old('unidad_medida_id', $producto->unidad_medida_id) == $unidad->id ? 'selected' : '' }}>
                                                {{ $unidad->nombre }} ({{ $unidad->abreviatura }})
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                            </div>

                        </div>


                        {{-- COLUMNA DERECHA --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                            <div class="mb-6 pb-4 border-b border-gray-100">
                                <h2 class="text-sm font-semibold text-gray-700">
                                    Precios y Stock
                                </h2>
                                <p class="text-xs text-gray-400">
                                    Configuración económica del producto
                                </p>
                            </div>

                            <div class="space-y-5">

                                {{-- Modelo --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Modelo
                                    </label>

                                    <input type="text"
                                        name="modelo"
                                        value="{{ old('modelo', $producto->modelo) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                                </div>


                                {{-- Precio Costo --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Precio Costo
                                    </label>

                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">$</span>
                                        </div>

                                        <input type="number"
                                            step="0.01"
                                            name="precio_costo"
                                            value="{{ old('precio_costo', $producto->precio_costo) }}"
                                            class="w-full pl-8 rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                                    </div>
                                </div>


                                {{-- Margen --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Margen %
                                    </label>

                                    <div class="relative">
                                        <input type="number"
                                            step="0.01"
                                            name="margen_ganancia"
                                            value="{{ old('margen_ganancia', $producto->margen_ganancia) }}"
                                            class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">%</span>
                                        </div>
                                    </div>
                                </div>


                                {{-- Precio Venta --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Precio Venta <span class="text-red-500">*</span>
                                    </label>

                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">$</span>
                                        </div>

                                        <input type="number"
                                            step="0.01"
                                            name="precio"
                                            value="{{ old('precio', $producto->precio) }}"
                                            class="w-full pl-8 rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black"
                                            required>
                                    </div>
                                </div>


                                {{-- Stock --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Stock <span class="text-red-500">*</span>
                                    </label>

                                    <input type="number"
                                        step="0.001"
                                        name="stock"
                                        value="{{ old('stock', $producto->stock) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black"
                                        required>
                                </div>


                                {{-- Stock mínimo --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Stock Mínimo <span class="text-red-500">*</span>
                                    </label>

                                    <input type="number"
                                        step="0.001"
                                        name="stock_minimo"
                                        value="{{ old('stock_minimo', $producto->stock_minimo) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black"
                                        required>
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ACTIVO + BOTÓN --}}
                    <div class="mt-8 border-gray-100 mb-5">

                        <div class="grid grid-cols-2 items-center pt-4 pb-6 mb-6">

                            {{-- ACTIVO --}}
                            <div class="flex items-center gap-3">
                                <input type="checkbox"
                                    name="activo"
                                    value="1"
                                    {{ old('activo', $producto->activo) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-black focus:ring-black">

                                <span class="text-sm text-gray-600">
                                    Producto activo (visible en ventas)
                                </span>
                            </div>

                            {{-- BOTÓN --}}
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="px-8 py-3 bg-black text-black text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow-sm">
                                    Actualizar Producto
                                </button>
                            </div>

                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/create.productos.js') }}"></script>

</x-app-layout>
