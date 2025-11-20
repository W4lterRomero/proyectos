<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AnualidadDiferidaService;
use App\Services\TasaInteresService;

class AnualidadDiferidaController extends Controller
{
    protected $anualidadService;
    protected $tasaService;

    public function __construct(AnualidadDiferidaService $anualidadService, TasaInteresService $tasaService)
    {
        $this->anualidadService = $anualidadService;
        $this->tasaService = $tasaService;
    }
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
            'tipo_calculo' => 'capital',
            'monto_pago' => 500.00,
            'tasa_interes' => 5.0,
            'tipo_tasa' => 'por_periodo',
            'numero_pagos' => 10,
            'periodos_diferidos' => 2,
        ];

        $opcionesTipoTasa = $this->tasaService->obtenerOpcionesTipoTasa();

        return view('calculadora', [
            'entradas' => $entradas,
            'opcionesTipoTasa' => $opcionesTipoTasa,
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
            case 'capital':
                $reglas = $reglasBase + [
                    'monto_pago' => ['required', 'numeric', 'gt:0'],
                    'numero_pagos' => ['required', 'integer', 'min:1'],
                    'periodos_diferidos' => ['required', 'integer', 'min:0'],
                ];
                break;

            case 'monto':
                $reglas = $reglasBase + [
                    'monto_pago' => ['required', 'numeric', 'gt:0'],
                    'numero_pagos' => ['required', 'integer', 'min:1'],
                ];
                break;

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

            case 'tasa_interes':
                // Para calcular tasa de interés NO necesitamos la tasa como entrada
                $reglas = [
                    'monto_pago' => ['required', 'numeric', 'gt:0'],
                    'valor_presente' => ['required', 'numeric', 'gt:0'],
                    'numero_pagos' => ['required', 'integer', 'min:1'],
                    'periodos_diferidos' => ['required', 'integer', 'min:0'],
                ];
                break;

            default:
                $reglas = $reglasBase + [
                    'monto_pago' => ['required', 'numeric', 'gt:0'],
                    'numero_pagos' => ['required', 'integer', 'min:1'],
                    'periodos_diferidos' => ['required', 'integer', 'min:0'],
                ];
                $tipoCalculo = 'capital';
        }

        $validated = $request->validate($reglas);
        $validated['tipo_calculo'] = $tipoCalculo;

        // Preparar información de tasa. Si se calcula la tasa, no habrá entrada de tasa_interes.
        $tasaInfo = null;
        $tasaInteres = null;
        $tasaIngresada = null;

        if ($tipoCalculo !== 'tasa_interes') {
            $tasaIngresada = (float) $validated['tasa_interes'];
            $tipoTasa = $validated['tipo_tasa'] ?? 'por_periodo';

            try {
                $tasaInfo = $this->tasaService->convertirTasaAPeriodo($tasaIngresada, $tipoTasa);
                $tasaInteres = $tasaInfo['tasa_periodo'];
            } catch (\InvalidArgumentException $e) {
                return back()
                    ->withInput()
                    ->withErrors(['tasa_interes' => $e->getMessage()]);
            }
        }

        $resultado = ['modo' => $tipoCalculo];

        if ($tasaInfo) {
            $resultado['tasa_detalle'] = [
                'tipo' => $tasaInfo['tipo'],
                'descripcion' => $tasaInfo['descripcion'],
                'tasa_ingresada' => $tasaIngresada,
                'tasa_periodo' => round($tasaInteres * 100, 6),
            ];
        }

        $pasos = [];

        // Cálculo principal según el modo seleccionado
        if ($tipoCalculo === 'capital') {
            $r = (float) $validated['monto_pago'];
            $n = (int) $validated['numero_pagos'];
            $k = (int) $validated['periodos_diferidos'];
            $i = $tasaInteres;

            try {
                // Usar el servicio para calcular solo el capital
                $resultadoCalculo = $this->anualidadService->calcularCapital($r, $n, $k, $i);

                $capital = $resultadoCalculo['capital'];
                $cTemporal = $resultadoCalculo['c_temporal'];

                $resultado['valor_presente'] = $capital;

                // Generar pasos
                $pasos[] = '<strong>Datos de entrada:</strong>';
                $pasos[] =
                    '• Renta \(R\): $' . number_format($r, 2) . '<br>' .
                    '• Número de pagos \(n\): ' . $n . '<br>' .
                    '• Tasa de interés \(i\): ' . sprintf('%.6f', $i) . ' (' . sprintf('%.4f', $i * 100) . '% por período)<br>' .
                    '• Períodos diferidos \(k\): ' . $k;

                // Paso 1: C_temporal
                $pasos[] = '<strong>Paso 1: Calcular capital equivalente de la anualidad vencida al final del período diferido</strong><br>';
                $pasos[] = '\\[ C_{\\text{temporal}} = R \\cdot \\frac{1 - (1 + i)^{-n}}{i} \\]';
                $pasos[] =
                    '\\[ C_{\\text{temporal}} = ' . number_format($r, 2) .
                    ' \\cdot \\frac{1 - (1 + ' . sprintf('%.6f', $i) . ')^{-' . $n . '}}{' . sprintf('%.6f', $i) . '} \\approx ' .
                    number_format($cTemporal, 2) . ' \\]';

                // Paso 2: trasladar al tiempo 0
                $pasos[] = '<strong>Paso 2: Trasladar el capital al tiempo 0 (inicio)</strong><br>';
                $pasos[] = '\\[ C = C_{\\text{temporal}} \\cdot (1 + i)^{-k} \\]';
                $pasos[] =
                    '\\[ C = ' . number_format($cTemporal, 2) .
                    ' \\cdot (1 + ' . sprintf('%.6f', $i) . ')^{-' . $k . '} \\approx ' .
                    number_format($capital, 2) . ' \\]';

                $pasos[] =
                    '<strong>Resultado Final - Capital (C):</strong> ' .
                    '<span class="text-blue-700 font-bold text-lg">$' . number_format($capital, 2) . '</span>';
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->withErrors(['monto_pago' => 'Error en el cálculo: ' . $e->getMessage()]);
            }
        } elseif ($tipoCalculo === 'monto') {
            $r = (float) $validated['monto_pago'];
            $n = (int) $validated['numero_pagos'];
            $i = $tasaInteres;

            try {
                // Usar el servicio para calcular solo el monto
                $resultadoCalculo = $this->anualidadService->calcularMonto($r, $n, $i);

                $monto = $resultadoCalculo['monto'];

                $resultado['valor_futuro'] = $monto;

                // Generar pasos
                $pasos[] = '<strong>Datos de entrada:</strong>';
                $pasos[] =
                    '• Renta \(R\): $' . number_format($r, 2) . '<br>' .
                    '• Número de pagos \(n\): ' . $n . '<br>' .
                    '• Tasa de interés \(i\): ' . sprintf('%.6f', $i) . ' (' . sprintf('%.4f', $i * 100) . '% por período)';

                // Cálculo del monto
                $pasos[] = '<strong>Cálculo del monto (M):</strong><br>';
                $pasos[] = '\\[ M = R \\cdot \\frac{(1 + i)^{n} - 1}{i} \\]';
                $pasos[] =
                    '\\[ M = ' . number_format($r, 2) .
                    ' \\cdot \\frac{(1 + ' . sprintf('%.6f', $i) . ')^{' . $n . '} - 1}{' . sprintf('%.6f', $i) . '} \\approx ' .
                    number_format($monto, 2) . ' \\]';

                $pasos[] =
                    '<strong>Resultado Final - Monto (M):</strong> ' .
                    '<span class="text-indigo-700 font-bold text-lg">$' . number_format($monto, 2) . '</span>';
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->withErrors(['monto_pago' => 'Error en el cálculo: ' . $e->getMessage()]);
            }
        } elseif ($tipoCalculo === 'pago') {
            $c = (float) $validated['valor_presente'];
            $n = (int) $validated['numero_pagos'];
            $k = (int) $validated['periodos_diferidos'];
            $i = $tasaInteres;

            try {
                // Usar el servicio para calcular el pago
                $resultadoCalculo = $this->anualidadService->calcularPago($c, $n, $k, $i);

                $r = $resultadoCalculo['renta'];
                $vpFinal = $resultadoCalculo['vp_final'];
                $monto = $resultadoCalculo['monto'];

                $resultado['monto_pago'] = $r;
                $resultado['valor_presente'] = $c;
                $resultado['valor_futuro'] = $monto;

                $pasos[] = '<strong>Datos de entrada:</strong>';
                $pasos[] =
                    '• Capital conocido \(C\): $' . number_format($c, 2) . '<br>' .
                    '• Número de pagos \(n\): ' . $n . '<br>' .
                    '• Tasa de interés \(i\): ' . sprintf('%.6f', $i) . ' (' . sprintf('%.4f', $i * 100) . '% por período)<br>' .
                    '• Períodos diferidos \(k\): ' . $k;

                $pasos[] = '<strong>Paso 1: Llevar el capital hasta el final del período de diferimiento</strong><br>';
                $pasos[] = '\\[ VP_{\\text{final}} = C (1 + i)^{k} \\]';
                $pasos[] =
                    '\\[ VP_{\\text{final}} = ' . number_format($c, 2) .
                    ' \\cdot (1 + ' . sprintf('%.6f', $i) . ')^{' . $k . '} \\approx ' .
                    number_format($vpFinal, 2) . ' \\]';

                $pasos[] = '<strong>Paso 2: Aplicar la fórmula de anualidad vencida y despejar R</strong><br>';
                $pasos[] = '\\[ VP_{\\text{final}} = R \\cdot \\frac{1 - (1 + i)^{-n}}{i} \\]';
                $pasos[] = '\\[ R = VP_{\\text{final}} \\cdot \\frac{i}{1 - (1 + i)^{-n}} \\]';
                $pasos[] =
                    '\\[ R = ' . number_format($vpFinal, 2) .
                    ' \\cdot \\frac{' . sprintf('%.6f', $i) . '}{1 - (1 + ' . sprintf('%.6f', $i) . ')^{-' . $n . '}} \\approx ' .
                    number_format($r, 2) . ' \\]';

                $pasos[] =
                    '<strong>Resultado Final - Renta (R):</strong> ' .
                    '<span class="text-emerald-700 font-bold text-lg">$' . number_format($r, 2) . '</span>';

                $pasos[] = '<hr class="my-4">';

                $pasos[] = '<strong>Monto futuro asociado:</strong><br>';
                $pasos[] = '\\[ M = R \\cdot \\frac{(1 + i)^{n} - 1}{i} \\]';
                $pasos[] =
                    '\\[ M = ' . number_format($r, 2) .
                    ' \\cdot \\frac{(1 + ' . sprintf('%.6f', $i) . ')^{' . $n . '} - 1}{' . sprintf('%.6f', $i) . '} \\approx ' .
                    number_format($monto, 2) . ' \\]';
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->withErrors(['valor_presente' => $e->getMessage()]);
            }
        } elseif ($tipoCalculo === 'numero_pagos') {
            $r = (float) $validated['monto_pago'];
            $c = (float) $validated['valor_presente'];
            $k = (int) $validated['periodos_diferidos'];
            $i = $tasaInteres;

            try {
                // Usar el servicio para calcular número de pagos
                $resultadoCalculo = $this->anualidadService->calcularNumeroPagos($c, $r, $k, $i);

                $nReal = $resultadoCalculo['n_real'];
                $nEntero = $resultadoCalculo['n_piso'];
                $pagoFinal = $resultadoCalculo['pago_final'];
                $vpFinal = $resultadoCalculo['vp_final'];
                $montoAcumulado = $resultadoCalculo['monto_acumulado'];
                $sumaPagos = $resultadoCalculo['suma_pagos'];

                $resultado['numero_pagos'] = $nReal;
                $resultado['numero_pagos_entero'] = $resultadoCalculo['n_entero'];
                $resultado['n_piso'] = $nEntero;
                $resultado['pago_final'] = $pagoFinal;

                $pasos[] = '<strong>Datos de entrada:</strong>';
                $pasos[] =
                    '• Capital conocido \(C\): $' . number_format($c, 2) . '<br>' .
                    '• Renta \(R\): $' . number_format($r, 2) . '<br>' .
                    '• Tasa de interés \(i\): ' . sprintf('%.6f', $i) . ' (' . sprintf('%.4f', $i * 100) . '% por período)<br>' .
                    '• Períodos diferidos \(k\): ' . $k;

                $pasos[] = '<strong>Paso 1: Calcular el capital al final del período diferido</strong><br>';
                $pasos[] = '\\[ VP_{\\text{final}} = C (1 + i)^{k} \\]';
                $pasos[] =
                    '\\[ VP_{\\text{final}} = ' . number_format($c, 2) .
                    ' \\cdot (1 + ' . sprintf('%.6f', $i) . ')^{' . $k . '} \\approx ' .
                    number_format($vpFinal, 2) . ' \\]';

                $pasos[] = '<strong>Paso 2: Plantear la ecuación de anualidad vencida</strong><br>';
                $pasos[] = '\\[ VP_{\\text{final}} = R \\cdot \\frac{1 - (1 + i)^{-n}}{i} \\]';

                $pasos[] = '<strong>Paso 3: Despejar n usando logaritmos</strong><br>';
                $pasos[] = '\\[ n = - \\frac{\\ln\\left( 1 - \\dfrac{VP_{\\text{final}} \\cdot i}{R} \\right)}{\\ln(1 + i)} \\]';
                $pasos[] =
                    '\\[ n = - \\frac{\\ln\\left( 1 - \\dfrac{' . number_format($vpFinal, 2) . ' \\cdot ' . sprintf('%.6f', $i) .
                    '}{' . number_format($r, 2) . '} \\right)}{\\ln(1 + ' . sprintf('%.6f', $i) . ')} \\approx ' .
                    sprintf('%.6f', $nReal) . ' \\text{ pagos} \\]';

                $pasos[] =
                    '<strong>Resultado: n = ' . sprintf('%.6f', $nReal) . ' pagos</strong>';

                $pasos[] = '<hr class="my-4">';

                $pasos[] = '<strong>Interpretación del resultado decimal:</strong><br>'
                    . '<span class="text-cyan-700"><strong>Opción A:</strong></span> Hacer ' . $nEntero . ' pagos completos de $' . number_format($r, 2)
                    . ' + 1 pago final de <strong>$' . number_format($pagoFinal, 2) . '</strong><br>'
                    . '<span class="text-blue-700"><strong>Opción B:</strong></span> Redondear a ' . ceil($nReal) . ' pagos completos';

                $pasos[] = '<strong>Desglose Opción A:</strong><br>'
                    . '• Monto acumulado después de ' . $nEntero . ' períodos: $' . number_format($montoAcumulado, 2) . '<br>'
                    . '• Suma de ' . $nEntero . ' pagos: $' . number_format($sumaPagos, 2) . '<br>'
                    . '• Saldo restante · (1+i): $' . number_format($pagoFinal, 2);
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->withErrors(['valor_presente' => $e->getMessage()]);
            }
        } elseif ($tipoCalculo === 'periodos_diferidos') {
            $r = (float) $validated['monto_pago'];
            $c = (float) $validated['valor_presente'];
            $n = (int) $validated['numero_pagos'];
            $i = $tasaInteres;

            try {
                // Usar el servicio para calcular períodos diferidos
                $resultadoCalculo = $this->anualidadService->calcularPeriodosDiferidos($c, $r, $n, $i);

                $kReal = $resultadoCalculo['k_real'];

                $resultado['periodos_diferidos'] = $kReal;
                $resultado['periodos_diferidos_entero'] = $resultadoCalculo['k_entero'];

                $pasos[] = '<strong>Datos de entrada:</strong>';
                $pasos[] =
                    '• Capital conocido \(C\): $' . number_format($c, 2) . '<br>' .
                    '• Renta \(R\): $' . number_format($r, 2) . '<br>' .
                    '• Número de pagos \(n\): ' . $n . '<br>' .
                    '• Tasa de interés \(i\): ' . sprintf('%.6f', $i) . ' (' . sprintf('%.4f', $i * 100) . '% por período)';

                $pasos[] = '<strong>Paso único: despejar k de la ecuación del capital</strong><br>';
                $pasos[] = '\\[ C = R \\cdot \\frac{1 - (1 + i)^{-n}}{i} \\cdot (1 + i)^{-k} \\]';
                $pasos[] = '\\[ k = - \\frac{\\ln\\left( \\dfrac{C \\cdot i}{R \\cdot (1 - (1 + i)^{-n})} \\right)}{\\ln(1 + i)} \\]';
                $pasos[] =
                    '\\[ k = - \\frac{\\ln\\left( \\dfrac{' . number_format($c, 2) . ' \\cdot ' . sprintf('%.6f', $i) .
                    '}{' . number_format($r, 2) . ' \\cdot (1 - (1 + ' . sprintf('%.6f', $i) . ')^{-' . $n . '})} \\right)}{\\ln(1 + ' . sprintf('%.6f', $i) . ')} \\approx ' .
                    number_format($kReal, 2) . ' \\]';
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->withErrors(['valor_presente' => $e->getMessage()]);
            }
        } elseif ($tipoCalculo === 'tasa_interes') {
            $r = (float) $validated['monto_pago'];
            $c = (float) $validated['valor_presente'];
            $n = (int) $validated['numero_pagos'];
            $k = (int) $validated['periodos_diferidos'];

            try {
                // Usar el servicio para calcular la tasa con Newton-Raphson
                $resultadoCalculo = $this->anualidadService->calcularTasaNewtonRaphson($c, $r, $n, $k);

                $iCalculada = $resultadoCalculo['tasa'];
                $iteraciones = $resultadoCalculo['iteraciones'];
                $convergencia = $resultadoCalculo['convergencia'];
                $numIteraciones = $resultadoCalculo['num_iteraciones'];

                $resultado['tasa_interes'] = $iCalculada;
                $resultado['num_iteraciones'] = $numIteraciones;
                $resultado['convergencia'] = $convergencia;

                $pasos[] = '<strong>Datos de entrada:</strong>';
                $pasos[] =
                    '• Capital conocido \(C\): $' . number_format($c, 2) . '<br>' .
                    '• Renta \(R\): $' . number_format($r, 2) . '<br>' .
                    '• Número de pagos \(n\): ' . $n . '<br>' .
                    '• Períodos diferidos \(k\): ' . $k;

                $pasos[] = '<strong>Método: Newton-Raphson</strong><br>';
                $pasos[] = 'Resolviendo la ecuación: \\[ C = R \\cdot \\frac{1 - (1 + i)^{-n}}{i} \\cdot (1 + i)^{-k} \\]';
                $pasos[] = 'Buscamos el valor de \(i\) que satisface esta ecuación mediante iteraciones sucesivas.';

                $pasos[] = '<strong>Iteraciones del método Newton-Raphson:</strong><br>';
                $pasos[] = 'Fórmula: \\( i_{n+1} = i_n - \\frac{f(i_n)}{f\'(i_n)} \\)';

                // Mostrar primeras 5 iteraciones
                $iteracionesMostrar = array_slice($iteraciones, 0, 5);
                $tablaIteraciones = '<table class="w-full text-xs mt-2 border-collapse">';
                $tablaIteraciones .= '<tr class="bg-slate-100 border-b border-slate-300">';
                $tablaIteraciones .= '<th class="p-1 text-left">Iter</th>';
                $tablaIteraciones .= '<th class="p-1 text-right">i (tasa)</th>';
                $tablaIteraciones .= '<th class="p-1 text-right">VP calculado</th>';
                $tablaIteraciones .= '<th class="p-1 text-right">Error</th>';
                $tablaIteraciones .= '</tr>';

                foreach ($iteracionesMostrar as $iter) {
                    $tablaIteraciones .= '<tr class="border-b border-slate-200">';
                    $tablaIteraciones .= '<td class="p-1">' . $iter['iteracion'] . '</td>';
                    $tablaIteraciones .= '<td class="p-1 text-right">' . number_format($iter['i'] * 100, 6) . '%</td>';
                    $tablaIteraciones .= '<td class="p-1 text-right">$' . number_format($iter['vp_calculado'], 2) . '</td>';
                    $tablaIteraciones .= '<td class="p-1 text-right">$' . number_format($iter['error_absoluto'], 2) . '</td>';
                    $tablaIteraciones .= '</tr>';
                }

                if (count($iteraciones) > 5) {
                    $tablaIteraciones .= '<tr><td colspan="4" class="p-1 text-center text-slate-500">...</td></tr>';
                    $ultima = end($iteraciones);
                    $tablaIteraciones .= '<tr class="border-b border-slate-200 bg-green-50">';
                    $tablaIteraciones .= '<td class="p-1 font-semibold">' . $ultima['iteracion'] . '</td>';
                    $tablaIteraciones .= '<td class="p-1 text-right font-semibold">' . number_format($ultima['i'] * 100, 6) . '%</td>';
                    $tablaIteraciones .= '<td class="p-1 text-right font-semibold">$' . number_format($ultima['vp_calculado'], 2) . '</td>';
                    $tablaIteraciones .= '<td class="p-1 text-right font-semibold">$' . number_format($ultima['error_absoluto'], 6) . '</td>';
                    $tablaIteraciones .= '</tr>';
                }

                $tablaIteraciones .= '</table>';
                $pasos[] = $tablaIteraciones;

                $pasos[] =
                    '<strong>Resultado Final - Tasa de interés (i):</strong> ' .
                    '<span class="text-orange-700 font-bold text-lg">' . number_format($iCalculada * 100, 6) . '% por período</span>';

                $pasos[] = '<strong>Estado de convergencia:</strong> ' . $convergencia;
                $pasos[] = '<strong>Número de iteraciones:</strong> ' . $numIteraciones;

                // Verificación
                $vpVerificacion = $r * ((1 - pow(1 + $iCalculada, -$n)) / $iCalculada) * pow(1 + $iCalculada, -$k);
                $pasos[] = '<hr class="my-4">';
                $pasos[] = '<strong>Verificación:</strong><br>';
                $pasos[] = '• Capital ingresado: $' . number_format($c, 2) . '<br>';
                $pasos[] = '• VP con tasa calculada: $' . number_format($vpVerificacion, 2) . '<br>';
                $pasos[] = '• Diferencia: $' . number_format(abs($c - $vpVerificacion), 6);
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->withErrors(['monto_pago' => 'Error en el cálculo: ' . $e->getMessage()]);
            }
        }

        $opcionesTipoTasa = $this->tasaService->obtenerOpcionesTipoTasa();

        return view('calculadora', [
            'resultado' => array_merge($resultado, ['pasos' => $pasos]),
            'entradas' => $validated,
            'opcionesTipoTasa' => $opcionesTipoTasa,
        ]);
    }

    /**
     * Calcula el capital (C) de una anualidad diferida
     * para una tasa de interés i dada:
     * C = R · [1 - (1+i)^(-n)] / i · (1+i)^(-k)
     */
    protected function calcularVPConTasa(float $r, int $n, int $k, float $i): float
    {
        if ($i <= 0.0) {
            throw new \InvalidArgumentException('La tasa debe ser mayor que 0.');
        }

        $factorAnualidad = (1 - pow(1 + $i, -$n)) / $i;

        return $r * $factorAnualidad * pow(1 + $i, -$k);
    }

    /**
     * Calcula la tasa de interés i de una anualidad diferida
     * usando el método de bisección sobre:
     * C = R · [1 - (1+i)^(-n)] / i · (1+i)^(-k)
     */
    protected function calcularTasaPorBiseccion(float $c, float $r, int $n, int $k): array
    {
        if ($c <= 0.0) {
            throw new \InvalidArgumentException('El valor presente debe ser mayor que 0.');
        }

        if ($r <= 0.0) {
            throw new \InvalidArgumentException('La renta debe ser mayor que 0.');
        }

        if ($n <= 0) {
            throw new \InvalidArgumentException('El número de pagos debe ser positivo.');
        }

        if ($k < 0) {
            throw new \InvalidArgumentException('Los periodos diferidos no pueden ser negativos.');
        }

        $iMin = 0.0001; // 0.01 %
        $iMax = 0.5;    // 50 %
        $precision = 0.000001;
        $maxIter = 200;
        $iteraciones = [];

        $iMedio = $iMin;

        for ($iter = 0; $iter < $maxIter && ($iMax - $iMin) > $precision; $iter++) {
            $iMedio = ($iMin + $iMax) / 2.0;
            $vpCalculado = $this->calcularVPConTasa($r, $n, $k, $iMedio);

            $iteraciones[] = [
                'i' => $iMedio,
                'vp_calculado' => $vpCalculado,
                'diferencia' => $c - $vpCalculado,
            ];

            if (abs($vpCalculado - $c) < $precision) {
                break;
            }

            if ($vpCalculado > $c) {
                $iMin = $iMedio;
            } else {
                $iMax = $iMedio;
            }
        }

        return [
            'i' => $iMedio,
            'iteraciones' => $iteraciones,
        ];
    }
}

