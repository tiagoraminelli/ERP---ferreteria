<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\CuentaCorrienteMovimiento as CuentaCorriente;

class ClienteController extends Controller
{

    /**
     * LISTADO
     */
    public function index(Request $request)
    {
        $query = Cliente::query();

        // ================= FILTROS =================

        // Por defecto, mostrar solo activos
        if (!$request->filled('estado')) {
            $query->where('activo', true);
        }

        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('documento', 'like', "%{$buscar}%")
                    ->orWhere('telefono', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {

            if ($request->estado === 'activos') {
                $query->where('activo', true);
            }

            if ($request->estado === 'inactivos') {
                $query->where('activo', false);
            }
        }

        if ($request->filled('deuda')) {

            if ($request->deuda === 'con_deuda') {
                $query->where('saldo_cuenta_corriente', '>', 0);
            }

            if ($request->deuda === 'sin_deuda') {
                $query->where('saldo_cuenta_corriente', '<=', 0);
            }
        }

        // ================= ORDEN =================

        $query->orderBy('nombre');

        $clientes = $query
            ->paginate(20)
            ->withQueryString();

        return view('clientes.index', compact(
            'clientes'
        ));
    }


    /**
     * FORM CREAR
     */
    public function create()
    {
        return view('clientes.create');
    }


    /**
     * GUARDAR
     */
    public function store(Request $request)
    {

        $validated = $request->validate(

            [
                'nombre' => 'required|string|max:150|min:3',

                'documento' => 'nullable|string|max:20',

                'tipo_documento' => 'nullable|in:DNI,CUIT,CUIL,Pasaporte',

                'telefono' => 'nullable|string|max:50',

                'email' => 'nullable|email|max:100',

                'direccion' => 'nullable|string|max:255',

                'ciudad' => 'nullable|string|max:100',

                'provincia' => 'nullable|string|max:100',

                'codigo_postal' => 'nullable|string|max:20',

                'limite_credito' => 'nullable|numeric|min:0',

                'notas' => 'nullable|string|max:1000',
            ],

            [
                'nombre.required' => 'El nombre del cliente es obligatorio.',
                'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',

                'email.email' => 'El email no es válido.',

                'limite_credito.numeric' => 'El límite de crédito debe ser numérico.',
                'limite_credito.min' => 'El límite de crédito no puede ser negativo.',
            ]

        );

        // ================= VALORES POR DEFECTO =================

        $validated['ciudad'] = $validated['ciudad'] ?? 'San Cristóbal';
        $validated['provincia'] = $validated['provincia'] ?? 'Santa Fe';
        $validated['codigo_postal'] = $validated['codigo_postal'] ?? '3070';

        $validated['limite_credito'] = $validated['limite_credito'] ?? 0;

        $validated['saldo_cuenta_corriente'] = 0;

        $validated['activo'] = true;

        Cliente::create($validated);


        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }


    /**
     * SHOW
     */


    public function show(Cliente $cliente)
    {
        // ================= VENTAS (SIN CUENTA CORRIENTE) =================
        $ventas = Venta::where('cliente_id', $cliente->id)
            ->where('metodo_pago', '!=', 'cuenta_corriente')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'ventas_page');

        // ================= MOVIMIENTOS =================
        $movimientos = CuentaCorriente::where('cliente_id', $cliente->id)
            ->orderBy('fecha', 'desc')
            ->paginate(10, ['*'], 'movimientos_page');

        // ================= ESTADÍSTICAS BASADAS EN DETALLES =================

        // Obtener todas las ventas del cliente (excluyendo cuenta corriente)
        $ventasCliente = Venta::where('cliente_id', $cliente->id)
            ->where('metodo_pago', '!=', 'cuenta_corriente')
            ->get();

        // Total de compras (cantidad de ventas)
        $totalCompras = $ventasCliente->count();

        // Calcular monto total comprado desde los detalles
        $montoTotal = 0;
        foreach ($ventasCliente as $venta) {
            foreach ($venta->detalles as $detalle) {
                // Usar el subtotal del detalle (que ya incluye descuentos)
                $montoTotal += $detalle->subtotal ?? ($detalle->cantidad * $detalle->precio);
            }
        }

        // Ticket promedio (montoTotal / totalCompras)
        $ticketPromedio = $totalCompras > 0 ? $montoTotal / $totalCompras : 0;

        // Última compra
        $ultimaCompra = $ventasCliente->sortByDesc('created_at')->first();

        // ================= SALDO EN VENTAS (PAGOS PARCIALES) =================
        $saldoVentas = 0;
        foreach ($ventasCliente as $venta) {
            $totalDetalles = 0;
            foreach ($venta->detalles as $detalle) {
                $totalDetalles += $detalle->subtotal ?? ($detalle->cantidad * $detalle->precio);
            }
            $saldoVentas += $totalDetalles - $venta->monto_pagado;
        }

        // ================= CUENTA CORRIENTE =================
        $saldo = $cliente->cuentaCorriente()->sum('monto');

        // ================= CRÉDITO DISPONIBLE =================
        $creditoDisponible = ($cliente->limite_credito ?? 0) - $saldo;

        // ================= TOTAL PAGADO (suma de todos los pagos) =================
        $totalPagado = $ventasCliente->sum('monto_pagado');

        return view('clientes.show', compact(
            'cliente',
            'ventas',
            'movimientos',
            'totalCompras',
            'montoTotal',
            'ticketPromedio',
            'ultimaCompra',
            'saldo',
            'saldoVentas',
            'creditoDisponible',
            'totalPagado'
        ));
    }

    /**
     * FORM EDITAR
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact(
            'cliente'
        ));
    }


    /**
     * ACTUALIZAR
     */
    public function update(Request $request, Cliente $cliente)
    {

        $validated = $request->validate(

            [
                'nombre' => 'required|string|max:150|min:3',

                'documento' => 'nullable|string|max:20',

                'tipo_documento' => 'nullable|in:DNI,CUIT,CUIL,Pasaporte',

                'telefono' => 'nullable|string|max:50',

                'email' => 'nullable|email|max:100',

                'direccion' => 'nullable|string|max:255',

                'ciudad' => 'nullable|string|max:100',

                'provincia' => 'nullable|string|max:100',

                'codigo_postal' => 'nullable|string|max:20',

                'limite_credito' => 'nullable|numeric|min:0',

                'notas' => 'nullable|string|max:1000',
            ],

            [
                'nombre.required' => 'El nombre del cliente es obligatorio.',
                'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',

                'email.email' => 'El email no es válido.',

                'limite_credito.numeric' => 'El límite de crédito debe ser numérico.',
                'limite_credito.min' => 'El límite de crédito no puede ser negativo.',
            ]

        );

        $cliente->update($validated);


        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }


    /**
     * DESACTIVAR
     */
    public function desactivar(Cliente $cliente)
    {

        $cliente->update([
            'activo' => false
        ]);

        return back()->with(
            'success',
            'Cliente desactivado correctamente.'
        );
    }


    /**
     * ACTIVAR
     */
    public function activar(Cliente $cliente)
    {

        $cliente->update([
            'activo' => true
        ]);

        return back()->with(
            'success',
            'Cliente activado correctamente.'
        );
    }


    /**
     * ELIMINAR
     */
    public function destroy(string $id)
    {

        $cliente = Cliente::findOrFail($id);

        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
