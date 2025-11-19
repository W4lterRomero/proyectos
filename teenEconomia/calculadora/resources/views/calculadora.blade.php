@extends('layouts.app')

@section('title', 'Calculadora - Anualidades diferidas')

@section('content')
    <div class="py-4">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Encabezado -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
                <div class="px-6 sm:px-8 py-6 border-b border-slate-100 bg-slate-50">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900 mb-1">
                        Calculadora de anualidades diferidas
                    </h1>
                    <p class="text-sm text-slate-600">
                        Calcula Capital, Monto, Renta n o k según los datos del ejercicio.
                    </p>
                </div>

                <div class="px-6 sm:px-8 py-6 space-y-3">
                    <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                        Esta calculadora permite trabajar con <strong>anualidades diferidas</strong>:
                        puedes obtener el <strong>capital o valor presente (C)</strong>, el <strong>monto o valor futuro (M)</strong>,
                        el <strong>monto de cada pago (R)</strong>, el <strong>número de pagos (n)</strong> o los
                        <strong>periodos de diferimiento (k)</strong>, según la información que tengas.
                    </p>
                    <p class="text-gray-600 text-xs md:text-sm">
                        En todos los casos se asume una anualidad vencida (pagos al final de cada periodo) con tasa
                        de interés constante por periodo.
                    </p>
                </div>
            </div>

            <!-- Formulario principal -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
                <form id="form-anualidad" method="POST" action="{{ route('calculadora.calcular') }}" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <!-- Tipo de cálculo -->
                    <div>
                        <label for="tipo_calculo" class="block  font-semibold text-gray-800 mb-2 inline-block bg-blue-600 text-white text-xs font-bold px-2.5 py-0.5 rounded-full mr-2">
                                Tipo de cálculo
                            </label>
                        <div class="rounded-xl p-4 border border-slate-200 bg-slate-50 mb-4">

                            @php
                                $tipoSeleccionado = old('tipo_calculo', $entradas['tipo_calculo'] ?? 'capital');
                            @endphp
                            <select
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white text-gray-800"
                                id="tipo_calculo"
                                name="tipo_calculo"
                            >
                                <option value="capital" {{ $tipoSeleccionado === 'capital' ? 'selected' : '' }}>
                                    Calcular capital C
                                </option>
                                <option value="monto" {{ $tipoSeleccionado === 'monto' ? 'selected' : '' }}>
                                    Calcular monto M
                                </option>
                                <option value="pago" {{ $tipoSeleccionado === 'pago' ? 'selected' : '' }}>
                                    Calcular monto de cada pago R
                                </option>
                                <option value="numero_pagos" {{ $tipoSeleccionado === 'numero_pagos' ? 'selected' : '' }}>
                                    Calcular número de pagos n
                                </option>
                                <option value="periodos_diferidos" {{ $tipoSeleccionado === 'periodos_diferidos' ? 'selected' : '' }}>
                                    Calcular periodos diferidos k
                                </option>
                            </select>
                            <p class="text-xs text-gray-600 mt-2">
                                Selecciona qué variable quieres obtener como resultado principal.
                            </p>
                        </div>
                    </div>

                    <!-- Ejemplos rápidos -->
                    <div class="pb-6 border-b border-gray-200">
                        <label class="block text-sm font-semibold text-gray-800 mb-3">
                            <span class="inline-block bg-emerald-600 text-white text-xs font-bold px-2.5 py-0.5 rounded-full mr-2">ATAJOS</span>
                            Cargar ejemplos rápidos
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <button
                                type="button"
                                class="px-4 py-2.5 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-emerald-600 transition text-sm"
                                onclick="aplicarEjemplo(1)"
                            >
                                Compra a crédito diferida
                            </button>
                            <button
                                type="button"
                                class="px-4 py-2.5 bg-cyan-600 text-white rounded-lg font-medium hover:bg-cyan-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-cyan-600 transition text-sm"
                                onclick="aplicarEjemplo(2)"
                            >
                                Renta semestral diferida
                            </button>
                            <button
                                type="button"
                                class="px-4 py-2.5 inline-flex items-center gap-2 rounded-full border border-violet-500 bg-white text-violet-700 font-semibold shadow-sm hover:bg-violet-50 hover:text-violet-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-violet-500 transition text-sm"
                                onclick="aplicarEjemplo(3)"
                            >
                                <span class="text-xs">★</span>
                                <span>Fondo con retiros futuros</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">
                            Cada ejemplo rellena el formulario con un caso típico explicado en la documentación.
                        </p>
                    </div>

                    <!-- Campos del formulario -->
                    <p class="text-xs text-gray-600">
                        Completa solo los campos visibles; los demas se calculan segun el tipo de calculo elegido.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                                Pago periódico en cada período.
                            </p>
                            @error('monto_pago')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Valor Presente -->
                        <div class="campo-grupo" id="grupo_valor_presente">
                            <label for="valor_presente" class="block text-sm font-semibold text-gray-800 mb-2">
                                Capital (C)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('valor_presente') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                                id="valor_presente"
                                name="valor_presente"
                                value="{{ old('valor_presente', $entradas['valor_presente'] ?? '') }}"
                                placeholder="0.00"
                            >
                            <p class="text-xs text-gray-600 mt-1.5">
                                Valor equivalente en el tiempo 0.
                            </p>
                            @error('valor_presente')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tasa de interés -->
                        <div>
                            <label for="tasa_interes" class="block text-sm font-semibold text-gray-800 mb-2">
                                Tasa de interés (%)
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input
                                    type="number"
                                    step="0.0001"
                                    min="0"
                                    class="w-full sm:w-1/2 px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('tasa_interes') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                                    id="tasa_interes"
                                    name="tasa_interes"
                                    value="{{ old('tasa_interes', $entradas['tasa_interes'] ?? '') }}"
                                    placeholder="0.00"
                                >
                                <x-tipo-tasa-select
                                    :selected="old('tipo_tasa', $entradas['tipo_tasa'] ?? 'por_periodo')"
                                    :opciones="$opcionesTipoTasa ?? null"
                                />
                            </div>
                            <p class="text-xs text-gray-600 mt-1.5">
                                Ingresa la tasa en porcentaje. Si eliges anual convertible mensual, dentro se ajusta a la tasa por periodo.
                            </p>
                            @error('tasa_interes')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Número de pagos -->
                        <div class="campo-grupo" id="grupo_numero_pagos">
                            <label for="numero_pagos" class="block text-sm font-semibold text-gray-800 mb-2">
                                Número de pagos (n)
                            </label>
                            <input
                                type="number"
                                step="1"
                                min="1"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('numero_pagos') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                                id="numero_pagos"
                                name="numero_pagos"
                                value="{{ old('numero_pagos', $entradas['numero_pagos'] ?? '') }}"
                                placeholder="0"
                            >
                            <p class="text-xs text-gray-600 mt-1.5">
                                Cuántos pagos iguales tendrá la renta.
                            </p>
                            @error('numero_pagos')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Periodos de diferimiento -->
                        <div class="campo-grupo" id="grupo_periodos_diferidos">
                            <label for="periodos_diferidos" class="block text-sm font-semibold text-gray-800 mb-2">
                                Periodos de diferimiento (k)
                            </label>
                            <input
                                type="number"
                                step="1"
                                min="0"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('periodos_diferidos') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                                id="periodos_diferidos"
                                name="periodos_diferidos"
                                value="{{ old('periodos_diferidos', $entradas['periodos_diferidos'] ?? '') }}"
                                placeholder="0"
                            >
                            <p class="text-xs text-gray-600 mt-1.5">
                                Número de periodos que se esperan antes del primer pago.
                            </p>
                            @error('periodos_diferidos')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-row flex-wrap gap-3 justify-end">
                        <button
                            type="submit"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-600 transition"
                        >
                            Calcular
                        </button>
                        <button
                            type="button"
                            onclick="limpiarFormulario()"
                            class="flex-1 px-6 py-3 border border-red-500 text-red-600 rounded-lg font-semibold bg-white hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-500 transition"
                        >
                            Limpiar
                        </button>
                    </div>

                    <!-- Resultados -->
                    @if(isset($resultado) && is_array($resultado))
                        <div id="resultado-card" class="mt-8 bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-3">
                            <h2 class="text-sm font-semibold text-slate-900 mb-1">Resultado principal</h2>
                            <p class="text-xs text-slate-600 mb-2">
                                Aqui veras la variable que elegiste calcular y, mas abajo, los pasos usados en el procedimiento.
                            </p>

                            @php
                                $modo = $resultado['modo'] ?? null;
                                $tasaDetalle = $resultado['tasa_detalle'] ?? null;
                                $pasos = $resultado['pasos'] ?? [];
                            @endphp

                            @if($tasaDetalle)
                                <div class="text-xs text-slate-700 bg-white border border-slate-200 rounded-lg p-3">
                                    <p class="font-semibold text-slate-900 mb-1">Tasa utilizada</p>
                                    <p>Tipo: {{ $tasaDetalle['descripcion'] ?? '' }}</p>
                                    <p>Tasa ingresada: {{ $tasaDetalle['tasa_ingresada'] ?? '' }} %</p>
                                    <p>Tasa por periodo: {{ $tasaDetalle['tasa_periodo'] ?? '' }} %</p>
                                </div>
                            @endif

                            @if($modo === 'capital')
                                <div class="bg-white rounded-lg border border-slate-200 p-3 text-sm">
                                    <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Capital (C)</p>
                                    <p class="text-lg font-semibold text-blue-700">
                                        ${{ number_format($resultado['valor_presente'] ?? 0, 2) }}
                                    </p>
                                </div>
                            @elseif($modo === 'monto')
                                <div class="bg-white rounded-lg border border-slate-200 p-3 text-sm">
                                    <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Monto (M)</p>
                                    <p class="text-lg font-semibold text-indigo-700">
                                        ${{ number_format($resultado['valor_futuro'] ?? 0, 2) }}
                                    </p>
                                </div>
                            @elseif($modo === 'pago')
                                <div class="bg-white rounded-lg border border-slate-200 p-3 text-sm">
                                    <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Monto de cada pago (R)</p>
                                    <p class="text-lg font-semibold text-emerald-700">
                                        ${{ number_format($resultado['monto_pago'] ?? 0, 2) }}
                                    </p>
                                </div>
                            @elseif($modo === 'numero_pagos')
                                <div class="bg-white rounded-lg border border-slate-200 p-3 text-sm space-y-1">
                                    <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Número de pagos</p>
                                    <p class="text-lg font-semibold text-cyan-700">
                                        {{ isset($resultado['numero_pagos']) ? number_format($resultado['numero_pagos'], 4) : '' }}
                                    </p>
                                    @if(isset($resultado['numero_pagos_entero']))
                                        <p class="text-xs text-slate-600">
                                            Aproximación entera: {{ $resultado['numero_pagos_entero'] }}
                                        </p>
                                    @endif
                                </div>
                            @elseif($modo === 'periodos_diferidos')
                                <div class="bg-white rounded-lg border border-slate-200 p-3 text-sm space-y-1">
                                    <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Períodos diferidos (k)</p>
                                    <p class="text-lg font-semibold text-purple-700">
                                        {{ isset($resultado['periodos_diferidos']) ? number_format($resultado['periodos_diferidos'], 4) : '' }}
                                    </p>
                                    @if(isset($resultado['periodos_diferidos_entero']))
                                        <p class="text-xs text-slate-600">
                                            Aproximación entera: {{ $resultado['periodos_diferidos_entero'] }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if(!empty($pasos))
                                <div class="border-t border-slate-200 pt-3 mt-2">
                                    <p class="text-xs font-semibold text-slate-900 mb-2">Pasos del cálculo</p>
                                    <div class="text-xs text-slate-700 leading-relaxed space-y-2 break-words whitespace-normal overflow-x-auto">
                                        @foreach($pasos as $paso)
                                            <p>{!! $paso !!}</p>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div id="resultado-card" class="mt-8 hidden bg-slate-50 border border-slate-200 rounded-xl p-6"></div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script>
        function aplicarEjemplo(n) {
            const tipoCalculo = document.getElementById('tipo_calculo');
            const montoPago = document.getElementById('monto_pago');
            const valorPresente = document.getElementById('valor_presente');
            const tasaInteres = document.getElementById('tasa_interes');
            const tipoTasa = document.getElementById('tipo_tasa');
            const numeroPagos = document.getElementById('numero_pagos');
            const periodosDiferidos = document.getElementById('periodos_diferidos');

            if (!tipoCalculo || !montoPago || !tasaInteres || !tipoTasa || !numeroPagos || !periodosDiferidos) {
                return;
            }

            if (n === 1) {
                // Ejemplo 1 - Compra a crédito diferida (calcular capital)
                tipoCalculo.value = 'capital';
                montoPago.value = 180;
                tasaInteres.value = 36;
                tipoTasa.value = 'anual_mensual';
                numeroPagos.value = 12;
                periodosDiferidos.value = 12;
            } else if (n === 2) {
                // Ejemplo 2 - Renta semestral diferida (calcular monto)
                tipoCalculo.value = 'monto';
                montoPago.value = 6000;
                tasaInteres.value = 17;
                tipoTasa.value = 'por_periodo'; // 17 % por semestre
                numeroPagos.value = 14;
                periodosDiferidos.value = 0;
            } else if (n === 3) {
                // Ejemplo 3 - Fondo de inversión con retiros futuros
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
                tipoCalculo.value = 'capital';
            }

            const tipoTasa = document.getElementById('tipo_tasa');
            if (tipoTasa) {
                tipoTasa.value = 'por_periodo';
            }

            const resultadoCard = document.getElementById('resultado-card');
            if (resultadoCard) {
                resultadoCard.style.display = 'none';
                resultadoCard.innerHTML = '';
            }

            actualizarTipoCalculo();
        }

        function actualizarTipoCalculo() {
            const tipo = document.getElementById('tipo_calculo')?.value;
            const grupoMonto = document.getElementById('grupo_monto_pago');
            const grupoVP = document.getElementById('grupo_valor_presente');
            const grupoN = document.getElementById('grupo_numero_pagos');
            const grupoK = document.getElementById('grupo_periodos_diferidos');

            if (!tipo || !grupoMonto || !grupoVP || !grupoN || !grupoK) {
                return;
            }

            if (tipo === 'capital') {
                // Calcular Capital: necesita R, i, n, k
                grupoMonto.classList.remove('hidden');
                grupoVP.classList.add('hidden');
                grupoN.classList.remove('hidden');
                grupoK.classList.remove('hidden');
            } else if (tipo === 'monto') {
                // Calcular Monto: necesita R, i, n (NO necesita k)
                grupoMonto.classList.remove('hidden');
                grupoVP.classList.add('hidden');
                grupoN.classList.remove('hidden');
                grupoK.classList.add('hidden');
            } else if (tipo === 'pago') {
                // Calcular Pago: necesita C, i, n, k
                grupoMonto.classList.add('hidden');
                grupoVP.classList.remove('hidden');
                grupoN.classList.remove('hidden');
                grupoK.classList.remove('hidden');
            } else if (tipo === 'numero_pagos') {
                // Calcular Número de Pagos: necesita C, R, i, k
                grupoMonto.classList.remove('hidden');
                grupoVP.classList.remove('hidden');
                grupoN.classList.add('hidden');
                grupoK.classList.remove('hidden');
            } else if (tipo === 'periodos_diferidos') {
                // Calcular Períodos Diferidos: necesita C, R, i, n
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
