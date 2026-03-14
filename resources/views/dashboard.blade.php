<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-900">
                    Dashboard
                </h2>
                <p class="text-sm text-black-500 dark:text-gray-400">
                    Panel general del sistema
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">

            {{-- CARD BIENVENIDA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">
                    Bienvenido
                </h3>

                <p class="text-sm text-gray-600">
                    Bienvenido al sistema ERP de <span class="font-semibold">Ferreterías</span>.
                    Desde aquí podrás gestionar productos, ventas, reportes y operaciones del negocio.
                </p>
            </div>


            {{-- MÉTRICAS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Productos</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">0</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Ventas Hoy</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">$0</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Stock Bajo</p>
                    <p class="text-2xl font-bold text-red-600 mt-2">0</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Clientes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">0</p>
                </div>

            </div>


            {{-- ACTIVIDAD RECIENTE --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-700 mb-4">
                    Actividad Reciente
                </h3>

                <p class="text-sm text-gray-500">
                    Aquí aparecerán las últimas ventas, movimientos de stock y cambios importantes del sistema.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
