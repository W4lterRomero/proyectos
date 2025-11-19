@extends('layouts.app')

@section('title', 'Calculadora - Anualidades Diferidas')

@section('content')
    <div class="min-h-screen bg-linear-to-br from-slate-50 via-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8 border border-slate-100">
                <div class="bg-linear-to-r from-blue-600 to-indigo-600 px-6 sm:px-8 py-8">
                    <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">Calculadora de Anualidades Diferidas</h1>
                    <p class="text-blue-100 text-sm sm:text-base">Calcula VP, VF, R, n o k con precisión</p>
                </div>

                <div class="px-6 sm:px-8 py-6 space-y-4">
                    <p class="text-gray-700 leading-relaxed">
                        Esta calculadora permite trabajar con <strong>anualidades diferidas</strong>:
                        puedes obtener el <strong>valor presente (VP)</strong>, el <strong>valor futuro (VF)</strong>,
                        el <strong>monto de cada pago (R)</strong>, el <strong>número de pagos (n)</strong> o los
                        <strong>periodos de diferimiento (k)</strong>, según los datos que tengas del problema.
                    </p>
                    <p class="text-gray-600 text-sm">
                        En todos los casos se asume una anualidad vencida (pagos al final de cada periodo) con tasa
                        de interés constante por periodo.
                    </p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-slate-100">
                <form id="form-anualidad" method="POST" action="{{ route('calculadora.calcular') }}" class="p-6 sm:p-8">
                    @csrf

                    <!-- Tipo de Cálculo Section -->
                    <div class="mb-8">
                        <div class="bg-linear-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100 mb-4">
                            <label for="tipo_calculo" class="block text-sm font-semibold text-gray-800 mb-2">
                                <span class="inline-block bg-blue-600 text-white text-xs font-bold px-2.5 py-0.5 rounded-full mr-2">PASO 1</span>
                                Tipo de cálculo
                            </label>
                            @php
                                $tipoSeleccionado = old('tipo_calculo', $entradas['tipo_calculo'] ?? 'vp_vf');
                            @endphp
                            <select
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white text-gray-800"
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
                            <p class="text-xs text-gray-600 mt-2">
                                Selecciona qué variable quieres obtener como resultado principal.
                            </p>
                        </div>
                    </div>

                    <!-- Ejemplos Rápidos Section -->
                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <label class="block text-sm font-semibold text-gray-800 mb-3">
                            <span class="inline-block bg-emerald-600 text-white text-xs font-bold px-2.5 py-0.5 rounded-full mr-2">ATAJOS</span>
                            Cargar ejemplos rápidos
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <button
                                type="button"
                                class="px-4 py-2.5 bg-linear-to-r from-emerald-500 to-teal-500 text-white rounded-lg font-medium hover:from-emerald-600 hover:to-teal-600 transition transform hover:scale-105 active:scale-95 text-sm"
                                onclick="aplicarEjemplo(1)"
                            >
                                Compra a crédito diferida
                            </button>
                            <button
                                type="button"
                                class="px-4 py-2.5 bg-linear-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-medium hover:from-cyan-600 hover:to-blue-600 transition transform hover:scale-105 active:scale-95 text-sm"
                                onclick="aplicarEjemplo(2)"
                            >
                                Renta semestral diferida
                            </button>
                            <button
                                type="button"
                                class="px-4 py-2.5 bg-linear-to-r from-violet-500 to-indigo-500 text-white rounded-lg font-medium hover:from-violet-600 hover:to-indigo-600 transition transform hover:scale-105 active:scale-95 text-sm"
                                onclick="aplicarEjemplo(3)"
                            >
                                Fondo con retiros futuros
                            </button>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">
                            Cada ejemplo rellena el formulario con un caso típico explicado en la documentación.
                        </p>
                    </div>

                    <!-- Campos del Formulario -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                        <!-- Monto de Pago -->
                        <div class="campo-grupo" id="grupo_monto_pago">
                            <label for="monto_pago" class="block text-sm font-semibold text-gray-800 mb-2">
                                Monto de cada pago (R)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('monto_pago') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                                id="monto_pago"
                                name="monto_pago"
                                value="{{ old('monto_pago', $entradas['monto_pago'] ?? '') }}"
                                placeholder="0.00"
                            >
                            <p class="text-xs text-gray-600 mt-1.5">
                                Pago periódico en cada período
                            </p>
                            @error('monto_pago')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Valor Presente -->
                        <div class="campo-grupo" id="grupo_valor_presente">
                            <label for="valor_presente" class="block text-sm font-semibold text-gray-800 mb-2">
                                Valor presente (VP)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-2.5 border @error('valor_presente') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white text-gray-800"
                                id="valor_presente"
                                name="valor_presente"
                                value="{{ old('valor_presente', $entradas['valor_presente'] ?? '') }}"
                                placeholder="0.00"
                            >
                            <p class="text-xs text-gray-600 mt-1.5">
                                Usa cuando tienes el VP inicial
                            </p>
                            @error('valor_presente')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tasa de Interés -->
                        <div class="sm:col-span-2">
                            <label for="tasa_interes" class="block text-sm font-semibold text-gray-800 mb-2">
                                Tasa de interés (%)
                            </label>
                            <input
                                type="number"
                                step="0.0001"
                                min="0.0001"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('tasa_interes') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                                id="tasa_interes"
                                name="tasa_interes"
                                value="{{ old('tasa_interes', $entradas['tasa_interes'] ?? '') }}"
                                placeholder="0.0000"
                                required
                            >
                            <p class="text-xs text-gray-600 mt-1.5">
                                Ingresa en porcentaje. La calculadora la convierte según el tipo de tasa seleccionado.
                            </p>
                            @error('tasa_interes')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de Tasa -->
                        <div class="sm:col-span-2">
                            <label for="tipo_tasa" class="block text-sm font-semibold text-gray-800 mb-2">
                                Tipo de tasa
                            </label>
                            @php
                                $tipoTasaSeleccionado = old('tipo_tasa', $entradas['tipo_tasa'] ?? 'por_periodo');
                            @endphp
                            <select
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('tipo_tasa') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
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
                            <p class="text-xs text-gray-600 mt-1.5">
                                La calculadora convierte a tasa efectiva por periodo según capitalización.
                            </p>
                            @error('tipo_tasa')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Número de Pagos -->
                        <div class="campo-grupo" id="grupo_numero_pagos">
                            <label for="numero_pagos" class="block text-sm font-semibold text-gray-800 mb-2">
                                Número de pagos (n)
                            </label>
                            <input
                                type="number"
                                min="1"
                                step="1"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('numero_pagos') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                                id="numero_pagos"
                                name="numero_pagos"
                                value="{{ old('numero_pagos', $entradas['numero_pagos'] ?? '') }}"
                                placeholder="1"
                            >
                            <p class="text-xs text-gray-600 mt-1.5">
                                Cantidad total de pagos iguales
                            </p>
                            @error('numero_pagos')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Períodos Diferidos -->
                        <div class="campo-grupo" id="grupo_periodos_diferidos">
                            <label for="periodos_diferidos" class="block text-sm font-semibold text-gray-800 mb-2">
                                Periodos diferidos (k)
                            </label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('periodos_diferidos') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                                id="periodos_diferidos"
                                name="periodos_diferidos"
                                value="{{ old('periodos_diferidos', $entradas['periodos_diferidos'] ?? '') }}"
                                placeholder="0"
                            >
                            <p class="text-xs text-gray-600 mt-1.5">
                                Periodos de espera antes del primer pago
                            </p>
                            @error('periodos_diferidos')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                        <button
                            type="submit"
                            class="flex-1 px-6 py-3 bg-linear-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition transform hover:scale-105 active:scale-95 shadow-lg"
                        >
                            Calcular Resultado
                        </button>
                        <button
                            type="button"
                            class="flex-1 px-6 py-3 bg-linear-to-r from-red-500 to-pink-500 text-white rounded-lg font-semibold hover:from-red-600 hover:to-pink-600 transition transform hover:scale-105 active:scale-95 shadow-lg"
                            onclick="limpiarFormulario()"
                        >
                            Limpiar Datos
                        </button>
                    </div>
                </form>
            </div>

            <!-- Resultados Card -->
            @isset($resultado)
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-slate-100 mt-8" id="resultado-card">
                    <div class="bg-linear-to-r from-emerald-600 to-teal-600 px-6 sm:px-8 py-6">
                        <h2 class="text-2xl font-bold text-white">Resultados del Cálculo</h2>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- Resultados principales según modo -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(($resultado['modo'] ?? null) === 'vp_vf')
                                <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                                    <p class="text-sm text-gray-600 font-medium mb-1">Valor Presente (VP)</p>
                                    <p class="text-3xl font-bold text-blue-700">
                                        {{ number_format($resultado['valor_presente'], 2, ',', '.') }}
                                    </p>
                                </div>
                                <div class="bg-linear-to-br from-indigo-50 to-indigo-100 rounded-lg p-6 border border-indigo-200">
                                    <p class="text-sm text-gray-600 font-medium mb-1">Valor Futuro (VF)</p>
                                    <p class="text-3xl font-bold text-indigo-700">
                                        {{ number_format($resultado['valor_futuro'], 2, ',', '.') }}
                                    </p>
                                </div>
                            @elseif(($resultado['modo'] ?? null) === 'pago')
                                <div class="bg-linear-to-br from-emerald-50 to-emerald-100 rounded-lg p-6 border border-emerald-200">
                                    <p class="text-sm text-gray-600 font-medium mb-1">Monto de Pago (R)</p>
                                    <p class="text-3xl font-bold text-emerald-700">
                                        {{ number_format($resultado['monto_pago'], 2, ',', '.') }}
                                    </p>
                                </div>
                                <div class="bg-linear-to-br from-teal-50 to-teal-100 rounded-lg p-6 border border-teal-200">
                                    <p class="text-sm text-gray-600 font-medium mb-1">Valor Futuro (VF)</p>
                                    <p class="text-3xl font-bold text-teal-700">
                                        {{ number_format($resultado['valor_futuro'], 2, ',', '.') }}
                                    </p>
                                </div>
                            @elseif(($resultado['modo'] ?? null) === 'numero_pagos')
                                <div class="bg-linear-to-br from-cyan-50 to-cyan-100 rounded-lg p-6 border border-cyan-200">
                                    <p class="text-sm text-gray-600 font-medium mb-1">Número de Pagos (n)</p>
                                    <p class="text-3xl font-bold text-cyan-700">
                                        {{ number_format($resultado['numero_pagos'], 2, ',', '.') }}
                                    </p>
                                </div>
                                <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                                    <p class="text-sm text-gray-600 font-medium mb-1">Valor Entero (Aproximado)</p>
                                    <p class="text-3xl font-bold text-blue-700">
                                        {{ $resultado['numero_pagos_entero'] }}
                                    </p>
                                </div>
                            @elseif(($resultado['modo'] ?? null) === 'periodos_diferidos')
                                <div class="bg-linear-to-br from-violet-50 to-violet-100 rounded-lg p-6 border border-violet-200">
                                    <p class="text-sm text-gray-600 font-medium mb-1">Periodos Diferidos (k)</p>
                                    <p class="text-3xl font-bold text-violet-700">
                                        {{ number_format($resultado['periodos_diferidos'], 2, ',', '.') }}
                                    </p>
                                </div>
                                <div class="bg-linear-to-br from-indigo-50 to-indigo-100 rounded-lg p-6 border border-indigo-200">
                                    <p class="text-sm text-gray-600 font-medium mb-1">Valor Entero (Aproximado)</p>
                                    <p class="text-3xl font-bold text-indigo-700">
                                        {{ $resultado['periodos_diferidos_entero'] }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Conversión de Tasa -->
                        @if(!empty($resultado['tasa_detalle']))
                            <div class="bg-amber-50 border-l-4 border-amber-500 rounded-r-lg p-4">
                                <h3 class="text-sm font-bold text-amber-900 mb-3">Conversión de la Tasa</h3>
                                <div class="space-y-2">
                                    <p class="text-sm">
                                        <span class="font-semibold text-gray-700">Tipo de tasa:</span>
                                        <span class="text-gray-600">{{ $resultado['tasa_detalle']['descripcion'] }}</span>
                                    </p>
                                    <p class="text-sm">
                                        <span class="font-semibold text-gray-700">Tasa ingresada:</span>
                                        <span class="text-gray-600">{{ $resultado['tasa_detalle']['tasa_ingresada'] }} %</span>
                                    </p>
                                    <p class="text-sm">
                                        <span class="font-semibold text-gray-700">Tasa efectiva por periodo:</span>
                                        <span class="font-bold text-amber-700">{{ $resultado['tasa_detalle']['tasa_periodo'] }} %</span>
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Proceso de Cálculo -->
                        @if(!empty($resultado['pasos']))
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h3 class="text-sm font-bold text-gray-800 mb-3">Proceso de Cálculo</h3>
                                <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                    @foreach($resultado['pasos'] as $paso)
                                        <li class="text-gray-600">{!! $paso !!}</li>
                                    @endforeach
                                </ol>
                            </div>
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
                grupoMonto.classList.remove('hidden');
                grupoVP.classList.add('hidden');
                grupoN.classList.remove('hidden');
                grupoK.classList.remove('hidden');
            } else if (tipo === 'pago') {
                grupoMonto.classList.add('hidden');
                grupoVP.classList.remove('hidden');
                grupoN.classList.remove('hidden');
                grupoK.classList.remove('hidden');
            } else if (tipo === 'numero_pagos') {
                grupoMonto.classList.remove('hidden');
                grupoVP.classList.remove('hidden');
                grupoN.classList.add('hidden');
                grupoK.classList.remove('hidden');
            } else if (tipo === 'periodos_diferidos') {
                grupoMonto.classList.remove('hidden');
                grupoVP.classList.remove('hidden');
                grupoN.classList.remove('hidden');
                grupoK.classList.add('hidden');
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
