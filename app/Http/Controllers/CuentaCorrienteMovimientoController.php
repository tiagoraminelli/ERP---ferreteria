<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\CuentaCorrienteMovimiento;
use Illuminate\Http\Request;

class CuentaCorrienteMovimientoController extends Controller
{
    public function index($clienteId)
    {
        $cliente = Cliente::findOrFail($clienteId);

        $movimientos = CuentaCorrienteMovimiento::where('cliente_id', $cliente->id)
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('cuenta_corriente.index', [
            'cliente' => $cliente,
            'movimientos' => $movimientos
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|in:venta,pago,ajuste_debito,ajuste_credito',
            'monto' => 'required|numeric|min:0',
        ]);

        $movimiento = CuentaCorrienteMovimiento::create([
            'cliente_id' => $request->cliente_id,
            'tipo' => $request->tipo,
            'monto' => $request->monto,
            'saldo_historico' => $request->saldo_historico ?? 0,
            'referencia_id' => $request->referencia_id,
            'descripcion' => $request->descripcion,
            'fecha' => now(),
        ]);

        return back()->with('success', 'Movimiento registrado');
    }
}
