<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">
                    Cuenta Corriente
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $cliente->nombre }} - Movimientos
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('clientes.index') }}"
                    class="px-5 py-2.5 bg-black text-white text-xs font-bold uppercase rounded-xl hover:bg-gray-800 transition">
                    ← Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">

            {{-- RESUMEN --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500">Saldo Actual</p>
                    <p class="text-2xl font-bold
                        {{ $cliente->saldo_cuenta_corriente > 0 ? 'text-red-600' : 'text-green-600' }}">
                        $ {{ number_format($cliente->saldo_cuenta_corriente, 2) }}
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500">Límite de Crédito</p>
                    <p class="text-2xl font-bold text-gray-800">
                        $ {{ number_format($cliente->limite_credito ?? 0, 2) }}
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500">Disponible</p>
                    <p class="text-2xl font-bold text-blue-600">
                        $ {{ number_format(($cliente->limite_credito ?? 0) - $cliente->saldo_cuenta_corriente, 2) }}
                    </p>
                </div>

            </div>

            {{-- TABLA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Detalle</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Debe</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Haber</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Saldo</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse($movimientos as $mov)

                                <tr class="hover:bg-gray-50 transition">

                                    {{-- FECHA --}}
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- DETALLE --}}
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            {{ $mov->descripcion ?? ucfirst($mov->tipo) }}
                                        </div>

                                        @if($mov->referencia_id)
                                            <div class="text-xs text-gray-400">
                                                Ref: #{{ $mov->referencia_id }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- DEBE --}}
                                    <td class="px-6 py-4 text-right">
                                        @if($mov->esDebe())
                                            <span class="text-red-600 font-semibold">
                                                $ {{ number_format($mov->monto, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>

                                    {{-- HABER --}}
                                    <td class="px-6 py-4 text-right">
                                        @if($mov->esHaber())
                                            <span class="text-green-600 font-semibold">
                                                $ {{ number_format($mov->monto, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>

                                    {{-- SALDO --}}
                                    <td class="px-6 py-4 text-right font-bold">
                                        <span class="{{ $mov->saldo_historico > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            $ {{ number_format($mov->saldo_historico, 2) }}
                                        </span>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center text-gray-400">
                                        No hay movimientos registrados.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
