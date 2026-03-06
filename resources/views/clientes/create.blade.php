<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Nuevo Cliente</h1>
                <p class="text-sm text-white-500 dark:text-gray-400">
                    Registro de cliente en el sistema
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

                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf

                    {{-- ERRORES --}}
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

                        {{-- TITULO --}}
                        <div class="mb-6 pb-4 border-b border-gray-100">
                            <h2 class="text-sm font-semibold text-gray-700">
                                Información del Cliente
                            </h2>
                            <p class="text-xs text-gray-400">
                                Complete los datos del cliente
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

                                {{-- NOMBRE --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>

                                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                                        placeholder="Ej: Juan Pérez"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('nombre') ? 'border-red-500' : '' }}"
                                        required>

                                    @error('nombre')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- TELEFONO --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Teléfono
                                    </label>

                                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                                        placeholder="Ej: 3564 123456"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('telefono') ? 'border-red-500' : '' }}">

                                    @error('telefono')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- EMAIL --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Email
                                    </label>

                                    <input type="email" name="email" value="{{ old('email') }}"
                                        placeholder="cliente@email.com"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('email') ? 'border-red-500' : '' }}">

                                    @error('email')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- DIRECCION --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Dirección
                                    </label>

                                    <input type="text" name="direccion" value="{{ old('direccion') }}"
                                        placeholder="Ej: Av. San Martín 123"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('direccion') ? 'border-red-500' : '' }}">

                                    @error('direccion')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            {{-- COLUMNA DERECHA --}}
                            <div class="space-y-5">

                                <div class="border-b border-gray-100 pb-2 mb-2">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Ubicación
                                    </span>
                                </div>

                                {{-- CIUDAD --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Ciudad
                                    </label>

                                    <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('ciudad') ? 'border-red-500' : '' }}">

                                    @error('ciudad')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- PROVINCIA --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Provincia
                                    </label>

                                    <input type="text" name="provincia" value="{{ old('provincia') }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('provincia') ? 'border-red-500' : '' }}">

                                    @error('provincia')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- CODIGO POSTAL --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Código Postal
                                    </label>

                                    <input type="text" name="codigo_postal" value="{{ old('codigo_postal') }}"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('codigo_postal') ? 'border-red-500' : '' }}">

                                    @error('codigo_postal')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- OBSERVACIONES --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                        Observaciones
                                    </label>

                                    <textarea name="observaciones" rows="3" placeholder="Notas sobre el cliente"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-black focus:border-black
                                        {{ $errors->has('observaciones') ? 'border-red-500' : '' }}">{{ old('observaciones') }}</textarea>

                                    @error('observaciones')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- ACTIVO --}}
                                <div class="mt-2">
                                    <label class="flex items-center gap-3">
                                        <input type="checkbox" name="activo" value="1"
                                            {{ old('activo', 1) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-black focus:ring-black">

                                        <span class="text-sm text-gray-600">
                                            Cliente activo
                                        </span>
                                    </label>
                                </div>

                            </div>
                        </div>

                        {{-- BOTON --}}
                        <div class="mt-6">
                            <button type="submit"
                                class="w-full py-3 bg-black text-black text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow-sm">
                                Crear Cliente
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
