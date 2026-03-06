<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Editar Cliente</h1>
                <p class="text-sm text-white-500 dark:text-gray-400">
                    Modificar información del cliente
                </p>
            </div>

            <a href="{{ route('clientes.index') }}"
                class="px-4 py-2 bg-white/10 text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-white/20 transition">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="flex justify-center">
            <div class="w-full max-w-7xl px-6 mt-2">

                <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                    @csrf
                    @method('PUT')

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

                    {{-- AVISO DE DATOS POR DEFECTO --}}
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded-xl p-4 mb-6">
                        <p class="text-sm font-semibold mb-1">
                            Información
                        </p>
                        <p class="text-xs leading-relaxed">
                            Por defecto, los clientes se registran con
                            <span class="font-semibold">Ciudad: San Cristóbal</span>,
                            <span class="font-semibold">Provincia: Santa Fe</span> y
                            <span class="font-semibold">Código Postal: 3070</span>.
                            <br>
                            Solo el campo <span class="font-semibold">Nombre</span> es obligatorio.
                            El resto de los datos pueden completarse opcionalmente.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                        {{-- TÍTULO --}}
                        <div class="mb-6 pb-4 border-b border-gray-100">
                            <h2 class="text-sm font-semibold text-gray-700">
                                Información del Cliente
                            </h2>
                            <p class="text-xs text-gray-400">
                                Actualice los datos del cliente
                            </p>
                        </div>

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
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('nombre') ? 'border-red-500' : '' }}"
                                        required>

                                    @error('nombre')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Dirección --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Dirección
                                    </label>
                                    <input type="text" name="direccion"
                                        value="{{ old('direccion', $cliente->direccion) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                                </div>

                                {{-- Teléfono --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Teléfono
                                    </label>
                                    <input type="text" name="telefono"
                                        value="{{ old('telefono', $cliente->telefono) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Email
                                    </label>
                                    <input type="email" name="email" value="{{ old('email', $cliente->email) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                                </div>

                            </div>

                            {{-- COLUMNA DERECHA --}}
                            <div class="space-y-5">

                                <div class="border-b border-gray-100 pb-2 mb-2">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Información Adicional
                                    </span>
                                </div>



                                {{-- Ciudad --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Ciudad
                                    </label>
                                    <input type="text" name="ciudad" value="{{ old('ciudad', $cliente->ciudad) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                                </div>

                                {{-- Provincia --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Provincia
                                    </label>
                                    <input type="text" name="provincia"
                                        value="{{ old('provincia', $cliente->provincia) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                                </div>

                                     {{-- CÓDIGO POSTAL --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Código Postal
                                    </label>
                                    <input type="text" name="codigo_postal"
                                        value="{{ old('codigo_postal', $cliente->codigo_postal) }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">
                                </div>
                                {{-- Observaciones --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Observaciones
                                    </label>
                                    <textarea name="observaciones" rows="3"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">{{ old('observaciones', $cliente->observaciones) }}</textarea>
                                </div>


                                {{-- Estado --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Estado
                                    </label>

                                    <select name="estado"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black">

                                        <option value="activo"
                                            {{ old('estado', $cliente->estado) == 'activo' ? 'selected' : '' }}>
                                            Activo
                                        </option>

                                        <option value="inactivo"
                                            {{ old('estado', $cliente->estado) == 'inactivo' ? 'selected' : '' }}>
                                            Inactivo
                                        </option>

                                    </select>
                                </div>

                            </div>

                        </div>

                        {{-- BOTÓN --}}
                        <div class="mt-6">
                            <button type="submit"
                                class="w-full py-3 bg-black text-black text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow-sm">
                                Guardar Cambios
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
