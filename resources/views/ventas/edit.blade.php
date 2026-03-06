<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Editar Venta #{{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="text-sm text-white-500 dark:text-gray-400">
                    Modificar venta y productos
                </p>
            </div>

            <a href="{{ route('ventas.index') }}"
                class="px-4 py-2 bg-white/10 text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-white/20 transition">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8" style="background-color:#f3f4f6;">
        <div class="max-w-7xl mx-auto px-6">

            @if ($errors->any())
                <div
                    style="background-color:#fee;border:1px solid #fcc;color:#c00;padding:15px;border-radius:8px;margin-bottom:20px">
                    <ul style="margin:0;padding-left:20px">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div
                    style="background-color:#fee;border:1px solid #fcc;color:#c00;padding:15px;border-radius:8px;margin-bottom:20px">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('ventas.update', $venta) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px">

                    {{-- COLUMNA PRODUCTOS --}}
                    <div style="background:white;padding:20px;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.1)">

                        <h3 style="margin-bottom:15px;font-size:16px;font-weight:600">
                            Productos
                        </h3>

                        {{-- SELECT2 con datos locales --}}
                        <select id="select2Productos" style="width:100%">
                            <option></option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"
                                    data-precio="{{ $producto->precio_venta }}"
                                    data-codigo="{{ $producto->codigo_barra }}">
                                    {{ $producto->nombre }} - ${{ number_format($producto->precio_venta, 2) }}
                                </option>
                            @endforeach
                        </select>

                        <div id="listadoProductos" style="max-height:400px;overflow-y:auto;margin-top:15px">
                            @foreach ($productos as $producto)
                                <button type="button"
                                    onclick="agregarProducto(
'{{ $producto->id }}',
'{{ addslashes($producto->nombre) }}',
'{{ $producto->precio_venta }}'
)"
                                    style="display:block;width:100%;text-align:left;padding:10px;border:1px solid #eee;margin:2px 0;border-radius:4px;background:white;cursor:pointer;transition:background 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f9f9f9'"
                                    onmouseout="this.style.backgroundColor='white'">
                                    <div style="display:flex;justify-content:space-between">
                                        <div>
                                            <strong>{{ $producto->nombre }}</strong>
                                            @if ($producto->codigo_barra)
                                                <div style="font-size:12px;color:#666">
                                                    Cód: {{ $producto->codigo_barra }}
                                                </div>
                                            @endif
                                        </div>
                                        <div style="font-weight:bold;color:#059669">
                                            $ {{ number_format($producto->precio_venta, 2) }}
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- COLUMNA VENTA --}}
                    <div>

                        {{-- DATOS --}}
                        <div
                            style="background:white;padding:20px;border-radius:8px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.1)">

                            <h3 style="margin-bottom:15px;font-size:16px;font-weight:600">
                                Información de la Venta
                            </h3>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">

                                {{-- Cliente con Select2 --}}
                                <div>
                                    <label style="display:block;margin-bottom:5px;font-weight:500;font-size:14px">
                                        Cliente *
                                    </label>

                                    <select name="cliente_id" id="select2Clientes" style="width:100%">
                                        <option value="">Seleccionar cliente</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}"
                                                {{ ($venta->cliente_id ?? old('cliente_id')) == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Método pago --}}
                                <div>
                                    <label style="display:block;margin-bottom:5px;font-weight:500;font-size:14px">
                                        Método Pago *
                                    </label>

                                    <select name="metodo_pago" id="metodo_pago"
                                        style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;background:white">
                                        <option value="efectivo"
                                            {{ ($venta->metodo_pago ?? old('metodo_pago')) == 'efectivo' ? 'selected' : '' }}>
                                            Efectivo</option>
                                        <option value="tarjeta"
                                            {{ ($venta->metodo_pago ?? old('metodo_pago')) == 'tarjeta' ? 'selected' : '' }}>
                                            Tarjeta</option>
                                        <option value="transferencia"
                                            {{ ($venta->metodo_pago ?? old('metodo_pago')) == 'transferencia' ? 'selected' : '' }}>
                                            Transferencia</option>
                                        <option value="cuenta_corriente"
                                            {{ ($venta->metodo_pago ?? old('metodo_pago')) == 'cuenta_corriente' ? 'selected' : '' }}>
                                            Cuenta Corriente</option>
                                    </select>
                                </div>

                                {{-- Estado --}}
                                <div>
                                    <label style="display:block;margin-bottom:5px;font-weight:500;font-size:14px">
                                        Estado *
                                    </label>

                                    <select name="estado" id="estado"
                                        style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;background:white">
                                        <option value="pendiente"
                                            {{ ($venta->estado ?? old('estado')) == 'pendiente' ? 'selected' : '' }}>
                                            Pendiente</option>
                                        <option value="completada"
                                            {{ ($venta->estado ?? old('estado')) == 'completada' ? 'selected' : '' }}>
                                            Completada</option>
                                        <option value="cancelada"
                                            {{ ($venta->estado ?? old('estado')) == 'cancelada' ? 'selected' : '' }}>
                                            Cancelada</option>
                                    </select>
                                </div>

                                {{-- Fecha --}}
                                <div>
                                    <label style="display:block;margin-bottom:5px;font-weight:500;font-size:14px">
                                        Fecha *
                                    </label>
                                    <input type="datetime-local" name="fecha" id="fecha"
                                        value="{{ $venta->fecha ? \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d\TH:i') : old('fecha') }}"
                                        style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box">
                                </div>

                                {{-- Observaciones --}}
                                <div style="grid-column:span 2">
                                    <label style="display:block;margin-bottom:5px;font-weight:500;font-size:14px">
                                        Notas u Observaciones
                                    </label>
                                    <textarea name="observaciones" rows="2"
                                        style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box">{{ $venta->notas ?? old('observaciones') }}</textarea>
                                </div>

                            </div>
                        </div>

                        {{-- TABLA --}}
                        <div
                            style="background:white;padding:20px;border-radius:8px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.1)">

                            <div style="display:flex;justify-content:space-between;margin-bottom:15px">
                                <h3 style="margin:0;font-size:16px;font-weight:600">
                                    Detalle de Productos
                                </h3>
                                <span id="contadorProductos"
                                    style="background:#f0f0f0;padding:4px 12px;border-radius:20px;font-weight:500">
                                    {{ $venta->detalles->count() }} items
                                </span>
                            </div>

                            <div style="overflow-x:auto">
                                <table style="width:100%;border-collapse:collapse;font-size:14px">
                                    <thead>
                                        <tr style="background:#f9f9f9">
                                            <th style="text-align:left;padding:10px">Producto</th>
                                            <th style="text-align:center;padding:10px">Cant</th>
                                            <th style="text-align:center;padding:10px">Precio</th>
                                            <th style="text-align:center;padding:10px">%Desc</th>
                                            <th style="text-align:center;padding:10px">$Desc</th>
                                            <th style="text-align:right;padding:10px">Subtotal</th>
                                            <th style="padding:10px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProductos">
                                        @foreach ($venta->detalles as $index => $detalle)
                                            <tr>
                                                <td style="padding:10px">
                                                    {{ $detalle->producto->nombre }}
                                                    <input type="hidden"
                                                        name="productos[{{ $index }}][producto_id]"
                                                        value="{{ $detalle->producto_id }}">
                                                </td>
                                                <td style="text-align:center">
                                                    <input type="number" step="0.001"
                                                        name="productos[{{ $index }}][cantidad]"
                                                        value="{{ $detalle->cantidad }}" oninput="calcular()"
                                                        style="width:70px;padding:5px;border:1px solid #ddd;border-radius:4px">
                                                </td>
                                                <td style="text-align:center">
                                                    <input type="number" step="0.01"
                                                        name="productos[{{ $index }}][precio]"
                                                        value="{{ $detalle->precio }}" oninput="calcular()"
                                                        style="width:80px;padding:5px;border:1px solid #ddd;border-radius:4px">
                                                </td>
                                                <td style="text-align:center">
                                                    <input type="number" step="0.01"
                                                        name="productos[{{ $index }}][descuento_porcentaje]"
                                                        value="{{ $detalle->descuento_porcentaje }}"
                                                        oninput="calcular()"
                                                        style="width:60px;padding:5px;border:1px solid #ddd;border-radius:4px">
                                                </td>
                                                <td style="text-align:center">
                                                    <input type="number" step="0.01"
                                                        name="productos[{{ $index }}][descuento_monto]"
                                                        value="{{ $detalle->descuento_monto }}" oninput="calcular()"
                                                        style="width:60px;padding:5px;border:1px solid #ddd;border-radius:4px">
                                                </td>
                                                <td class="subtotal"
                                                    style="text-align:right;padding:10px;font-weight:500">
                                                    {{ number_format($detalle->subtotal, 2) }}</td>
                                                <td style="padding:10px">
                                                    <button type="button"
                                                        onclick="this.closest('tr').remove();calcular();actualizarContador()"
                                                        style="border:none;background:none;cursor:pointer;color:#999;font-size:16px">✕</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        {{-- TOTAL --}}
                        <div
                            style="background:white;padding:20px;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.1)">

                            <div
                                style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                                <div>
                                    <label style="display:block;margin-bottom:5px;font-weight:500;font-size:14px">
                                        Monto Pagado
                                    </label>
                                    <input type="number" step="0.01" id="monto_pagado" name="monto_pagado"
                                        value="{{ $venta->monto_pagado ?? old('monto_pagado', 0) }}"
                                        oninput="calcularSaldoPendiente()"
                                        style="padding:8px;border:1px solid #ddd;border-radius:4px;width:150px">
                                </div>

                                <div style="text-align:right">
                                    <div style="font-size:14px;color:#666;margin-bottom:5px">
                                        Total
                                    </div>
                                    <div id="totalVenta" style="font-size:24px;font-weight:bold;color:#059669">
                                        $ {{ number_format($venta->total, 2) }}
                                    </div>
                                    <div id="saldoPendiente"
                                        style="{{ $venta->total - $venta->monto_pagado > 0 ? 'display:block;' : 'display:none;' }}color:#c00;font-size:14px;margin-top:5px">
                                        Saldo ${{ number_format($venta->total - $venta->monto_pagado, 2) }}
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                style="width:100%;padding:12px;background:#1f2937;color:white;border:none;border-radius:6px;font-weight:600;cursor:pointer;transition:background 0.2s"
                                onmouseover="this.style.backgroundColor='#111827'"
                                onmouseout="this.style.backgroundColor='#1f2937'">
                                Actualizar Venta
                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- jQuery y Select2 JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let indexProducto = {{ $venta->detalles->count() }}

        function agregarProducto(id, nombre, precio) {
            // Validar que el producto no esté duplicado
            let productosExistentes = document.querySelectorAll('#tablaProductos tr');
            for (let row of productosExistentes) {
                let input = row.querySelector('input[name*="[producto_id]"]');
                if (input && input.value === id.toString()) {
                    alert('Este producto ya está en la lista');
                    return;
                }
            }

            let tabla = document.getElementById("tablaProductos")
            let row = `
                <tr>
                    <td style="padding:10px">
                        ${nombre}
                        <input type="hidden" name="productos[${indexProducto}][producto_id]" value="${id}">
                    </td>
                    <td style="text-align:center">
                        <input type="number" step="0.001" name="productos[${indexProducto}][cantidad]"
                            value="1"
                            oninput="calcular()"
                            style="width:70px;padding:5px;border:1px solid #ddd;border-radius:4px">
                    </td>
                    <td style="text-align:center">
                        <input type="number" step="0.01" name="productos[${indexProducto}][precio]"
                            value="${precio}"
                            oninput="calcular()"
                            style="width:80px;padding:5px;border:1px solid #ddd;border-radius:4px">
                    </td>
                    <td style="text-align:center">
                        <input type="number" step="0.01" name="productos[${indexProducto}][descuento_porcentaje]"
                            value="0"
                            oninput="calcular()"
                            style="width:60px;padding:5px;border:1px solid #ddd;border-radius:4px">
                    </td>
                    <td style="text-align:center">
                        <input type="number" step="0.01" name="productos[${indexProducto}][descuento_monto]"
                            value="0"
                            oninput="calcular()"
                            style="width:60px;padding:5px;border:1px solid #ddd;border-radius:4px">
                    </td>
                    <td class="subtotal" style="text-align:right;padding:10px;font-weight:500">0.00</td>
                    <td style="padding:10px">
                        <button type="button" onclick="this.closest('tr').remove();calcular();actualizarContador()" style="border:none;background:none;cursor:pointer;color:#999;font-size:16px">✕</button>
                    </td>
                </tr>
            `
            tabla.insertAdjacentHTML("beforeend", row)
            indexProducto++
            calcular()
            actualizarContador()
        }

        function calcular() {
            let total = 0
            document.querySelectorAll("#tablaProductos tr").forEach(row => {
                let cant = parseFloat(row.querySelector('[name*="[cantidad]"]').value) || 0
                let precio = parseFloat(row.querySelector('[name*="[precio]"]').value) || 0
                let porc = parseFloat(row.querySelector('[name*="[descuento_porcentaje]"]').value) || 0
                let monto = parseFloat(row.querySelector('[name*="[descuento_monto]"]').value) || 0

                let subtotal = cant * precio
                if (porc > 0) subtotal = subtotal - (subtotal * porc / 100)
                if (monto > 0) subtotal = subtotal - monto
                subtotal = Math.max(0, subtotal)

                row.querySelector(".subtotal").innerText = subtotal.toFixed(2)
                total += subtotal
            })

            document.getElementById("totalVenta").innerText = "$ " + total.toFixed(2)
            calcularSaldoPendiente()
        }

        function calcularSaldoPendiente() {
            let total = parseFloat(document.getElementById("totalVenta").innerText.replace('$ ', '')) || 0
            let pagado = parseFloat(document.getElementById("monto_pagado").value) || 0
            let saldo = total - pagado
            let el = document.getElementById("saldoPendiente")

            if (saldo > 0) {
                el.style.display = "block"
                el.innerText = "Saldo $" + saldo.toFixed(2)
            } else {
                el.style.display = "none"
            }
        }

        function actualizarContador() {
            let rows = document.querySelectorAll("#tablaProductos tr").length
            document.getElementById('contadorProductos').innerText = rows + ' items'
        }

        $(document).ready(function() {
            // Select2 para Productos - BÚSQUEDA LOCAL
            $('#select2Productos').select2({
                placeholder: 'Buscar producto...',
                allowClear: true,
                dropdownParent: $('#select2Productos').parent()
            });

            // Select2 para Clientes - BÚSQUEDA LOCAL
            $('#select2Clientes').select2({
                placeholder: 'Buscar cliente...',
                allowClear: true,
                dropdownParent: $('#select2Clientes').parent()
            });

            // Evento al seleccionar producto
            $('#select2Productos').on('select2:select', function(e) {
                var data = e.params.data;
                var id = data.id;
                var nombre = $(this).find('option:selected').data('nombre');
                var precio = $(this).find('option:selected').data('precio');

                agregarProducto(id, nombre, precio);
                $(this).val(null).trigger('change');
            });

            // Inicializar cálculos
            actualizarContador();
            calcular();
        });
    </script>

</x-app-layout>
