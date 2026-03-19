<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ferretería Pro-Gest - ERP</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans">

    <div class="bg-gray-50 dark:bg-black text-black/70 dark:text-white/70 min-h-screen relative">

        <!-- Background -->
        <img class="hidden lg:block absolute -left-20 top-0 max-w-[800px] opacity-30"
            src="https://laravel.com/assets/img/welcome/background.svg" />

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <header class="flex flex-col sm:flex-row items-center justify-between py-8 gap-4">

                <div class="flex items-center gap-3">
                    <i class="fas fa-screwdriver-wrench text-3xl sm:text-4xl text-orange-600"></i>
                    <span class="text-lg sm:text-xl font-semibold text-black dark:text-white">
                        Pro-Gest ERP
                    </span>
                </div>

                @if (Route::has('login'))
                    <livewire:welcome.navigation />
                @endif

            </header>

            <!-- MAIN -->
            <main class="pb-10">

                <!-- GRID: 2 COLUMNAS EN ESCRITORIO, 1 EN MÓVIL -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- INVENTARIO / PRODUCTOS -->
                    <a href="javascript:void(0)"
                       data-destination="/productos"
                       onclick="handleNavigation(event, this.dataset.destination)"
                       class="flex flex-col bg-white dark:bg-zinc-900 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition h-full group cursor-pointer">

                        <div class="relative overflow-hidden">
                            <img src="{{ asset('assets/img/dashboard-preview.png') }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition duration-300"
                                 onerror="this.src='https://images.unsplash.com/photo-1581235720704-06d3acfcb36f?q=80&w=1000&auto=format&fit=crop'">
                        </div>

                        <div class="p-6 flex gap-4">
                            <div class="w-14 h-14 flex items-center justify-center rounded-full bg-orange-600/10 shrink-0">
                                <i class="fas fa-boxes-stacked text-orange-600 text-xl"></i>
                            </div>

                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white">
                                    Control de Stock Maestro
                                </h2>

                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Gestión de artículos, SKU, stock crítico y actualización masiva.
                                </p>
                            </div>
                        </div>
                    </a>

                    <!-- VENTAS / PUNTO DE VENTA -->
                    <a href="javascript:void(0)"
                       data-destination="/ventas"
                       onclick="handleNavigation(event, this.dataset.destination)"
                       class="flex flex-col bg-white dark:bg-zinc-900 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition h-full group cursor-pointer">

                        <div class="relative overflow-hidden">
                            <img src="{{ asset('assets/img/dashboard-preview.png') }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition duration-300"
                                 onerror="this.src='https://images.unsplash.com/photo-1556742044-3c52d6e88c62?q=80&w=1000&auto=format&fit=crop'">
                        </div>

                        <div class="p-6 flex gap-4">
                            <div class="w-14 h-14 flex items-center justify-center rounded-full bg-orange-600/10 shrink-0">
                                <i class="fas fa-cash-register text-orange-600 text-xl"></i>
                            </div>

                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white">
                                    Punto de Venta
                                </h2>

                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Ventas rápidas, códigos de barra y tickets automáticos.
                                </p>
                            </div>
                        </div>
                    </a>

                    <!-- REPORTES / DASHBOARD -->
                    <a href="javascript:void(0)"
                       data-destination="/dashboard"
                       onclick="handleNavigation(event, this.dataset.destination)"
                       class="flex flex-col bg-white dark:bg-zinc-900 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition h-full group cursor-pointer">

                        <div class="relative overflow-hidden">
                            <img src="{{ asset('assets/img/dashboard-preview.png') }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition duration-300"
                                 onerror="this.src='https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1000&auto=format&fit=crop'">
                        </div>

                        <div class="p-6 flex gap-4">
                            <div class="w-14 h-14 flex items-center justify-center rounded-full bg-orange-600/10 shrink-0">
                                <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                            </div>

                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white">
                                    Reportes & Dashboard
                                </h2>

                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Métricas, productos más vendidos y rendimiento diario.
                                </p>
                            </div>
                        </div>
                    </a>

                    <!-- CRM / CLIENTES -->
                    <a href="javascript:void(0)"
                       data-destination="/clientes"
                       onclick="handleNavigation(event, this.dataset.destination)"
                       class="flex flex-col bg-white dark:bg-zinc-900 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition h-full group cursor-pointer">

                        <div class="relative overflow-hidden">
                            <img src="{{ asset('assets/img/dashboard-preview.png') }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition duration-300"
                                 onerror="this.src='https://images.unsplash.com/photo-1556742044-3c52d6e88c62?q=80&w=1000&auto=format&fit=crop'">
                        </div>

                        <div class="p-6 flex gap-4">
                            <div class="w-14 h-14 flex items-center justify-center rounded-full bg-orange-600/10 shrink-0">
                                <i class="fas fa-users-gear text-orange-600 text-xl"></i>
                            </div>

                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white">
                                    CRM & Clientes
                                </h2>

                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Gestión de clientes, proveedores y cuentas corrientes.
                                </p>
                            </div>
                        </div>
                    </a>

                </div>

            </main>

            <!-- FOOTER -->
            <footer class="py-8 sm:py-10 text-center text-xs sm:text-sm text-black dark:text-white/60 border-t border-gray-200 dark:border-zinc-800">
                &copy; 2026 Ferretería Pro-Gest - Sistema Integral
            </footer>

        </div>
    </div>

    <script>
        function handleNavigation(event, destination) {
            event.preventDefault();

            @auth
                window.location.href = destination;
            @else
                window.location.href = '/login?redirect=' + encodeURIComponent(destination);
            @endauth
        }
    </script>

</body>

</html>
