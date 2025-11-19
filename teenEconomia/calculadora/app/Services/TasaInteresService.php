<?php

namespace App\Services;

class TasaInteresService
{
    /**
     * Convierte la tasa ingresada en porcentaje a tasa efectiva por período
     *
     * @param float $tasaIngresada Tasa en porcentaje (ej: 5.0 para 5%)
     * @param string $tipoTasa Tipo de conversión a aplicar
     * @return array ['tasa_periodo' => float, 'descripcion' => string, 'tipo' => string]
     */
    public function convertirTasaAPeriodo(float $tasaIngresada, string $tipoTasa): array
    {
        if ($tasaIngresada <= 0) {
            throw new \InvalidArgumentException('La tasa debe ser mayor que 0.');
        }

        $tasaInteres = $tasaIngresada / 100.0;
        $descripcion = 'Tasa por periodo (ya convertida)';

        switch ($tipoTasa) {
            case 'anual_mensual':
                $tasaInteres = ($tasaIngresada / 100.0) / 12.0;
                $descripcion = 'Tasa anual convertible mensualmente';
                break;

            case 'anual_trimestral':
                $tasaInteres = ($tasaIngresada / 100.0) / 4.0;
                $descripcion = 'Tasa anual convertible trimestralmente';
                break;

            case 'anual_semestral':
                $tasaInteres = ($tasaIngresada / 100.0) / 2.0;
                $descripcion = 'Tasa anual convertible semestralmente';
                break;

            case 'anual_diaria':
                $tasaInteres = ($tasaIngresada / 100.0) / 360.0;
                $descripcion = 'Tasa anual convertible diariamente (360 días)';
                break;

            case 'anual_diaria_365':
                $tasaInteres = ($tasaIngresada / 100.0) / 365.0;
                $descripcion = 'Tasa anual convertible diariamente (365 días)';
                break;

            case 'por_periodo':
            default:
                $tasaInteres = $tasaIngresada / 100.0;
                $descripcion = 'Tasa por periodo (ya convertida)';
                $tipoTasa = 'por_periodo';
                break;
        }

        return [
            'tasa_periodo' => $tasaInteres,
            'descripcion' => $descripcion,
            'tipo' => $tipoTasa,
            'tasa_ingresada' => $tasaIngresada,
        ];
    }

    /**
     * Obtiene las opciones disponibles para el selector de tipo de tasa
     *
     * @return array Array asociativo con value => label
     */
    public function obtenerOpcionesTipoTasa(): array
    {
        return [
            'por_periodo' => 'Por periodo de pago',
            'anual_mensual' => 'Anual convertible mensual',
            'anual_trimestral' => 'Anual convertible trimestral',
            'anual_semestral' => 'Anual convertible semestral',
            'anual_diaria' => 'Anual convertible diaria (360 días)',
            'anual_diaria_365' => 'Anual convertible diaria (365 días)',
        ];
    }
}
