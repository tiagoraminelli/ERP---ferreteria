<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Nuevo Pedido</h1>
                <p class="text-sm text-white-500 dark:text-gray-400">
                    Registro de compra pendiente
                </p>
            </div>

            <a href="{{ route('pedidos.index') }}"
                class="px-4 py-2 bg-white/10 text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-white/20 transition">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="flex justify-center">
            <div class="w-full max-w-7xl px-6 mt-2">

                <form action="{{ route('pedidos.store') }}" method="POST">
                    @csrf

                    {{-- RESUMEN DE ERRORES --}}
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">
                            <p class="text-sm font-semibold mb-2">
                                Se encontraron errores:
                            </p>
                            <ul class="text-sm list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                        {{-- TÍTULO --}}
                        <div class="mb-6 pb-4 border-b border-gray-100">
                            <h2 class="text-sm font-semibold text-gray-700">
                                Información del Pedido
                            </h2>
                            <p class="text-xs text-gray-400">
                                Complete los datos del pedido
                            </p>
                        </div>

                        {{-- GRID --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            {{-- COLUMNA IZQUIERDA --}}
                            <div class="space-y-5">
                                <div class="border-b border-gray-100 pb-2 mb-2">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Datos Principales
                                    </span>
                                </div>

                                {{-- Nombre --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                                        placeholder="Ej: Pedido de insumos"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('nombre') ? 'border-red-500' : '' }}"
                                        required>
                                    @error('nombre')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Descripción --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Descripción
                                    </label>
                                    <textarea name="descripcion" rows="3"
                                        placeholder="Observaciones del pedido"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('descripcion') ? 'border-red-500' : '' }}">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Categoría --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Categoría
                                    </label>
                                    <select name="categoria_id"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('categoria_id') ? 'border-red-500' : '' }}">
                                        <option value="">Seleccionar categoría</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}"
                                                {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Proveedor --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Proveedor
                                    </label>
                                    <input type="text" name="proveedor" value="{{ old('proveedor') }}"
                                        placeholder="Nombre del proveedor"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('proveedor') ? 'border-red-500' : '' }}">
                                    @error('proveedor')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- COLUMNA DERECHA --}}
                            <div class="space-y-5">
                                <div class="border-b border-gray-100 pb-2 mb-2">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Cantidad y Estado
                                    </span>
                                </div>

                                {{-- Cantidad --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Cantidad <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="cantidad" value="{{ old('cantidad', 1) }}"
                                        min="1"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('cantidad') ? 'border-red-500' : '' }}"
                                        required>
                                    @error('cantidad')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Estado --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Estado
                                    </label>
                                    <select name="estado"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                                        <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>
                                            Pendiente
                                        </option>
                                        <option value="comprado" {{ old('estado') == 'comprado' ? 'selected' : '' }}>
                                            Comprado
                                        </option>
                                        <option value="cancelado" {{ old('estado') == 'cancelado' ? 'selected' : '' }}>
                                            Cancelado
                                        </option>
                                    </select>
                                </div>

                                {{-- Observaciones --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Observaciones
                                    </label>
                                    <textarea name="observaciones" rows="3"
                                        placeholder="Notas adicionales"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('observaciones') ? 'border-red-500' : '' }}">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Visible --}}
                                <div class="mt-2">
                                    <label class="flex items-center gap-3">
                                        <input type="checkbox" name="visible" value="1"
                                            {{ old('visible', 1) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-black focus:ring-black">
                                        <span class="text-sm text-gray-600">
                                            Pedido visible
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- BOTÓN --}}
                        <div class="mt-6">
                            <button type="submit"
                                class="w-full py-3 bg-black text-black text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow-sm">
                                Crear Pedido
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
