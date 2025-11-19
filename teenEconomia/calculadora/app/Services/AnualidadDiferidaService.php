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
            throw new \DomainException('Los datos no permiten calcular períodos diferidos válidos.');
        }

        $kReal = -log($base) / log(1 + $i);

        return [
            'k_real' => $kReal,
            'k_entero' => (int) ceil($kReal),
            'factor_anualidad' => $factorAnualidad,
        ];
    }
}
