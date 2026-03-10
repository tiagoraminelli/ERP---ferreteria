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
            <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="https://laravel.com/assets/img/welcome/background.svg" />

            <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-orange-600 selection:text-white">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                        <div class="flex lg:justify-center lg:col-start-2">
                            <div class="text-orange-600 dark:text-orange-500">
                                <i class="fas fa-screwdriver-wrench fa-3x"></i>
                            </div>
                        </div>
                        @if (Route::has('login'))
                            <livewire:welcome.navigation />
                        @endif
                    </header>

                    <main class="mt-6">
                        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">

                            <a
                                href="/inventario"
                                id="docs-card"
                                class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-orange-600 md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700"
                            >
                                <div id="screenshot-container" class="relative flex w-full flex-1 items-stretch">
                                    <img
                                        src="{{ asset('assets/img/dashboard-preview.png') }}"
                                        alt="Vista previa Inventario"
                                        class="aspect-video h-full w-full flex-1 rounded-[10px] object-top object-cover drop-shadow-[0px_4px_34px_rgba(0,0,0,0.06)]"
                                        onerror="this.src='https://images.unsplash.com/photo-1581235720704-06d3acfcb36f?q=80&w=1000&auto=format&fit=crop'"
                                    />
                                    <div class="absolute -bottom-16 -left-16 h-40 w-[calc(100%+8rem)] bg-gradient-to-b from-transparent via-white to-white dark:via-zinc-900 dark:to-zinc-900"></div>
                                </div>

                                <div class="relative flex items-center gap-6 lg:items-end">
                                    <div id="docs-card-content" class="flex items-start gap-6 lg:flex-col">
                                        <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-orange-600/10 sm:size-16">
                                            <i class="fas fa-boxes-stacked text-orange-600 text-xl sm:text-2xl"></i>
                                        </div>

                                        <div class="pt-3 sm:pt-5 lg:pt-0">
                                            <h2 class="text-xl font-semibold text-black dark:text-white">Control de Stock Maestro</h2>
                                            <p class="mt-4 text-sm/relaxed">
                                                Gestión centralizada de miles de artículos. Seguimiento de SKU, alertas de stock crítico, entrada de mercadería y actualización masiva de precios mediante planillas Excel.
                                            </p>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right size-6 shrink-0 text-orange-600"></i>
                                </div>
                            </a>

                            <a
                                href="/ventas"
                                class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-orange-600 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700"
                            >
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-orange-600/10 sm:size-16">
                                    <i class="fas fa-cash-register text-orange-600 text-xl"></i>
                                </div>
                                <div class="pt-3 sm:pt-5">
                                    <h2 class="text-xl font-semibold text-black dark:text-white">Punto de Venta Rápido</h2>
                                    <p class="mt-4 text-sm/relaxed">
                                        Interfaz optimizada para lectura de códigos de barra. Soporte para múltiples medios de pago, emisión de tickets y presupuestos en segundos.
                                    </p>
                                </div>
                                <i class="fas fa-arrow-right size-6 shrink-0 self-center text-orange-600"></i>
                            </a>

                            <a
                                href="/reportes"
                                class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-orange-600 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700"
                            >
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-orange-600/10 sm:size-16">
                                    <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                                </div>
                                <div class="pt-3 sm:pt-5">
                                    <h2 class="text-xl font-semibold text-black dark:text-white">Inteligencia de Negocio</h2>
                                    <p class="mt-4 text-sm/relaxed">
                                        Visualiza márgenes de ganancia, productos más vendidos y rendimiento diario. Toma decisiones basadas en datos reales de tu ferretería.
                                    </p>
                                </div>
                                <i class="fas fa-arrow-right size-6 shrink-0 self-center text-orange-600"></i>
                            </a>

                            <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-orange-600/10 sm:size-16">
                                    <i class="fas fa-users-gear text-orange-600 text-xl"></i>
                                </div>
                                <div class="pt-3 sm:pt-5">
                                    <h2 class="text-xl font-semibold text-black dark:text-white">Módulo CRM & Compras</h2>
                                    <p class="mt-4 text-sm/relaxed">
                                        Base de datos de clientes con cuentas corrientes y gestión de proveedores. Organiza tus pedidos y mantén tus deudas y cobros bajo control.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </main>

                    <footer class="py-16 text-center text-sm text-black dark:text-white/70 font-medium">
                        &copy; 2026 Ferretería Pro-Gest - Sistema Integral v1.0
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>
