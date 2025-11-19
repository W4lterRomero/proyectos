@props([
    'name' => 'tipo_tasa',
    'id' => 'tipo_tasa',
    'selected' => 'por_periodo',
    'opciones' => null
])

@php
    // Si no se pasan opciones, usar las opciones por defecto
    $opcionesDefault = [
        'por_periodo' => 'Por periodo de pago',
        'anual_mensual' => 'Anual convertible mensual',
        'anual_trimestral' => 'Anual convertible trimestral',
        'anual_semestral' => 'Anual convertible semestral',
        'anual_diaria' => 'Anual convertible diaria (360 días)',
        'anual_diaria_365' => 'Anual convertible diaria (365 días)',
    ];

    $opcionesFinales = $opciones ?? $opcionesDefault;
@endphp

<select
    id="{{ $id }}"
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'w-full sm:w-1/2 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-800 text-xs']) }}
>
    @foreach($opcionesFinales as $value => $label)
        <option value="{{ $value }}" {{ $selected === $value ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>
