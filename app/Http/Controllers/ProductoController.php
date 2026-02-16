<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'marca', 'proveedor']);

        // Filtros avanzados
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
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

        // Ordenamiento
        $orden = $request->get('orden', 'nombre');
        $direccion = $request->get('direccion', 'asc');
        $query->orderBy($orden, $direccion);

        $productos = $query->paginate(15)->withQueryString();

        // Métricas
        $totalProductos = Producto::count();
        $productosActivos = Producto::where('activo', true)->count();
        $stockBajoCount = Producto::whereRaw('stock <= stock_minimo')->count();
        $valorInventario = Producto::sum(DB::raw('stock * precio_costo'));

        // Datos para filtros
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
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

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:200',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'codigo_barra' => 'nullable|unique:productos,codigo_barra',
        ]);

        $producto = Producto::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'producto' => $producto->load(['categoria', 'marca'])
            ]);
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado');
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
}
