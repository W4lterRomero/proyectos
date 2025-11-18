@extends('layouts.app')

@section('title', 'Calculadora - Anualidades Diferidas')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="card-title mb-3">Calculadora de Anualidades Diferidas</h1>
                    <p class="mb-3">
                        Esta calculadora permite trabajar con <strong>anualidades diferidas</strong>:
                        puedes obtener el <strong>valor presente (VP)</strong>, el <strong>valor futuro (VF)</strong>,
                        el <strong>monto de cada pago (R)</strong>, el <strong>número de pagos (n)</strong> o los
                        <strong>periodos de diferimiento (k)</strong>, según los datos que tengas del problema.
                    </p>
                    <p class="mb-3 text-muted">
                        En todos los casos se asume una anualidad vencida (pagos al final de cada periodo) con tasa
                        de interés constante por periodo.
                    </p>

                    <form id="form-anualidad" method="POST" action="{{ route('calculadora.calcular') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="tipo_calculo" class="form-label">Tipo de cálculo</label>
                            @php
                                $tipoSeleccionado = old('tipo_calculo', $entradas['tipo_calculo'] ?? 'vp_vf');
                            @endphp
                            <select
                                class="form-select"
                                id="tipo_calculo"
                                name="tipo_calculo"
                            >
                                <option value="vp_vf" {{ $tipoSeleccionado === 'vp_vf' ? 'selected' : '' }}>
                                    Calcular VP y VF (conocidos R, i, n, k)
                                </option>
                                <option value="pago" {{ $tipoSeleccionado === 'pago' ? 'selected' : '' }}>
                                    Calcular monto de cada pago R (conocidos VP, i, n, k)
                                </option>
                                <option value="numero_pagos" {{ $tipoSeleccionado === 'numero_pagos' ? 'selected' : '' }}>
                                    Calcular número de pagos n (conocidos VP, R, i, k)
                                </option>
                                <option value="periodos_diferidos" {{ $tipoSeleccionado === 'periodos_diferidos' ? 'selected' : '' }}>
                                    Calcular periodos diferidos k (conocidos VP, R, i, n)
                                </option>
                            </select>
                            <div class="form-text">
                                Selecciona qué variable quieres obtener como resultado principal.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ejemplos rápidos</label>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="aplicarEjemplo(1)">
                                    Ejemplo 1: Compra a crédito diferida
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="aplicarEjemplo(2)">
                                    Ejemplo 2: Renta semestral diferida
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="aplicarEjemplo(3)">
                                    Ejemplo 3: Fondo con retiros futuros
                                </button>
                            </div>
                            <div class="form-text">
                                Cada ejemplo rellena el formulario con un caso típico explicado en la página de documentación.
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3 campo-grupo" id="grupo_monto_pago">
                            <label for="monto_pago" class="form-label">Monto de cada pago (R)</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control @error('monto_pago') is-invalid @enderror"
                                id="monto_pago"
                                name="monto_pago"
                                value="{{ old('monto_pago', $entradas['monto_pago'] ?? '') }}"
                            >
                            <div class="form-text">
                                Pago periódico que se realiza en cada periodo de la anualidad.
                            </div>
                            @error('monto_pago')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 campo-grupo" id="grupo_valor_presente">
                            <label for="valor_presente" class="form-label">Valor presente conocido (VP)</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control @error('valor_presente') is-invalid @enderror"
                                id="valor_presente"
                                name="valor_presente"
                                value="{{ old('valor_presente', $entradas['valor_presente'] ?? '') }}"
                            >
                            <div class="form-text">
                                Utilízalo cuando el problema te da el valor presente y necesitas calcular otra variable.
                            </div>
                            @error('valor_presente')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tasa_interes" class="form-label">Tasa de interés (%)</label>
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
                                Ingresa la tasa en porcentaje. Puedes indicar si es una tasa por periodo o una tasa anual con cierta capitalización.
                            </div>
                            @error('tasa_interes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <label for="tipo_tasa" class="form-label mt-3">Tipo de tasa</label>
                            @php
                                $tipoTasaSeleccionado = old('tipo_tasa', $entradas['tipo_tasa'] ?? 'por_periodo');
                            @endphp
                            <select
                                class="form-select @error('tipo_tasa') is-invalid @enderror"
                                id="tipo_tasa"
                                name="tipo_tasa"
                            >
                                <option value="por_periodo" {{ $tipoTasaSeleccionado === 'por_periodo' ? 'selected' : '' }}>
                                    Tasa por periodo (ya convertida)
                                </option>
                                <option value="anual_mensual" {{ $tipoTasaSeleccionado === 'anual_mensual' ? 'selected' : '' }}>
                                    Tasa anual convertible mensualmente
                                </option>
                                <option value="anual_trimestral" {{ $tipoTasaSeleccionado === 'anual_trimestral' ? 'selected' : '' }}>
                                    Tasa anual convertible trimestralmente
                                </option>
                                <option value="anual_semestral" {{ $tipoTasaSeleccionado === 'anual_semestral' ? 'selected' : '' }}>
                                    Tasa anual convertible semestralmente
                                </option>
                                <option value="anual_diaria" {{ $tipoTasaSeleccionado === 'anual_diaria' ? 'selected' : '' }}>
                                    Tasa anual convertible diariamente (360 días)
                                </option>
                                <option value="anual_diaria_365" {{ $tipoTasaSeleccionado === 'anual_diaria_365' ? 'selected' : '' }}>
                                    Tasa anual convertible diariamente (365 días)
                                </option>
                            </select>
                            <div class="form-text">
                                La calculadora convierte esta tasa a una tasa efectiva por periodo de pago según la capitalización seleccionada.
                            </div>
                            @error('tipo_tasa')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 campo-grupo" id="grupo_numero_pagos">
                            <label for="numero_pagos" class="form-label">Número de pagos (n)</label>
                            <input
                                type="number"
                                min="1"
                                step="1"
                                class="form-control @error('numero_pagos') is-invalid @enderror"
                                id="numero_pagos"
                                name="numero_pagos"
                                value="{{ old('numero_pagos', $entradas['numero_pagos'] ?? '') }}"
                            >
                            <div class="form-text">
                                Cantidad total de pagos iguales que se realizarán en la anualidad.
                            </div>
                            @error('numero_pagos')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 campo-grupo" id="grupo_periodos_diferidos">
                            <label for="periodos_diferidos" class="form-label">Periodos diferidos antes del primer pago (k)</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                class="form-control @error('periodos_diferidos') is-invalid @enderror"
                                id="periodos_diferidos"
                                name="periodos_diferidos"
                                value="{{ old('periodos_diferidos', $entradas['periodos_diferidos'] ?? '') }}"
                            >
                            <div class="form-text">
                                Periodos de espera (sin pagos) que transcurren antes de iniciar la serie de pagos.
                            </div>
                            @error('periodos_diferidos')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary me-2">
                            Calcular
                        </button>
                        <button type="button" class="btn btn-danger" onclick="limpiarFormulario()">
                            Limpiar datos
                        </button>
                    </form>
                </div>
            </div>

            @isset($resultado)
                <div class="card shadow-sm" id="resultado-card">
                    <div class="card-body">
                        <h2 class="h4 card-title mb-3">Resultados</h2>

                        @if(($resultado['modo'] ?? null) === 'vp_vf')
                            <p class="mb-1">
                                <strong>Valor presente (VP):</strong>
                                {{ number_format($resultado['valor_presente'], 2, ',', '.') }}
                            </p>
                            <p class="mb-3">
                                <strong>Valor futuro (VF):</strong>
                                {{ number_format($resultado['valor_futuro'], 2, ',', '.') }}
                            </p>
                        @elseif(($resultado['modo'] ?? null) === 'pago')
                            <p class="mb-1">
                                <strong>Monto de cada pago (R):</strong>
                                {{ number_format($resultado['monto_pago'], 2, ',', '.') }}
                            </p>
                            <p class="mb-3">
                                <strong>Valor futuro (VF) asociado:</strong>
                                {{ number_format($resultado['valor_futuro'], 2, ',', '.') }}
                            </p>
                        @elseif(($resultado['modo'] ?? null) === 'numero_pagos')
                            <p class="mb-1">
                                <strong>Número de pagos (n) calculado:</strong>
                                {{ number_format($resultado['numero_pagos'], 2, ',', '.') }}
                            </p>
                            <p class="mb-3">
                                <strong>Número de pagos aproximado (entero):</strong>
                                {{ $resultado['numero_pagos_entero'] }}
                            </p>
                        @elseif(($resultado['modo'] ?? null) === 'periodos_diferidos')
                            <p class="mb-1">
                                <strong>Periodos diferidos (k) calculados:</strong>
                                {{ number_format($resultado['periodos_diferidos'], 2, ',', '.') }}
                            </p>
                            <p class="mb-3">
                                <strong>Periodos diferidos aproximados (enteros):</strong>
                                {{ $resultado['periodos_diferidos_entero'] }}
                            </p>
                        @endif

                        @if(!empty($resultado['tasa_detalle']))
                            <hr>
                            <h3 class="h6">Conversión de la tasa</h3>
                            <p class="small mb-1">
                                Tipo de tasa: <strong>{{ $resultado['tasa_detalle']['descripcion'] }}</strong>
                            </p>
                            <p class="small mb-1">
                                Tasa ingresada: <strong>{{ $resultado['tasa_detalle']['tasa_ingresada'] }} %</strong>
                            </p>
                            <p class="small mb-0">
                                Tasa efectiva por periodo utilizada en las fórmulas:
                                <strong>{{ $resultado['tasa_detalle']['tasa_periodo'] }} %</strong>
                            </p>
                        @endif

                        @if(!empty($resultado['pasos']))
                            <hr>
                            <h3 class="h6">Proceso de cálculo</h3>
                            <ol class="small mb-0">
                                @foreach($resultado['pasos'] as $paso)
                                    <li>{!! $paso !!}</li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </div>
            @endisset
        </div>
    </div>

    <script>
        function aplicarEjemplo(n) {
            const tipoCalculo = document.getElementById('tipo_calculo');
            const montoPago = document.getElementById('monto_pago');
            const tasaInteres = document.getElementById('tasa_interes');
            const tipoTasa = document.getElementById('tipo_tasa');
            const numeroPagos = document.getElementById('numero_pagos');
            const periodosDiferidos = document.getElementById('periodos_diferidos');
            const valorPresente = document.getElementById('valor_presente');

            if (valorPresente) {
                valorPresente.value = '';
            }

            if (n === 1) {
                // Ejemplo 1 – Compra a crédito diferida
                tipoCalculo.value = 'vp_vf';
                montoPago.value = 180;
                tasaInteres.value = 36;
                tipoTasa.value = 'anual_mensual';
                numeroPagos.value = 12;
                periodosDiferidos.value = 12;
            } else if (n === 2) {
                // Ejemplo 2 – Renta semestral diferida
                tipoCalculo.value = 'vp_vf';
                montoPago.value = 6000;
                tasaInteres.value = 17;
                tipoTasa.value = 'por_periodo'; // 17 % por semestre
                numeroPagos.value = 14;
                periodosDiferidos.value = 6;
            } else if (n === 3) {
                // Ejemplo 3 – Fondo de inversión con retiros futuros
                tipoCalculo.value = 'pago';
                montoPago.value = '';
                tasaInteres.value = 17.52;
                tipoTasa.value = 'anual_mensual';
                numeroPagos.value = 10;
                periodosDiferidos.value = 21;
                if (valorPresente) {
                    valorPresente.value = 100000;
                }
            }

            actualizarTipoCalculo();
        }

        function limpiarFormulario() {
            const campos = ['monto_pago', 'valor_presente', 'tasa_interes', 'numero_pagos', 'periodos_diferidos'];
            campos.forEach(function (id) {
                const campo = document.getElementById(id);
                if (campo) {
                    campo.value = '';
                }
            });

            const tipoCalculo = document.getElementById('tipo_calculo');
            if (tipoCalculo) {
                tipoCalculo.value = 'vp_vf';
            }

            const tipoTasa = document.getElementById('tipo_tasa');
            if (tipoTasa) {
                tipoTasa.value = 'por_periodo';
            }

            const resultadoCard = document.getElementById('resultado-card');
            if (resultadoCard) {
                resultadoCard.style.display = 'none';
            }

            actualizarTipoCalculo();
        }

        function actualizarTipoCalculo() {
            const tipo = document.getElementById('tipo_calculo').value;
            const grupoMonto = document.getElementById('grupo_monto_pago');
            const grupoVP = document.getElementById('grupo_valor_presente');
            const grupoN = document.getElementById('grupo_numero_pagos');
            const grupoK = document.getElementById('grupo_periodos_diferidos');

            if (tipo === 'vp_vf') {
                grupoMonto.classList.remove('d-none');
                grupoVP.classList.add('d-none');
                grupoN.classList.remove('d-none');
                grupoK.classList.remove('d-none');
            } else if (tipo === 'pago') {
                grupoMonto.classList.add('d-none');
                grupoVP.classList.remove('d-none');
                grupoN.classList.remove('d-none');
                grupoK.classList.remove('d-none');
            } else if (tipo === 'numero_pagos') {
                grupoMonto.classList.remove('d-none');
                grupoVP.classList.remove('d-none');
                grupoN.classList.add('d-none');
                grupoK.classList.remove('d-none');
            } else if (tipo === 'periodos_diferidos') {
                grupoMonto.classList.remove('d-none');
                grupoVP.classList.remove('d-none');
                grupoN.classList.remove('d-none');
                grupoK.classList.add('d-none');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const tipoSelect = document.getElementById('tipo_calculo');
            if (tipoSelect) {
                tipoSelect.addEventListener('change', actualizarTipoCalculo);
            }

            actualizarTipoCalculo();
        });
    </script>
@endsection
