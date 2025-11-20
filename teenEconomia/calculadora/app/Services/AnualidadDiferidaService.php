<?php

namespace App\Services;

class AnualidadDiferidaService
{
    /**
     * Calcula solo el capital (C) de una anualidad diferida
     *
     * Fórmulas:
     * C_temporal = R · (1 - (1+i)^(-n)) / i
     * C = C_temporal · (1+i)^(-k)
     *
     * @param float $r Renta o pago periódico
     * @param int $n Número de pagos
     * @param int $k Períodos de diferimiento
     * @param float $i Tasa de interés por período (en decimal, ej: 0.05 para 5%)
     * @return array ['capital' => float, 'c_temporal' => float, 'factor_anualidad' => float]
     */
    public function calcularCapital(float $r, int $n, int $k, float $i): array
    {
        // Validaciones
        if ($r <= 0) {
            throw new \InvalidArgumentException('La renta (R) debe ser mayor que 0.');
        }
        if ($n <= 0) {
            throw new \InvalidArgumentException('El número de pagos (n) debe ser mayor que 0.');
        }
        if ($k < 0) {
            throw new \InvalidArgumentException('Los períodos diferidos (k) no pueden ser negativos.');
        }
        if ($i <= 0) {
            throw new \InvalidArgumentException('La tasa de interés (i) debe ser mayor que 0.');
        }

        // Paso 1: Calcular capital temporal (al final del período diferido)
        $factorAnualidad = (1 - pow(1 + $i, -$n)) / $i;
        $cTemporal = $r * $factorAnualidad;

        // Paso 2: Trasladar al tiempo 0
        $capital = $cTemporal * pow(1 + $i, -$k);

        // Validar que el capital sea positivo
        if ($capital <= 0) {
            throw new \DomainException('El capital calculado es negativo o cero. Verifique los datos ingresados (renta, tasa, períodos).');
        }

        // Validar que el capital sea finito
        if (!is_finite($capital)) {
            throw new \DomainException('El capital calculado es infinito. Verifique la tasa de interés y los períodos.');
        }

        return [
            'capital' => $capital,
            'c_temporal' => $cTemporal,
            'factor_anualidad' => $factorAnualidad,
        ];
    }

    /**
     * Calcula solo el monto (M) de una anualidad diferida
     *
     * Fórmula:
     * M = R · ((1+i)^n - 1) / i
     *
     * @param float $r Renta o pago periódico
     * @param int $n Número de pagos
     * @param float $i Tasa de interés por período (en decimal, ej: 0.05 para 5%)
     * @return array ['monto' => float]
     */
    public function calcularMonto(float $r, int $n, float $i): array
    {
        // Validaciones
        if ($r <= 0) {
            throw new \InvalidArgumentException('La renta (R) debe ser mayor que 0.');
        }
        if ($n <= 0) {
            throw new \InvalidArgumentException('El número de pagos (n) debe ser mayor que 0.');
        }
        if ($i <= 0) {
            throw new \InvalidArgumentException('La tasa de interés (i) debe ser mayor que 0.');
        }

        // Calcular monto
        $monto = $r * (pow(1 + $i, $n) - 1) / $i;

        // Validar que el monto sea positivo
        if ($monto <= 0) {
            throw new \DomainException('El monto calculado es negativo o cero. Verifique los datos ingresados (renta, tasa, número de pagos).');
        }

        // Validar que el monto sea finito
        if (!is_finite($monto)) {
            throw new \DomainException('El monto calculado es infinito. Verifique la tasa de interés (puede ser demasiado alta).');
        }

        return [
            'monto' => $monto,
        ];
    }

    /**
     * Calcula el pago periódico (R) conociendo el capital (C)
     *
     * @param float $c Capital o valor presente
     * @param int $n Número de pagos
     * @param int $k Períodos de diferimiento
     * @param float $i Tasa de interés por período
     * @return array ['renta' => float, 'vp_final' => float, 'monto' => float]
     */
    public function calcularPago(float $c, int $n, int $k, float $i): array
    {
        // Validaciones
        if ($c <= 0) {
            throw new \InvalidArgumentException('El capital (C) debe ser mayor que 0.');
        }
        if ($n <= 0) {
            throw new \InvalidArgumentException('El número de pagos (n) debe ser mayor que 0.');
        }
        if ($k < 0) {
            throw new \InvalidArgumentException('Los períodos diferidos (k) no pueden ser negativos.');
        }
        if ($i <= 0) {
            throw new \InvalidArgumentException('La tasa de interés (i) debe ser mayor que 0.');
        }

        // Paso 1: Llevar el capital al final del período diferido
        $vpFinal = $c * pow(1 + $i, $k);

        // Paso 2: Calcular la renta
        $factorAnualidad = (1 - pow(1 + $i, -$n)) / $i;

        if ($factorAnualidad <= 0) {
            throw new \DomainException('Los datos no permiten calcular un pago válido.');
        }

        $r = ($vpFinal * $i) / (1 - pow(1 + $i, -$n));

        // Validar que la renta sea positiva
        if ($r <= 0) {
            throw new \DomainException('El pago calculado es negativo o cero. Verifique los datos ingresados (capital, tasa, número de pagos).');
        }

        // Validar que la renta sea un valor razonable (no infinito ni muy grande)
        if (!is_finite($r) || $r > PHP_FLOAT_MAX / 1000) {
            throw new \DomainException('El pago calculado es un valor demasiado grande o infinito. Verifique la tasa de interés y el número de pagos.');
        }

        // Calcular monto asociado
        $monto = $r * (pow(1 + $i, $n) - 1) / $i;

        return [
            'renta' => $r,
            'vp_final' => $vpFinal,
            'monto' => $monto,
            'factor_anualidad' => $factorAnualidad,
        ];
    }

    /**
     * Calcula el número de pagos (n) conociendo capital y renta
     *
     * @param float $c Capital o valor presente
     * @param float $r Renta o pago periódico
     * @param int $k Períodos de diferimiento
     * @param float $i Tasa de interés por período
     * @return array ['n_real' => float, 'n_entero' => int, 'n_piso' => int, 'pago_final' => float]
     */
    public function calcularNumeroPagos(float $c, float $r, int $k, float $i): array
    {
        // Validaciones
        if ($c <= 0) {
            throw new \InvalidArgumentException('El capital (C) debe ser mayor que 0.');
        }
        if ($r <= 0) {
            throw new \InvalidArgumentException('La renta (R) debe ser mayor que 0.');
        }
        if ($k < 0) {
            throw new \InvalidArgumentException('Los períodos diferidos (k) no pueden ser negativos.');
        }
        if ($i <= 0) {
            throw new \InvalidArgumentException('La tasa de interés (i) debe ser mayor que 0.');
        }

        // Llevar capital al final del período diferido
        $vpFinal = $c * pow(1 + $i, $k);

        // Verificar que sea posible calcular n
        $factor = 1 - ($vpFinal * $i / $r);

        if ($factor <= 0 || $factor >= 1) {
            throw new \DomainException('No es posible calcular un número de pagos válido. Intente aumentar la renta (R) o reducir el capital (C).');
        }

        // Calcular n real
        $nReal = -log($factor) / log(1 + $i);

        // Validar que n sea positivo y finito
        if ($nReal <= 0) {
            throw new \DomainException('El número de pagos calculado es negativo o cero. La renta (R) es demasiado pequeña para cubrir el capital.');
        }

        if (!is_finite($nReal)) {
            throw new \DomainException('El número de pagos calculado es infinito. Verifique los datos ingresados.');
        }

        // Validar que n no sea excesivamente grande (más de 1000 pagos es probablemente un error)
        if ($nReal > 1000) {
            throw new \DomainException('El número de pagos calculado es excesivamente grande (' . number_format($nReal, 2) . ' pagos). Aumente la renta (R) o reduzca el capital (C).');
        }

        // Calcular pago final para opción A
        $nPiso = (int) floor($nReal);
        $montoAcumulado = $vpFinal * pow(1 + $i, $nPiso);
        $sumaPagos = $r * (pow(1 + $i, $nPiso) - 1) / $i;
        $pagoFinal = ($montoAcumulado - $sumaPagos) * (1 + $i);

        return [
            'n_real' => $nReal,
            'n_entero' => (int) ceil($nReal),
            'n_piso' => $nPiso,
            'pago_final' => $pagoFinal,
            'vp_final' => $vpFinal,
            'monto_acumulado' => $montoAcumulado,
            'suma_pagos' => $sumaPagos,
        ];
    }

    /**
     * Calcula los períodos diferidos (k) conociendo capital, renta y n
     *
     * @param float $c Capital o valor presente
     * @param float $r Renta o pago periódico
     * @param int $n Número de pagos
     * @param float $i Tasa de interés por período
     * @return array ['k_real' => float, 'k_entero' => int]
     */
    public function calcularPeriodosDiferidos(float $c, float $r, int $n, float $i): array
    {
        // Validaciones
        if ($c <= 0) {
            throw new \InvalidArgumentException('El capital (C) debe ser mayor que 0.');
        }
        if ($r <= 0) {
            throw new \InvalidArgumentException('La renta (R) debe ser mayor que 0.');
        }
        if ($n <= 0) {
            throw new \InvalidArgumentException('El número de pagos (n) debe ser mayor que 0.');
        }
        if ($i <= 0) {
            throw new \InvalidArgumentException('La tasa de interés (i) debe ser mayor que 0.');
        }

        $factorAnualidad = (1 - pow(1 + $i, -$n)) / $i;
        $base = $c / ($r * $factorAnualidad);

        if ($base <= 0) {
            throw new \DomainException('Los datos no permiten calcular períodos diferidos válidos. El capital es demasiado bajo en relación a la renta.');
        }

        // Si base > 1, significa que C > VP de la anualidad sin diferimiento, lo cual implica k negativo
        if ($base > 1) {
            throw new \DomainException('Los datos indican que NO hay período de diferimiento (k sería negativo). El capital (' . number_format($c, 2) . ') es mayor que el valor de la anualidad sin diferimiento (' . number_format($r * $factorAnualidad, 2) . '). Verifique los datos ingresados.');
        }

        $kReal = -log($base) / log(1 + $i);

        // Validar que k sea finito
        if (!is_finite($kReal)) {
            throw new \DomainException('Los períodos diferidos calculados son infinitos. Verifique los datos ingresados.');
        }

        // Validar que k no sea negativo
        if ($kReal < 0) {
            throw new \DomainException('Los períodos diferidos calculados son negativos (' . number_format($kReal, 2) . '). Esto indica que el capital es mayor que el valor presente de la anualidad. Verifique los datos.');
        }

        // Validar que k no sea excesivamente grande (más de 500 períodos es probablemente un error)
        if ($kReal > 500) {
            throw new \DomainException('Los períodos diferidos calculados son excesivamente grandes (' . number_format($kReal, 2) . ' períodos). Verifique los datos ingresados.');
        }

        return [
            'k_real' => $kReal,
            'k_entero' => (int) ceil($kReal),
            'factor_anualidad' => $factorAnualidad,
        ];
    }

    /**
     * Calcula la tasa de interés (i) usando el método de Newton-Raphson
     *
     * Resuelve la ecuación: C = R · [(1-(1+i)^(-n))/i] · (1+i)^(-k)
     *
     * @param float $c Capital o valor presente
     * @param float $r Renta o pago periódico
     * @param int $n Número de pagos
     * @param int $k Períodos de diferimiento
     * @return array ['tasa' => float, 'iteraciones' => array, 'convergencia' => string]
     */
    public function calcularTasaNewtonRaphson(float $c, float $r, int $n, int $k): array
    {
        // Validaciones
        if ($c <= 0) {
            throw new \InvalidArgumentException('El capital (C) debe ser mayor que 0.');
        }
        if ($r <= 0) {
            throw new \InvalidArgumentException('La renta (R) debe ser mayor que 0.');
        }
        if ($n <= 0) {
            throw new \InvalidArgumentException('El número de pagos (n) debe ser mayor que 0.');
        }
        if ($k < 0) {
            throw new \InvalidArgumentException('Los períodos diferidos (k) no pueden ser negativos.');
        }

        // Validación matemática: el pago total debe ser mayor que el capital
        // Si R*n <= C, no existe tasa de interés positiva que satisfaga la ecuación
        if ($r * $n <= $c) {
            throw new \DomainException(
                'Los datos no permiten calcular una tasa de interés válida. ' .
                'El pago total (R × n = $' . number_format($r * $n, 2) . ') ' .
                'debe ser significativamente mayor que el capital (C = $' . number_format($c, 2) . '). ' .
                'Aumente el monto de pago (R) o el número de pagos (n), o reduzca el capital (C).'
            );
        }

        // Estimación inicial: usar una tasa razonable del 5%
        $i = 0.05;

        // Parámetros del método
        $precision = 0.00000001; // 10^-8
        $maxIteraciones = 50;
        $iteraciones = [];

        for ($iter = 0; $iter < $maxIteraciones; $iter++) {
            // Calcular VP con tasa actual
            $vpCalculado = $this->calcularVPConTasa($r, $n, $k, $i);

            // f(i) = VP_calculado - C (queremos que sea 0)
            $f = $vpCalculado - $c;

            // Calcular derivada usando diferencias finitas
            $h = 0.00000001;
            $fPrima = $this->calcularDerivadaVP($r, $n, $k, $i, $h);

            // Validar que los valores calculados sean finitos
            if (!is_finite($vpCalculado) || !is_finite($f) || !is_finite($fPrima)) {
                throw new \DomainException(
                    'Se detectaron valores no válidos durante el cálculo de la tasa. ' .
                    'Los datos ingresados podrían no permitir una solución matemática válida. ' .
                    'Verifique que el pago total (R × n) sea mayor que el capital (C).'
                );
            }

            // Guardar iteración
            $iteraciones[] = [
                'iteracion' => $iter + 1,
                'i' => $i,
                'vp_calculado' => $vpCalculado,
                'f' => $f,
                'f_prima' => $fPrima,
                'error_absoluto' => abs($f),
            ];

            // Verificar convergencia
            if (abs($f) < $precision) {
                // Validar que la tasa final sea razonable
                if ($i < 0.0001 || $i > 0.5) {
                    throw new \DomainException('La tasa calculada (' . number_format($i * 100, 4) . '%) parece fuera de rango razonable (0.01% - 50%). Verifique los datos ingresados.');
                }

                return [
                    'tasa' => $i,
                    'iteraciones' => $iteraciones,
                    'convergencia' => 'Convergió exitosamente',
                    'num_iteraciones' => $iter + 1,
                ];
            }

            // Verificar que la derivada no sea muy pequeña
            if (abs($fPrima) < 1e-10) {
                return [
                    'tasa' => $i,
                    'iteraciones' => $iteraciones,
                    'convergencia' => 'Derivada muy pequeña, el método puede no converger',
                    'num_iteraciones' => $iter + 1,
                ];
            }

            // Calcular siguiente aproximación: i_nuevo = i - f(i)/f'(i)
            $iNuevo = $i - ($f / $fPrima);

            // Asegurar que i sea positivo (tasas negativas no tienen sentido)
            if ($iNuevo <= 0) {
                $iNuevo = $i / 2;
            }

            // Limitar la tasa a un máximo razonable (100%)
            if ($iNuevo > 1.0) {
                $iNuevo = 1.0;
            }

            // Verificar cambio entre iteraciones
            if (abs($iNuevo - $i) < $precision) {
                $i = $iNuevo;
                break;
            }

            $i = $iNuevo;
        }

        // Si llegamos aquí, se alcanzó el máximo de iteraciones
        // Validar que el resultado final sea válido antes de retornar
        if (!is_finite($i) || $i <= 0 || $i > 1.0) {
            throw new \DomainException(
                'El método de Newton-Raphson no pudo converger a una tasa válida. ' .
                'Los datos ingresados podrían ser inconsistentes. ' .
                'Verifique que el pago total (R × n = $' . number_format($r * $n, 2) . ') ' .
                'sea mayor que el capital (C = $' . number_format($c, 2) . ').'
            );
        }

        return [
            'tasa' => $i,
            'iteraciones' => $iteraciones,
            'convergencia' => 'Máximo de iteraciones alcanzado',
            'num_iteraciones' => count($iteraciones),
        ];
    }

    /**
     * Calcula el valor presente (VP) dado una tasa de interés
     *
     * @param float $r Renta
     * @param int $n Número de pagos
     * @param int $k Períodos diferidos
     * @param float $i Tasa de interés
     * @return float Valor presente
     */
    private function calcularVPConTasa(float $r, int $n, int $k, float $i): float
    {
        if ($i <= 0) {
            return PHP_FLOAT_MAX;
        }

        $factorAnualidad = (1 - pow(1 + $i, -$n)) / $i;
        $factorDiferimiento = pow(1 + $i, -$k);

        return $r * $factorAnualidad * $factorDiferimiento;
    }

    /**
     * Calcula la derivada de VP respecto a i usando diferencias finitas
     *
     * @param float $r Renta
     * @param int $n Número de pagos
     * @param int $k Períodos diferidos
     * @param float $i Tasa de interés actual
     * @param float $h Delta para la aproximación
     * @return float Derivada aproximada
     */
    private function calcularDerivadaVP(float $r, int $n, int $k, float $i, float $h): float
    {
        $vpMas = $this->calcularVPConTasa($r, $n, $k, $i + $h);
        $vpMenos = $this->calcularVPConTasa($r, $n, $k, $i - $h);

        $derivada = ($vpMas - $vpMenos) / (2 * $h);

        // Validar que la derivada sea finita
        if (!is_finite($derivada)) {
            // Retornar un valor muy pequeño para evitar división por cero
            return 1e-10;
        }

        return $derivada;
    }
}
