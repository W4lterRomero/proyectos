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
        // Valores de ejemplo precargados para la primera visita
        $entradas = [
            'tipo_calculo' => 'vp_vf',
            'monto_pago' => 500.00,
            'tasa_interes' => 5.0,
            'tipo_tasa' => 'por_periodo',
            'numero_pagos' => 10,
            'periodos_diferidos' => 2,
        ];

        return view('calculadora', [
            'entradas' => $entradas,
        ]);
    }

    public function calcular(Request $request)
    {
        $tipoCalculo = $request->input('tipo_calculo', 'vp_vf');

        $reglasBase = [
            'tasa_interes' => ['required', 'numeric', 'gt:0'],
            'tipo_tasa' => ['required', 'string'],
        ];

        switch ($tipoCalculo) {
            case 'pago':
                $reglas = $reglasBase + [
                    'valor_presente' => ['required', 'numeric', 'gt:0'],
                    'numero_pagos' => ['required', 'integer', 'min:1'],
                    'periodos_diferidos' => ['required', 'integer', 'min:0'],
                ];
                break;

            case 'numero_pagos':
                $reglas = $reglasBase + [
                    'monto_pago' => ['required', 'numeric', 'gt:0'],
                    'valor_presente' => ['required', 'numeric', 'gt:0'],
                    'periodos_diferidos' => ['required', 'integer', 'min:0'],
                ];
                break;

            case 'periodos_diferidos':
                $reglas = $reglasBase + [
                    'monto_pago' => ['required', 'numeric', 'gt:0'],
                    'valor_presente' => ['required', 'numeric', 'gt:0'],
                    'numero_pagos' => ['required', 'integer', 'min:1'],
                ];
                break;

            case 'vp_vf':
            default:
                $reglas = $reglasBase + [
                    'monto_pago' => ['required', 'numeric', 'gt:0'],
                    'numero_pagos' => ['required', 'integer', 'min:1'],
                    'periodos_diferidos' => ['required', 'integer', 'min:0'],
                ];
                $tipoCalculo = 'vp_vf';
        }

        $validated = $request->validate($reglas);
        $validated['tipo_calculo'] = $tipoCalculo;

        // Conversión de la tasa ingresada a tasa efectiva por periodo
        $tasaIngresada = (float) $validated['tasa_interes'];
        $tipoTasa = $validated['tipo_tasa'] ?? 'por_periodo';

        $tasaInteres = $tasaIngresada / 100.0;
        $descripcionTasa = 'Tasa por periodo (ya convertida)';

        switch ($tipoTasa) {
            case 'anual_mensual':
                $tasaInteres = ($tasaIngresada / 100.0) / 12.0;
                $descripcionTasa = 'Tasa anual convertible mensualmente';
                break;
            case 'anual_trimestral':
                $tasaInteres = ($tasaIngresada / 100.0) / 4.0;
                $descripcionTasa = 'Tasa anual convertible trimestralmente';
                break;
            case 'anual_semestral':
                $tasaInteres = ($tasaIngresada / 100.0) / 2.0;
                $descripcionTasa = 'Tasa anual convertible semestralmente';
                break;
            case 'anual_diaria':
                $tasaInteres = ($tasaIngresada / 100.0) / 360.0;
                $descripcionTasa = 'Tasa anual convertible diariamente (360 días)';
                break;
            case 'anual_diaria_365':
                $tasaInteres = ($tasaIngresada / 100.0) / 365.0;
                $descripcionTasa = 'Tasa anual convertible diariamente (365 días)';
                break;
            case 'por_periodo':
            default:
                $tasaInteres = $tasaIngresada / 100.0;
                $descripcionTasa = 'Tasa por periodo (ya convertida)';
                $tipoTasa = 'por_periodo';
        }

        $resultado = [
            'modo' => $tipoCalculo,
            'tasa_detalle' => [
                'tipo' => $tipoTasa,
                'descripcion' => $descripcionTasa,
                'tasa_ingresada' => $tasaIngresada,
                'tasa_periodo' => round($tasaInteres * 100, 6),
            ],
        ];

        $pasos = [];

        // Cálculo principal según el modo seleccionado
        if ($tipoCalculo === 'vp_vf') {
            $montoPago = (float) $validated['monto_pago'];
            $numeroPagos = (int) $validated['numero_pagos'];
            $periodosDiferidos = (int) $validated['periodos_diferidos'];

            $factorAnualidad = (1 - pow(1 + $tasaInteres, -$numeroPagos)) / $tasaInteres;
            $valorPresente = $montoPago * $factorAnualidad * pow(1 + $tasaInteres, -$periodosDiferidos);
            $valorFuturo = $montoPago * ((pow(1 + $tasaInteres, $numeroPagos) - 1) / $tasaInteres);

            $resultado['valor_presente'] = $valorPresente;
            $resultado['valor_futuro'] = $valorFuturo;

            $pasos[] = 'Se convierte la tasa ingresada a decimal por periodo: <code>i = ' . sprintf('%.6f', $tasaInteres) . '</code>.';
            $pasos[] = 'Se calcula el factor de anualidad: <code>A = (1 - (1 + i)^{-n}) / i</code> con '
                . '<code>R = ' . $montoPago . '</code>, <code>n = ' . $numeroPagos . '</code>.';
            $pasos[] = 'Se obtiene el valor presente diferido: <code>VP = R · A · (1 + i)^{-k}</code> con '
                . '<code>k = ' . $periodosDiferidos . '</code>, resultando aproximadamente <strong>'
                . number_format($valorPresente, 2, ',', '.') . '</strong>.';
            $pasos[] = 'Se obtiene el valor futuro: <code>VF = R · ((1 + i)^{n} - 1) / i</code>, resultando aproximadamente <strong>'
                . number_format($valorFuturo, 2, ',', '.') . '</strong>.';
        } elseif ($tipoCalculo === 'pago') {
            $valorPresente = (float) $validated['valor_presente'];
            $numeroPagos = (int) $validated['numero_pagos'];
            $periodosDiferidos = (int) $validated['periodos_diferidos'];

            $factorAnualidad = (1 - pow(1 + $tasaInteres, -$numeroPagos)) / $tasaInteres;
            $denominador = $factorAnualidad * pow(1 + $tasaInteres, -$periodosDiferidos);

            if ($denominador <= 0) {
                return back()
                    ->withInput()
                    ->withErrors(['valor_presente' => 'Los datos proporcionados no permiten calcular un pago periódico válido.']);
            }

            $montoPago = $valorPresente / $denominador;
            $valorFuturo = $montoPago * ((pow(1 + $tasaInteres, $numeroPagos) - 1) / $tasaInteres);

            $resultado['monto_pago'] = $montoPago;
            $resultado['valor_presente'] = $valorPresente;
            $resultado['valor_futuro'] = $valorFuturo;

            $pasos[] = 'Se convierte la tasa ingresada a decimal por periodo: <code>i = ' . sprintf('%.6f', $tasaInteres) . '</code>.';
            $pasos[] = 'Se calcula el factor de anualidad: <code>A = (1 - (1 + i)^{-n}) / i</code> con '
                . '<code>n = ' . $numeroPagos . '</code>.';
            $pasos[] = 'Se despeja el pago periódico a partir de <code>VP = R · A · (1 + i)^{-k}</code>: '
                . '<code>R = VP / (A · (1 + i)^{-k})</code>, obteniendo aproximadamente <strong>'
                . number_format($montoPago, 2, ',', '.') . '</strong>.';
        } elseif ($tipoCalculo === 'numero_pagos') {
            $montoPago = (float) $validated['monto_pago'];
            $valorPresente = (float) $validated['valor_presente'];
            $periodosDiferidos = (int) $validated['periodos_diferidos'];

            $factorDiferido = pow(1 + $tasaInteres, -$periodosDiferidos);
            $base = ($valorPresente * $tasaInteres) / ($montoPago * $factorDiferido);

            if ($base <= 0 || $base >= 1) {
                return back()
                    ->withInput()
                    ->withErrors(['valor_presente' => 'Los datos proporcionados no permiten calcular un número de pagos válido.']);
            }

            $potencia = 1 - $base;
            $numeroPagosReal = -log($potencia) / log(1 + $tasaInteres);

            $resultado['numero_pagos'] = $numeroPagosReal;
            $resultado['numero_pagos_entero'] = ceil($numeroPagosReal);

            $pasos[] = 'Se plantea la ecuación a partir de <code>VP = R · [(1 - (1 + i)^{-n}) / i] · (1 + i)^{-k}</code> y se despeja <code>n</code> usando logaritmos.';
            $pasos[] = 'El número de pagos obtenido es <strong>' . number_format($numeroPagosReal, 2, ',', '.') . '</strong>, '
                . 'que se aproxima al entero <strong>' . ceil($numeroPagosReal) . '</strong>.';
        } elseif ($tipoCalculo === 'periodos_diferidos') {
            $montoPago = (float) $validated['monto_pago'];
            $valorPresente = (float) $validated['valor_presente'];
            $numeroPagos = (int) $validated['numero_pagos'];

            $factorAnualidad = (1 - pow(1 + $tasaInteres, -$numeroPagos)) / $tasaInteres;
            $base = ($valorPresente) / ($montoPago * $factorAnualidad);

            if ($base <= 0) {
                return back()
                    ->withInput()
                    ->withErrors(['valor_presente' => 'Los datos proporcionados no permiten calcular periodos diferidos válidos.']);
            }

            $periodosDiferidosReal = -log($base) / log(1 + $tasaInteres);

            $resultado['periodos_diferidos'] = $periodosDiferidosReal;
            $resultado['periodos_diferidos_entero'] = ceil($periodosDiferidosReal);

            $pasos[] = 'Se plantea la ecuación a partir de <code>VP = R · [(1 - (1 + i)^{-n}) / i] · (1 + i)^{-k}</code> y se despeja <code>k</code> usando logaritmos.';
            $pasos[] = 'Los periodos de diferimiento obtenidos son <strong>' . number_format($periodosDiferidosReal, 2, ',', '.') . '</strong>, '
                . 'que se aproximan al entero <strong>' . ceil($periodosDiferidosReal) . '</strong>.';
        }

        return view('calculadora', [
            'resultado' => array_merge($resultado, ['pasos' => $pasos]),
            'entradas' => $validated,
        ]);
    }
}
