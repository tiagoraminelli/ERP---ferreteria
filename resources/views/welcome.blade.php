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

    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">

        <!-- Background decorativo -->
        <img id="background" class="hidden lg:block absolute -left-20 top-0 max-w-[877px] opacity-40"
            src="https://laravel.com/assets/img/welcome/background.svg" />

        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-orange-600 selection:text-white px-4">

            <div class="relative w-full max-w-7xl">

                <!-- HEADER -->
                <header class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 items-center gap-4 py-10">

                    <div class="flex justify-center sm:justify-start lg:justify-center lg:col-start-2">
                        <div class="text-orange-600 dark:text-orange-500">
                            <i class="fas fa-screwdriver-wrench text-4xl sm:text-5xl"></i>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        <livewire:welcome.navigation />
                    @endif

                </header>

                <!-- MAIN -->
                <main class="mt-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        <!-- INVENTARIO -->
                        <a href="/inventario"
                            class="flex flex-col gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-lg transition hover:scale-[1.02] hover:ring-2 hover:ring-orange-500 dark:bg-zinc-900">

                            <div class="relative w-full">
                                <img src="{{ asset('assets/img/dashboard-preview.png') }}" alt="Inventario"
                                    class="w-full h-48 sm:h-56 md:h-64 object-cover rounded-lg"
                                    onerror="this.src='https://images.unsplash.com/photo-1581235720704-06d3acfcb36f?q=80&w=1000&auto=format&fit=crop'">
                            </div>

                            <div class="flex items-start gap-4">

                                <div
                                    class="flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-orange-600/10">
                                    <i class="fas fa-boxes-stacked text-orange-600 text-lg sm:text-xl"></i>
                                </div>

                                <div>
                                    <h2 class="text-lg sm:text-xl font-semibold text-black dark:text-white">
                                        Control de Stock Maestro
                                    </h2>

                                    <p class="mt-2 text-sm leading-relaxed">
                                        Gestión centralizada de artículos, control de SKU, alertas de stock crítico
                                        y actualización masiva de precios mediante Excel.
                                    </p>
                                </div>

                            </div>

                        </a>


                        <!-- VENTAS -->
                        <a href="/ventas"
                            class="flex gap-4 rounded-lg bg-white p-6 shadow-lg transition hover:scale-[1.02] hover:ring-2 hover:ring-orange-500 dark:bg-zinc-900">

                            <div
                                class="flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-orange-600/10">
                                <i class="fas fa-cash-register text-orange-600 text-lg"></i>
                            </div>

                            <div>
                                <h2 class="text-lg sm:text-xl font-semibold text-black dark:text-white">
                                    Punto de Venta Rápido
                                </h2>

                                <p class="mt-2 text-sm leading-relaxed">
                                    Lectura de códigos de barra, múltiples medios de pago,
                                    tickets y presupuestos instantáneos.
                                </p>
                            </div>

                        </a>


                        <!-- REPORTES -->
                        <a href="/reportes"
                            class="flex gap-4 rounded-lg bg-white p-6 shadow-lg transition hover:scale-[1.02] hover:ring-2 hover:ring-orange-500 dark:bg-zinc-900">

                            <div
                                class="flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-orange-600/10">
                                <i class="fas fa-chart-line text-orange-600 text-lg"></i>
                            </div>

                            <div>
                                <h2 class="text-lg sm:text-xl font-semibold text-black dark:text-white">
                                    Inteligencia de Negocio
                                </h2>

                                <p class="mt-2 text-sm leading-relaxed">
                                    Visualiza márgenes, productos más vendidos y rendimiento
                                    diario del negocio.
                                </p>
                            </div>

                        </a>


                        <!-- CRM -->
                        <div class="flex gap-4 rounded-lg bg-white p-6 shadow-lg dark:bg-zinc-900">

                            <div
                                class="flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-orange-600/10">
                                <i class="fas fa-users-gear text-orange-600 text-lg"></i>
                            </div>

                            <div>
                                <h2 class="text-lg sm:text-xl font-semibold text-black dark:text-white">
                                    CRM & Compras
                                </h2>

                                <p class="mt-2 text-sm leading-relaxed">
                                    Gestión de clientes, cuentas corrientes,
                                    proveedores y control de pedidos.
                                </p>
                            </div>

                        </div>

                    </div>

                </main>

                <!-- FOOTER -->
                <footer class="py-12 text-center text-sm text-black dark:text-white/70 font-medium">
                    &copy; 2026 Ferretería Pro-Gest - Sistema Integral v1.0
                </footer>

            </div>
        </div>
    </div>

</body>

</html>
