<?php

namespace App\Http\Controllers;

// dependencias necesarias
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
// dependencias de soporte
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'marca', 'proveedor']);

        /*
    |--------------------------------------------------------------------------
    | FILTRO ELIMINADOS (VISIBLE BOOLEAN)
    |--------------------------------------------------------------------------
    | Por defecto muestra activo (1)
    | Si viene ?activo=1 muestra solo eliminados (0)
    */

        if ($request->filled('eliminados')) {
            $query->where('activo', 0);
        } else {
            $query->where('activo', 1);
        }

        // ================= FILTROS AVANZADOS =================

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('codigo_barra', 'like', "%{$buscar}%")
                    ->orWhere('modelo', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('marca')) {
            $query->where('marca_id', $request->marca);
        }

        if ($request->filled('proveedor')) {
            $query->where('proveedor_id', $request->proveedor);
        }

        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'bajo':
                    $query->whereRaw('stock <= stock_minimo');
                    break;

                case 'agotado':
                    $query->where('stock', '<=', 0);
                    break;

                case 'disponible':
                    $query->where('stock', '>', 0);
                    break;
            }
        }

        // ================= ORDENAMIENTO =================

        $orden = $request->get('orden', 'nombre');
        $direccion = $request->get('direccion', 'asc');

        $query->orderBy($orden, $direccion);

        $productos = $query->paginate(15)->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | MÉTRICAS (respetando visibles/eliminados)
    |--------------------------------------------------------------------------
    */

        $metricBaseQuery = Producto::query();

        if ($request->filled('eliminados')) {
            $metricBaseQuery->where('activo', 0);
        } else {
            $metricBaseQuery->where('activo', 1);
        }

        $totalProductos = $metricBaseQuery->count();
        $productosActivos = (clone $metricBaseQuery)->where('activo', true)->count();
        $stockBajoCount = (clone $metricBaseQuery)->whereRaw('stock <= stock_minimo')->count();
        $valorInventario = (clone $metricBaseQuery)->sum(DB::raw('stock * precio_costo'));

        // ================= DATOS PARA FILTROS =================

        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $marcas = Marca::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('productos.index', compact(
            'productos',
            'totalProductos',
            'productosActivos',
            'stockBajoCount',
            'valorInventario',
            'categorias',
            'marcas',
            'proveedores'
        ));
    }

    public function create()
    {
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();
        $unidades = UnidadMedida::where('activo', 1)->orderBy('nombre')->get();

        return view('productos.create', compact('categorias', 'marcas', 'proveedores', 'unidades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:200|min:3',
                'descripcion' => 'nullable|string|max:1000',
                'modelo' => 'nullable|string|max:150|min:3',

                'precio_costo' => 'nullable|numeric|min:0',
                'precio' => 'required|numeric|min:0|gte:precio_costo',
                'margen_ganancia' => 'nullable|numeric|min:0',

                'stock' => 'required|numeric|min:0',
                'stock_minimo' => 'required|numeric|min:0',

                'categoria_id' => 'nullable|exists:categorias,id',
                'unidad_medida_id' => 'nullable|exists:unidades_medida,id',
                'marca_id' => 'nullable|exists:marcas,id',
                'proveedor_id' => 'nullable|exists:proveedores,id',
            ],
            [
                'nombre.required' => 'El nombre del producto es obligatorio.',
                'nombre.max' => 'El nombre no puede superar los 200 caracteres.',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',

                'precio.required' => 'El precio de venta es obligatorio.',
                'precio.numeric' => 'El precio debe ser un número válido.',
                'precio.min' => 'El precio no puede ser negativo.',
                'precio.gte' => 'El precio de venta no puede ser menor al precio de proveedor.',

                'precio_costo.numeric' => 'El precio de proveedor debe ser un número válido.',
                'precio_costo.min' => 'El precio de proveedor no puede ser negativo.',

                'margen_ganancia.numeric' => 'El margen de ganancia debe ser un número.',
                'margen_ganancia.min' => 'El margen de ganancia no puede ser negativo.',

                'stock.required' => 'El stock es obligatorio.',
                'stock.numeric' => 'El stock debe ser un número.',
                'stock.min' => 'El stock no puede ser negativo.',

                'stock_minimo.required' => 'El stock mínimo es obligatorio.',
                'stock_minimo.numeric' => 'El stock mínimo debe ser un número.',
                'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',

                'categoria_id.exists' => 'La categoría seleccionada no es válida.',
                'unidad_medida_id.exists' => 'La unidad de medida seleccionada no es válida.',
                'marca_id.exists' => 'La marca seleccionada no es válida.',
                'proveedor_id.exists' => 'El proveedor seleccionado no es válido.',
            ]
        );

        // 🔥 Crear producto
        $producto = Producto::create($validated);

        // 🔥 Generar código de barras automático (Regla: P00 + ID)
        $producto->codigo_barra = 'P00' . $producto->id;
        $producto->save();

        // 🔹 Respuesta AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente.',
                'producto' => $producto->load(['categoria', 'marca'])
            ]);
        }

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|max:200',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'codigo_barra' => 'nullable|unique:productos,codigo_barra,' . $producto->id,
        ]);

        $producto->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'producto' => $producto->load(['categoria', 'marca'])
            ]);
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado');
    }

    public function show(Producto $producto)
    {
        return response()->json($producto->load(['categoria', 'marca', 'proveedor']));
    }

    public function destroy(Producto $producto)
    {
        // Softdelete, cambiando el campo "activo" a false
        $producto->update(['activo' => false]);
        return redirect()->route('productos.index')->with('success', 'Producto eliminado');
    }

    // En ProductoController.php
    public function bulkUpdatePrices(Request $request)
    {
        $request->validate([
            'productos' => 'required|string',
            'tipo_actualizacion' => 'required|in:fijo,porcentaje_aumento,porcentaje_disminucion',
            'precio_fijo' => 'required_if:tipo_actualizacion,fijo|numeric|min:0|nullable',
            'porcentaje' => 'required_if:tipo_actualizacion,porcentaje_aumento,porcentaje_disminucion|numeric|nullable'
        ]);

        $productosIds = explode(',', $request->productos);
        $productos = Producto::whereIn('id', $productosIds)->get();

        DB::beginTransaction();
        try {
            foreach ($productos as $producto) {
                $precioAnterior = $producto->precio;

                switch ($request->tipo_actualizacion) {
                    case 'fijo':
                        $nuevoPrecio = $request->precio_fijo;
                        break;
                    case 'porcentaje_aumento':
                        $nuevoPrecio = $producto->precio * (1 + ($request->porcentaje / 100));
                        break;
                    case 'porcentaje_disminucion':
                        $nuevoPrecio = $producto->precio * (1 - ($request->porcentaje / 100));
                        break;
                }

                $producto->update(['precio' => $nuevoPrecio]);

                // Registrar el cambio en historial (si tienes tabla de historial)
                // $producto->historialPrecios()->create([...]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Precios actualizados correctamente',
                    'updated_count' => $productos->count()
                ]);
            }

            return redirect()->back()->with('success', 'Precios actualizados correctamente');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar precios: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al actualizar precios');
        }
    }

    public function restaurar(Producto $producto)
{
    // Solo restaurar si está inactivo
    if ($producto->activo == 0) {
        $producto->activo = 1;
        $producto->save();

        return redirect()
            ->back()
            ->with('success', 'Producto restaurado correctamente.');
    }

    return redirect()
        ->back()
        ->with('info', 'El producto ya está activo.');
}
}
