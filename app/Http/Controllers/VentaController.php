<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\VentaDetalle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{

    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario']);

        // ================= FILTROS =================

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {
                $q->where('id', $buscar)
                    ->orWhereHas('cliente', function ($q2) use ($buscar) {
                        $q2->where('nombre', 'like', "%{$buscar}%");
                    });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        // ================= ORDEN =================

        $orden = $request->get('orden', 'fecha');
        $direccion = $request->get('direccion', 'desc');

        $query->orderBy($orden, $direccion);

        $ventas = $query->paginate(20)->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS
        |--------------------------------------------------------------------------
        */

        $metricBaseQuery = Venta::query();

        if ($request->filled('desde')) {
            $metricBaseQuery->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $metricBaseQuery->whereDate('fecha', '<=', $request->hasta);
        }

        $totalVentas = $metricBaseQuery->count();

        $ventasCompletadas = (clone $metricBaseQuery)
            ->where('estado', 'completada')
            ->count();

        $ventasPendientes = (clone $metricBaseQuery)
            ->where('estado', 'pendiente')
            ->count();

        $totalFacturado = (clone $metricBaseQuery)->sum('total');

        return view('ventas.index', compact(
            'ventas',
            'totalVentas',
            'ventasCompletadas',
            'ventasPendientes',
            'totalFacturado'
        ));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $usuario = Auth::user();

        return view('ventas.create', compact(
            'clientes',
            'productos',
            'usuario'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,cheque,cuenta_corriente',
            'observaciones' => 'nullable|string|max:500',
            'estado' => 'required|in:completada,pendiente,cancelada',
            'fecha' => 'nullable|date',
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'required_with:productos|exists:productos,id',
            'productos.*.cantidad' => 'required_with:productos|numeric|min:0.001',
            'productos.*.precio' => 'required_with:productos|numeric|min:0',
            'productos.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.descuento_monto' => 'nullable|numeric|min:0',
            'monto_pagado' => 'nullable|numeric|min:0',
        ], [
            'cliente_id.required' => 'Debe seleccionar un cliente',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'metodo_pago.required' => 'Debe seleccionar un método de pago',
            'metodo_pago.in' => 'Método de pago no válido',
            'productos.*.producto_id.required_with' => 'Debe seleccionar un producto',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe',
            'productos.*.cantidad.required_with' => 'Debe ingresar una cantidad',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
            'productos.*.precio.required_with' => 'Debe ingresar un precio',
            'productos.*.precio.min' => 'El precio debe ser mayor a 0',
        ]);

        DB::beginTransaction();

        try {
            // Crear la venta
            $venta = Venta::create([
                'cliente_id' => $validated['cliente_id'],
                'usuario_id' => Auth::id() ?? 1, // Asignar un ID de usuario predeterminado si no hay autenticación, este es el de admin.
                'fecha' => $validated['fecha'] ?? now()->toDateString(),
                'estado' => $validated['estado'] ?? 'pendiente',
                'metodo_pago' => $validated['metodo_pago'],
                'notas' => $validated['observaciones'] ?? null,
                'total' => 0,
                'monto_pagado' => $validated['monto_pagado'] ?? 0,
                'saldo_pendiente' => 0
            ]);

            $total = 0;

            // Procesar productos si existen
            if (!empty($validated['productos'])) {
                foreach ($validated['productos'] as $item) {
                    $cantidad = floatval($item['cantidad']);
                    $precio = floatval($item['precio']);
                    $descuento_porcentaje = floatval($item['descuento_porcentaje'] ?? 0);
                    $descuento_monto = floatval($item['descuento_monto'] ?? 0);

                    // Calcular subtotal con descuentos
                    $subtotal_sin_descuento = $cantidad * $precio;
                    $subtotal = $subtotal_sin_descuento;

                    if ($descuento_porcentaje > 0) {
                        $subtotal = $subtotal_sin_descuento * (1 - $descuento_porcentaje / 100);
                    }

                    if ($descuento_monto > 0) {
                        $subtotal = $subtotal_sin_descuento - $descuento_monto;
                    }

                    // Obtener precio costo del producto
                    $producto = Producto::find($item['producto_id']);

                    VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $cantidad,
                        'precio' => $precio,
                        'precio_costo' => $producto ? $producto->precio_costo : 0,
                        'descuento_porcentaje' => $descuento_porcentaje,
                        'descuento_monto' => $descuento_monto,
                        'subtotal' => max(0, $subtotal),
                        'subtotal_sin_descuento' => $subtotal_sin_descuento
                    ]);

                    $total += $subtotal;

                    // // Actualizar stock del producto si controla stock
                    // if ($producto && $producto->controla_stock) {
                    //     $producto->decrement('stock', $cantidad);
                    // }
                }
            }

            // Calcular saldo pendiente
            $montoPagado = floatval($validated['monto_pagado'] ?? 0);
            $saldoPendiente = max(0, $total - $montoPagado);

            $venta->update([
                'total' => $total,
                'saldo_pendiente' => $saldoPendiente
            ]);

            DB::commit();

            return redirect()
                ->route('ventas.index')
                ->with('success', '¡Venta creada correctamente! N°: ' . $venta->id);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Error al crear la venta: ' . $e->getMessage())
                ->withInput();
        }
    }




    public function show(Venta $venta)
    {
        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        // Cargar relaciones necesarias
        $venta->load('detalles.producto');

        $clientes = Cliente::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $usuario = Auth::user();

        return view('ventas.edit', compact(
            'venta',
            'clientes',
            'productos',
            'usuario'
        ));
    }

    public function update(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,cheque,cuenta_corriente',
            'observaciones' => 'nullable|string|max:500',
            'estado' => 'required|in:completada,pendiente,cancelada',
            'fecha' => 'nullable|date',
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'required_with:productos|exists:productos,id',
            'productos.*.cantidad' => 'required_with:productos|numeric|min:0.001',
            'productos.*.precio' => 'required_with:productos|numeric|min:0',
            'productos.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.descuento_monto' => 'nullable|numeric|min:0',
            'monto_pagado' => 'nullable|numeric|min:0',
        ], [
            'cliente_id.required' => 'Debe seleccionar un cliente',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'metodo_pago.required' => 'Debe seleccionar un método de pago',
            'metodo_pago.in' => 'Método de pago no válido',

            'productos.*.producto_id.required_with' => 'Debe seleccionar un producto',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe',
            'productos.*.cantidad.required_with' => 'Debe ingresar una cantidad',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
            'productos.*.precio.required_with' => 'Debe ingresar un precio',
            'productos.*.precio.min' => 'El precio debe ser mayor a 0',
        ]);

        // dd($validated);

        DB::beginTransaction();

        try {
            // Restaurar stock de productos anteriores (si controlan stock)
            // foreach ($venta->detalles as $detalle) {
            //     $producto = Producto::find($detalle->producto_id);
            //     if ($producto && $producto->controla_stock) {
            //         $producto->increment('stock', $detalle->cantidad);
            //     }
            // }

            // Eliminar detalles anteriores
            VentaDetalle::where('venta_id', $venta->id)->delete();

            // Actualizar datos de la venta
            $venta->update([
                'cliente_id' => $validated['cliente_id'],
                'metodo_pago' => $validated['metodo_pago'],
                'estado' => $validated['estado'],
                'fecha' => $validated['fecha'],
                'notas' => $validated['observaciones'] ?? null,
                'total' => 0,
                'monto_pagado' => $validated['monto_pagado'] ?? 0,
                'saldo_pendiente' => 0
            ]);

            $total = 0;

            // Procesar productos si existen
            if (!empty($validated['productos'])) {
                foreach ($validated['productos'] as $item) {
                    $cantidad = floatval($item['cantidad']);
                    $precio = floatval($item['precio']);
                    $descuento_porcentaje = floatval($item['descuento_porcentaje'] ?? 0);
                    $descuento_monto = floatval($item['descuento_monto'] ?? 0);

                    // Calcular subtotal con descuentos
                    $subtotal_sin_descuento = $cantidad * $precio;
                    $subtotal = $subtotal_sin_descuento;

                    if ($descuento_porcentaje > 0) {
                        $subtotal = $subtotal_sin_descuento * (1 - $descuento_porcentaje / 100);
                    }

                    if ($descuento_monto > 0) {
                        $subtotal = $subtotal_sin_descuento - $descuento_monto;
                    }

                    // Obtener precio costo del producto
                    $producto = Producto::find($item['producto_id']);

                    VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $cantidad,
                        'precio' => $precio,
                        'precio_costo' => $producto ? $producto->precio_costo : 0,
                        'descuento_porcentaje' => $descuento_porcentaje,
                        'descuento_monto' => $descuento_monto,
                        'subtotal' => max(0, $subtotal),
                        'subtotal_sin_descuento' => $subtotal_sin_descuento
                    ]);

                    $total += $subtotal;

                    // Actualizar stock del producto (restar el nuevo stock)
                    // if ($producto && $producto->controla_stock) {
                    //     $producto->decrement('stock', $cantidad);
                    // }
                }
            }

            // Calcular saldo pendiente
            $montoPagado = floatval($validated['monto_pagado'] ?? 0);
            $saldoPendiente = max(0, $total - $montoPagado);

            $venta->update([
                'total' => $total,
                'saldo_pendiente' => $saldoPendiente
            ]);

            DB::commit();

            return redirect()
                ->route('ventas.index')
                ->with('success', '¡Venta actualizada correctamente! N°: ' . $venta->id);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Error al actualizar la venta: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Venta $venta)
    {
        //
    }
}
