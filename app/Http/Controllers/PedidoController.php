<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Categoria;


class PedidoController extends Controller
{

    public function index(Request $request)
    {
        $query = Pedido::with('categoria');

        if ($request->filled('ocultos')) {
            $query->where('visible', false);
        } else {
            $query->where('visible', true);
        }

        // ================= FILTROS =================

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('codigo', 'like', "%{$buscar}%")
                    ->orWhere('proveedor', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('cantidad')) {
            switch ($request->cantidad) {
                case 'mayor1':
                    $query->where('cantidad', '>', 1);
                    break;

                case 'unitario':
                    $query->where('cantidad', 1);
                    break;
            }
        }

        // ================= ORDEN =================

        $orden = $request->get('orden', 'nombre');
        $direccion = $request->get('direccion', 'asc');
        $fechaDeCreacion = $request->get('created_at', 'desc');

        // $query->orderBy($orden, $direccion);
        $query->orderBy('created_at', $fechaDeCreacion);

        $pedidos = $query->paginate(15)->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | MÉTRICAS
    |--------------------------------------------------------------------------
    */

        $metricBaseQuery = Pedido::query();

        if ($request->filled('ocultos')) {
            $metricBaseQuery->where('visible', false);
        } else {
            $metricBaseQuery->where('visible', true);
        }

        $totalPedidos = $metricBaseQuery->count();
        $pendientesCount = (clone $metricBaseQuery)
            ->where('estado', 'pendiente')
            ->count();

        $compradosCount = (clone $metricBaseQuery)
            ->where('estado', 'comprado')
            ->count();

        $cantidadTotal = (clone $metricBaseQuery)->sum('cantidad');

        /*
    |--------------------------------------------------------------------------
    | DATOS PARA FILTROS
    |--------------------------------------------------------------------------
    */

        $categorias = Categoria::orderBy('nombre')->get();

        return view('pedidos.index', compact(
            'pedidos',
            'totalPedidos',
            'pendientesCount',
            'compradosCount',
            'cantidadTotal',
            'categorias'
        ));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('pedidos.create', compact(
            'categorias'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:150|min:3',
                'descripcion' => 'nullable|string|max:1000',
                'cantidad' => 'required|integer|min:1',
                'categoria_id' => 'nullable|exists:categorias,id',
                'proveedor' => 'nullable|string|max:150',

                'estado' => 'nullable|in:pendiente,comprado,cancelado',
                'visible' => 'nullable|boolean',
            ],
            [
                'nombre.required' => 'El nombre del pedido es obligatorio.',
                'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',

                'cantidad.required' => 'La cantidad es obligatoria.',
                'cantidad.integer' => 'La cantidad debe ser un número entero.',
                'cantidad.min' => 'La cantidad mínima es 1.',

                'categoria_id.exists' => 'La categoría seleccionada no es válida.',

                'estado.in' => 'El estado seleccionado no es válido.',
            ]
        );

        // Valores por defecto
        $validated['estado'] = $validated['estado'] ?? 'pendiente';
        $validated['visible'] = $validated['visible'] ?? true;

        // Crear pedido (sin código todavía)
        $pedido = Pedido::create($validated);

        // Generar código dinámico (PED-ID)
        $pedido->codigo = 'PED-' . $pedido->id;
        $pedido->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pedido creado correctamente.',
                'pedido' => $pedido,
            ]);
        }

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido creado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pedido = Pedido::findOrFail($id);
        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('pedidos.edit', compact(
            'pedido',
            'categorias'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:150|min:3',
                'descripcion' => 'nullable|string|max:1000',
                'cantidad' => 'required|integer|min:1',
                'categoria_id' => 'nullable|exists:categorias,id',
                'proveedor' => 'nullable|string|max:150',
                'observaciones' => 'nullable|string|max:1000',

                'estado' => 'nullable|in:pendiente,comprado,cancelado',
                'visible' => 'nullable|boolean',
            ],
            [
                'nombre.required' => 'El nombre del pedido es obligatorio.',
                'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',

                'cantidad.required' => 'La cantidad es obligatoria.',
                'cantidad.integer' => 'La cantidad debe ser un número entero.',
                'cantidad.min' => 'La cantidad mínima es 1.',

                'categoria_id.exists' => 'La categoría seleccionada no es válida.',
                'estado.in' => 'El estado seleccionado no es válido.',

                'observaciones.max' => 'Las observaciones no pueden superar los 1000 caracteres.',
            ]
        );

        $validated['visible'] = $validated['visible'] ?? false;

        $pedido->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pedido actualizado.',
                'pedido' => $pedido,
            ]);
        }

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido actualizado.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // cambiar el visible a "oculto" en lugar de eliminar físicamente
        $pedido = Pedido::findOrFail($id);
        $pedido->update(['visible' => false]);

        return back()->with('success', 'Pedido ocultado correctamente.');
    }

    // Metodo para restaurar un pedido oculto
    public function restaurar(Pedido $pedido)
    {
        $pedido->update([
            'estado' => 'pendiente',   // o como quieras volverlo
            'oculto'  => false
        ]);

        return redirect()->route('pedidos.index')
            ->with('success', 'Pedido restaurado correctamente.');
    }

    // Método para marcar un pedido como comprado
    public function comprar(Pedido $pedido)
    {
        $pedido->update(['estado' => 'comprado']);

        return back()->with('success', 'Pedido comprado correctamente.');
    }

    // Método para marcar un pedido como cancelado
    public function cancelar(Pedido $pedido)
    {
        $pedido->update(['estado' => 'cancelado']);

        return back()->with('success', 'Pedido cancelado correctamente.');
    }
}
