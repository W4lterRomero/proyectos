@extends('layouts.app')

@section('title', 'Calculadora - Anualidades Diferidas')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="card-title mb-3">Calculadora de Anualidades Diferidas</h1>
                    <p class="mb-3">
                        Esta calculadora estima el <strong>valor presente (VP)</strong> y el
                        <strong>valor futuro (VF)</strong> de una anualidad diferida, a partir
                        del monto de cada pago, la tasa de interés, el número de pagos y los
                        periodos de diferimiento.
                    </p>
                    <p class="mb-3 text-muted">
                        En el formulario, <strong>R</strong> es el pago periódico, <strong>i</strong> la
                        tasa de interés por periodo, <strong>n</strong> el número total de pagos y
                        <strong>k</strong> los periodos que pasan antes de realizar el primer pago.
                    </p>

                    <form method="POST" action="{{ route('calculadora.calcular') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="monto_pago" class="form-label">Monto de cada pago (R)</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control @error('monto_pago') is-invalid @enderror"
                                id="monto_pago"
                                name="monto_pago"
                                value="{{ old('monto_pago', $entradas['monto_pago'] ?? '') }}"
                                required
                            >
                            <div class="form-text">
                                Corresponde al pago periódico que se realiza en cada periodo de la anualidad.
                            </div>
                            @error('monto_pago')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tasa_interes" class="form-label">Tasa de interés por periodo (%)</label>
                            <input
                                type="number"
                                step="0.0001"
                                min="0.0001"
                                class="form-control @error('tasa_interes') is-invalid @enderror"
                                id="tasa_interes"
                                name="tasa_interes"
                                value="{{ old('tasa_interes', $entradas['tasa_interes'] ?? '') }}"
                                required
                            >
                            <div class="form-text">
                                Es la tasa de interés efectiva por cada periodo de pago (por ejemplo, mensual o anual).
                            </div>
                            @error('tasa_interes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="numero_pagos" class="form-label">Número de pagos (n)</label>
                            <input
                                type="number"
                                min="1"
                                step="1"
                                class="form-control @error('numero_pagos') is-invalid @enderror"
                                id="numero_pagos"
                                name="numero_pagos"
                                value="{{ old('numero_pagos', $entradas['numero_pagos'] ?? '') }}"
                                required
                            >
                            <div class="form-text">
                                Indica cuántos pagos iguales se harán en total en la anualidad.
                            </div>
                            @error('numero_pagos')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="periodos_diferidos" class="form-label">Periodos diferidos antes del primer pago (k)</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                class="form-control @error('periodos_diferidos') is-invalid @enderror"
                                id="periodos_diferidos"
                                name="periodos_diferidos"
                                value="{{ old('periodos_diferidos', $entradas['periodos_diferidos'] ?? '') }}"
                                required
                            >
                            <div class="form-text">
                                Son los periodos de espera (sin pagos) que transcurren antes de iniciar la serie de pagos.
                            </div>
                            @error('periodos_diferidos')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Calcular
                        </button>
                    </form>
                </div>
            </div>

            @isset($resultado)
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 card-title mb-3">Resultados</h2>
                        <p class="mb-1">
                            <strong>Valor presente (VP):</strong>
                            {{ number_format($resultado['valor_presente'], 2, ',', '.') }}
                        </p>
                        <p class="mb-3">
                            <strong>Valor futuro (VF):</strong>
                            {{ number_format($resultado['valor_futuro'], 2, ',', '.') }}
                        </p>
                        <p class="text-muted mb-0">
                            Los resultados se muestran en las mismas unidades monetarias que el monto del pago.
                        </p>
                    </div>
                </div>
            @endisset
        </div>
    </div>
@endsection

