<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnualidadDiferidaController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    public function documentacion()
    {
        return view('documentacion');
    }

    public function mostrarCalculadora()
    {
        return view('calculadora');
    }

    public function calcular(Request $request)
    {
        $validated = $request->validate([
            'monto_pago' => ['required', 'numeric', 'min:0'],
            'tasa_interes' => ['required', 'numeric', 'gt:0'],
            'numero_pagos' => ['required', 'integer', 'min:1'],
            'periodos_diferidos' => ['required', 'integer', 'min:0'],
        ]);

        $montoPago = (float) $validated['monto_pago'];
        $tasaInteres = (float) $validated['tasa_interes'] / 100; // de porcentaje a decimal
        $numeroPagos = (int) $validated['numero_pagos'];
        $periodosDiferidos = (int) $validated['periodos_diferidos'];

        // Valor presente de una anualidad diferida (pagos al final de cada periodo)
        $factorAnualidad = (1 - pow(1 + $tasaInteres, -$numeroPagos)) / $tasaInteres;
        $valorPresente = $montoPago * $factorAnualidad * pow(1 + $tasaInteres, -$periodosDiferidos);

        // Valor futuro al final del último pago (no se afecta por la diferencia)
        $valorFuturo = $montoPago * ((pow(1 + $tasaInteres, $numeroPagos) - 1) / $tasaInteres);

        return view('calculadora', [
            'resultado' => [
                'valor_presente' => $valorPresente,
                'valor_futuro' => $valorFuturo,
            ],
            'entradas' => $validated,
        ]);
    }
}
