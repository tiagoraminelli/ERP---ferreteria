<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presupuesto;
use App\Models\PresupuestoDetalle;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PresupuestoController extends Controller
{

    public function index(Request $request)
    {
        $query = Presupuesto::with(['cliente', 'usuario']);

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

        $presupuestos = $query->paginate(10)->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS
        |--------------------------------------------------------------------------
        */

        $metricBaseQuery = Presupuesto::query();

        if ($request->filled('desde')) {
            $metricBaseQuery->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $metricBaseQuery->whereDate('fecha', '<=', $request->hasta);
        }

        $totalPresupuestos = $metricBaseQuery->count();

        $presupuestosBorrador = (clone $metricBaseQuery)
            ->where('estado', 'borrador')
            ->count();

        $presupuestosEnviados = (clone $metricBaseQuery)
            ->where('estado', 'enviado')
            ->count();

        $presupuestosAprobados = (clone $metricBaseQuery)
            ->where('estado', 'aprobado')
            ->count();

        $presupuestosRechazados = (clone $metricBaseQuery)
            ->where('estado', 'rechazado')
            ->count();

        $presupuestosConvertidos = (clone $metricBaseQuery)
            ->where('estado', 'convertido')
            ->count();

        $totalPresupuestado = (clone $metricBaseQuery)->sum('total');

        // return a la vist

        return view('presupuestos.index', compact(
            'presupuestos',
            'totalPresupuestos',
            'presupuestosBorrador',
            'presupuestosEnviados',
            'presupuestosAprobados',
            'presupuestosRechazados',
            'presupuestosConvertidos',
            'totalPresupuestado'
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

        return view('presupuestos.create', compact(
            'clientes',
            'productos',
            'usuario'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'nullable',
            'observaciones' => 'nullable|string|max:500',
            'estado' => 'required|in:borrador,enviado,aprobado,rechazado',
            'fecha' => 'nullable|date',
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'required_with:productos|exists:productos,id',
            'productos.*.cantidad' => 'required_with:productos|numeric|min:0.001',
            'productos.*.precio' => 'required_with:productos|numeric|min:0',
            'productos.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.descuento_monto' => 'nullable|numeric|min:0',
        ], [
            'productos.*.producto_id.required_with' => 'Debe seleccionar un producto',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe',
            'productos.*.cantidad.required_with' => 'Debe ingresar una cantidad',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
            'productos.*.precio.required_with' => 'Debe ingresar un precio',
            'productos.*.precio.min' => 'El precio debe ser mayor a 0',
        ]);

        DB::beginTransaction();


        try {

            $clienteIdFinal = $this->resolverCliente($request->input('cliente_id'));


            // Crear el presupuesto
            $presupuesto = Presupuesto::create([
                'cliente_id' => $clienteIdFinal ?? null,
                'usuario_id' => Auth::id() ?? 1,
                'fecha' => $validated['fecha'] ?? now()->toDateString(),
                'estado' => $validated['estado'],
                'notas' => $validated['observaciones'] ?? null,
                'total' => 0,
                'subtotal' => 0,
                'descuento_porcentaje' => 0,
                'descuento_monto' => 0,
            ]);

            $subtotal = 0;

            // Procesar productos
            if (!empty($validated['productos'])) {
                foreach ($validated['productos'] as $item) {

                    $cantidad = floatval($item['cantidad']);
                    $precio = floatval($item['precio']);
                    $descuento_porcentaje = floatval($item['descuento_porcentaje'] ?? 0);
                    $descuento_monto = floatval($item['descuento_monto'] ?? 0);

                    $itemSubtotal = $cantidad * $precio;

                    if ($descuento_porcentaje > 0) {
                        $itemSubtotal = $itemSubtotal * (1 - $descuento_porcentaje / 100);
                    }

                    if ($descuento_monto > 0) {
                        $itemSubtotal = $itemSubtotal - $descuento_monto;
                    }

                    PresupuestoDetalle::create([
                        'presupuesto_id' => $presupuesto->id,
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $cantidad,
                        'precio' => $precio,
                        'descuento_porcentaje' => $descuento_porcentaje,
                        'descuento_monto' => $descuento_monto,
                        'subtotal' => max(0, $itemSubtotal),
                    ]);

                    $subtotal += $itemSubtotal;
                }
            }

            $presupuesto->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            DB::commit();

            return redirect()
                ->route('presupuestos.show', $presupuesto)
                ->with('success', '¡Presupuesto creado correctamente! N°: ' . $presupuesto->id);
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', 'Error al crear el presupuesto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Presupuesto $presupuesto)
    {
        return view('presupuestos.show', compact('presupuesto'));
    }

    public function edit(Presupuesto $presupuesto)
    {
        // Solo se pueden editar presupuestos en estado borrador
        if ($presupuesto->estado !== 'borrador') {
            return redirect()
                ->route('presupuestos.show', $presupuesto)
                ->with('error', 'Solo se pueden editar presupuestos en estado borrador');
        }

        $presupuesto->load('detalles.producto');

        $clientes = Cliente::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $usuario = Auth::user();

        return view('presupuestos.edit', compact(
            'presupuesto',
            'clientes',
            'productos',
            'usuario'
        ));
    }

    public function update(Request $request, Presupuesto $presupuesto)
    {
        // Solo se pueden editar presupuestos en estado borrador
        if ($presupuesto->estado !== 'borrador') {
            return redirect()
                ->route('presupuestos.show', $presupuesto)
                ->with('error', 'Solo se pueden editar presupuestos en estado borrador');
        }

        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'observaciones' => 'nullable|string|max:500',
            'estado' => 'required|in:borrador,enviado,aprobado,rechazado',
            'fecha' => 'nullable|date',
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'required_with:productos|exists:productos,id',
            'productos.*.cantidad' => 'required_with:productos|numeric|min:0.001',
            'productos.*.precio' => 'required_with:productos|numeric|min:0',
            'productos.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.descuento_monto' => 'nullable|numeric|min:0',
        ], [
            'productos.*.producto_id.required_with' => 'Debe seleccionar un producto',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe',
            'productos.*.cantidad.required_with' => 'Debe ingresar una cantidad',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
            'productos.*.precio.required_with' => 'Debe ingresar un precio',
            'productos.*.precio.min' => 'El precio debe ser mayor a 0',
        ]);

        DB::beginTransaction();

        try {
            // Eliminar detalles antiguos
            PresupuestoDetalle::where('presupuesto_id', $presupuesto->id)->delete();

            // Actualizar presupuesto
            $presupuesto->update([
                'cliente_id' => $validated['cliente_id'] ?? null,
                'fecha' => $validated['fecha'] ?? now()->toDateString(),
                'estado' => $validated['estado'],
                'notas' => $validated['observaciones'] ?? null,
                'total' => 0,
                'subtotal' => 0,
            ]);

            $subtotal = 0;

            // Procesar productos
            if (!empty($validated['productos'])) {
                foreach ($validated['productos'] as $item) {

                    $cantidad = floatval($item['cantidad']);
                    $precio = floatval($item['precio']);
                    $descuento_porcentaje = floatval($item['descuento_porcentaje'] ?? 0);
                    $descuento_monto = floatval($item['descuento_monto'] ?? 0);

                    $itemSubtotal = $cantidad * $precio;

                    if ($descuento_porcentaje > 0) {
                        $itemSubtotal = $itemSubtotal * (1 - $descuento_porcentaje / 100);
                    }

                    if ($descuento_monto > 0) {
                        $itemSubtotal = $itemSubtotal - $descuento_monto;
                    }

                    PresupuestoDetalle::create([
                        'presupuesto_id' => $presupuesto->id,
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $cantidad,
                        'precio' => $precio,
                        'descuento_porcentaje' => $descuento_porcentaje,
                        'descuento_monto' => $descuento_monto,
                        'subtotal' => max(0, $itemSubtotal),
                    ]);

                    $subtotal += $itemSubtotal;
                }
            }

            $presupuesto->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            DB::commit();

            return redirect()
                ->route('presupuestos.show', $presupuesto)
                ->with('success', '¡Presupuesto actualizado correctamente! N°: ' . $presupuesto->id);
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', 'Error al actualizar el presupuesto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Presupuesto $presupuesto)
    {
        // Solo se pueden eliminar presupuestos en estado borrador
        if ($presupuesto->estado !== 'borrador') {
            return redirect()
                ->route('presupuestos.index')
                ->with('error', 'Solo se pueden eliminar presupuestos en estado borrador');
        }

        $presupuesto->delete();

        return redirect()
            ->route('presupuestos.index')
            ->with('success', 'Presupuesto eliminado correctamente');
    }



    public function exportPdf(Presupuesto $presupuesto)
    {
        $presupuesto->load('cliente', 'detalles.producto', 'usuario');

        $pdf = Pdf::loadView('presupuestos.pdf', compact('presupuesto'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream(
            'Presupuesto_' .
                $presupuesto->id . '_' .
                optional($presupuesto->cliente)->nombre . '_' .
                $presupuesto->created_at->format('Ymd_His') .
                '.pdf'
        );
    }

    /**
     * Cambiar estado del presupuesto
     */
    public function cambiarEstado(Request $request, Presupuesto $presupuesto)
    {
        $validated = $request->validate([
            'estado' => 'required|in:borrador,enviado,aprobado,rechazado,convertido'
        ]);

        $presupuesto->update([
            'estado' => $validated['estado']
        ]);

        return redirect()
            ->route('presupuestos.show', $presupuesto)
            ->with('success', 'Estado actualizado a: ' . ucfirst($validated['estado']));
    }



    private function resolverCliente($input)
    {
        $input = trim($input);

        if (!$input) {
            return null;
        }

        // 🔹 Caso ID
        if (is_numeric($input) && $cliente = Cliente::find($input)) {
            return $cliente->id;
        }

        // 🔹 Caso nombre
        $normalizado = $this->normalizarNombre($input);

        $clienteExistente = Cliente::all()->first(function ($c) use ($normalizado) {
            return $this->normalizarNombre($c->nombre) === $normalizado;
        });

        if ($clienteExistente) {
            return $clienteExistente->id;
        }

        // 🔹 Crear nuevo sin duplicar
        return Cliente::create([
            'nombre' => ucwords($normalizado),
            'activo' => '1', // es tipo dato tinyint(1) en la base, pero en el modelo lo manejamos como booleano, así que se guardará como 1
        ])->id;
    }

    private function normalizarNombre($nombre)
    {
        $nombre = trim($nombre);
        $nombre = preg_replace('/\s+/', ' ', $nombre);
        return mb_strtolower($nombre, 'UTF-8');
    }
}
