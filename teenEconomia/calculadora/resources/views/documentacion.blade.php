@extends('layouts.app')

@section('title', 'Documentación - Anualidades diferidas')

@section('content')
    <div class="py-4">
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Sección de Introducción -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
                <div class="px-6 sm:px-8 py-6 border-b border-slate-100 bg-slate-50">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900 mb-1">Documentación del proyecto</h1>
                    <p class="text-sm text-slate-600">
                        Resumen teórico que acompaña la calculadora de anualidades diferidas.
                    </p>
                </div>
                <div class="px-6 sm:px-8 py-8">
                    <p class="text-base text-slate-700 leading-relaxed mb-6">
                        Este proyecto fue desarrollado por un grupo de estudiantes de la
                        <span class="font-semibold text-blue-600">Universidad de El Salvador</span> como apoyo didáctico
                        para el estudio de las <span class="font-semibold text-indigo-600">anualidades diferidas</span> en
                        la asignatura de Ingeniería Económica.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-800">
                            <h2 class="text-base font-semibold text-slate-900 mb-2 flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">1</span>
                                Objetivo del proyecto
                            </h2>
                            <p>
                                Ofrecer una herramienta sencilla para practicar ejercicios de anualidades diferidas y
                                revisar rápidamente las fórmulas básicas sin necesidad de un libro al lado.
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-800">
                            <h2 class="text-base font-semibold text-slate-900 mb-2 flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">2</span>
                                Integrantes
                            </h2>
                            <p class="mb-2">Integrantes del grupo</p>
                            <ul class="space-y-1">
                                <li>• Gerson Mauricio Alegría Caballero</li>
                                <li>• Pedro David Ramos García</li>
                                <li>• Fabiola Alexandra Romero Amaya</li>
                                <li>• David Elías Romero Claros</li>
                                <li>• Walter Bryan Romero Hernández</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Conceptos -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 space-y-5">
                <h2 class="text-xl font-semibold text-slate-900">
                    ¿Qué es una anualidad diferida?
                </h2>
                <p class="text-sm text-slate-700 leading-relaxed">
                    Es una renta con pagos iguales y periódicos, pero que no empieza a pagarse desde el tiempo 1,
                    sino después de cierto número de periodos de espera (k). Durante ese tiempo el capital “está
                    quieto”, pero sigue generando intereses.
                </p>

                <div class="grid md:grid-cols-2 gap-4 text-sm text-slate-700">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-blue-200 hover:bg-blue-50/60 cursor-default">
                        <p class="font-semibold text-slate-900 mb-1">R (renta o pago)</p>
                        <p>Monto que se paga en cada periodo de la anualidad.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-blue-200 hover:bg-blue-50/60 cursor-default">
                        <p class="font-semibold text-slate-900 mb-1">i (tasa de interés)</p>
                        <p>Tasa por periodo de la anualidad (ya ajustada si es necesario).</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-blue-200 hover:bg-blue-50/60 cursor-default">
                        <p class="font-semibold text-slate-900 mb-1">n (número de pagos)</p>
                        <p>Cantidad total de pagos de la renta.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-blue-200 hover:bg-blue-50/60 cursor-default">
                        <p class="font-semibold text-slate-900 mb-1">k (periodos de diferimiento)</p>
                        <p>Número de periodos que se esperan antes de que comiencen los pagos.</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Fórmulas detalladas -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 space-y-5">
                <h2 class="text-xl font-semibold text-slate-900">
                    Fórmulas de anualidades diferidas
                </h2>
                <p class="text-sm text-slate-700">
                    Usamos la siguiente notación: \(R\) (renta o pago), \(C\) (capital o valor presente),
                    \(M\) (monto o valor futuro), \(i\) (tasa por periodo), \(n\) (número de pagos) y \(k\) (periodos de diferimiento).
                </p>

                <div class="grid md:grid-cols-2 gap-4 text-sm text-slate-700">
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <p class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold">C</span>
                            Capital (Valor Presente)
                        </p>
                        <p class="text-xs text-slate-600 mb-2">Valor presente en el tiempo 0 con diferimiento:</p>
                        <div class="bg-white rounded px-3 py-2 border border-blue-200">
                            \[ C = R \cdot \frac{1 - (1 + i)^{-n}}{i} \cdot (1 + i)^{-k} \]
                        </div>
                        <p class="text-xs text-slate-600 mt-2 italic">
                            <strong>Requiere:</strong> R, i, n, k
                        </p>
                    </div>

                    <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                        <p class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-white text-xs font-bold">M</span>
                            Monto (Valor Futuro)
                        </p>
                        <p class="text-xs text-slate-600 mb-2">Valor acumulado al final de los pagos:</p>
                        <div class="bg-white rounded px-3 py-2 border border-indigo-200">
                            \[ M = R \cdot \frac{(1 + i)^{n} - 1}{i} \]
                        </div>
                        <p class="text-xs text-slate-600 mt-2 italic">
                            <strong>Requiere:</strong> R, i, n (NO necesita k)
                        </p>
                    </div>

                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-white text-xs font-bold">R</span>
                            Renta (Pago periódico)
                        </p>
                        <p class="text-xs text-slate-600 mb-2">Despejando \(R\) del capital diferido:</p>
                        <div class="bg-white rounded px-3 py-2 border border-emerald-200">
                            \[ R = C \cdot (1 + i)^{k} \cdot \frac{i}{1 - (1 + i)^{-n}} \]
                        </div>
                        <p class="text-xs text-slate-600 mt-2 italic">
                            <strong>Requiere:</strong> C, i, n, k
                        </p>
                    </div>

                    <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4">
                        <p class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-cyan-600 text-white text-xs font-bold">n</span>
                            Número de pagos
                        </p>
                        <p class="text-xs text-slate-600 mb-2">Despejando con logaritmos:</p>
                        <div class="bg-white rounded px-3 py-2 border border-cyan-200">
                            \[ n = - \frac{\ln\left( 1 - \dfrac{C(1+i)^k \cdot i}{R} \right)}{\ln(1 + i)} \]
                        </div>
                        <p class="text-xs text-slate-600 mt-2 italic">
                            <strong>Requiere:</strong> C, R, i, k
                        </p>
                    </div>

                    <div class="rounded-xl border border-purple-200 bg-purple-50 p-4">
                        <p class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-purple-600 text-white text-xs font-bold">k</span>
                            Períodos diferidos
                        </p>
                        <p class="text-xs text-slate-600 mb-2">A partir de la fórmula de \(C\):</p>
                        <div class="bg-white rounded px-3 py-2 border border-purple-200">
                            \[ k = - \frac{\ln\left( \dfrac{C \cdot i}{R \cdot (1 - (1 + i)^{-n})} \right)}{\ln(1 + i)} \]
                        </div>
                        <p class="text-xs text-slate-600 mt-2 italic">
                            <strong>Requiere:</strong> C, R, i, n
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sección de Ejemplos Prácticos -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 space-y-5">
                <h2 class="text-xl font-semibold text-slate-900">
                    Ejemplos prácticos
                </h2>
                <p class="text-sm text-slate-700 mb-4">
                    A continuación se presentan tres ejemplos típicos de anualidades diferidas que puedes resolver con la calculadora.
                </p>

                <!-- Ejemplo 1 -->
                <div class="rounded-xl border-2 border-blue-200 bg-blue-50 p-5 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-bold flex-shrink-0">1</span>
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-900 mb-2">Compra de un vehículo a crédito diferido</h3>
                            <p class="text-sm text-slate-700 mb-3">
                                Una persona compra un vehículo y acuerda pagar $180 mensuales durante 12 meses,
                                pero el primer pago se realizará dentro de 12 meses (un año de gracia).
                                La tasa de interés es del 36% anual convertible mensualmente.
                            </p>
                            <div class="bg-white rounded-lg p-4 border border-blue-300">
                                <p class="text-xs font-semibold text-slate-900 mb-2">Datos para la calculadora:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div><strong>Tipo de cálculo:</strong> Calcular capital C</div>
                                    <div><strong>R (Renta):</strong> $180</div>
                                    <div><strong>i (Tasa):</strong> 36% anual convertible mensual</div>
                                    <div><strong>n (Pagos):</strong> 12</div>
                                    <div><strong>k (Diferimiento):</strong> 12 meses</div>
                                </div>
                                <p class="text-xs text-blue-700 mt-3 font-semibold">
                                    → Esto calcula cuánto vale el vehículo hoy (capital C)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ejemplo 2 -->
                <div class="rounded-xl border-2 border-indigo-200 bg-indigo-50 p-5 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-white text-sm font-bold flex-shrink-0">2</span>
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-900 mb-2">Renta semestral futura</h3>
                            <p class="text-sm text-slate-700 mb-3">
                                Se realizarán 14 pagos semestrales de $6,000 cada uno, con una tasa del 17% semestral.
                                ¿Cuál será el monto acumulado al final de todos los pagos?
                            </p>
                            <div class="bg-white rounded-lg p-4 border border-indigo-300">
                                <p class="text-xs font-semibold text-slate-900 mb-2">Datos para la calculadora:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div><strong>Tipo de cálculo:</strong> Calcular monto M</div>
                                    <div><strong>R (Renta):</strong> $6,000</div>
                                    <div><strong>i (Tasa):</strong> 17% por periodo</div>
                                    <div><strong>n (Pagos):</strong> 14</div>
                                    <div class="col-span-2"><strong>k (Diferimiento):</strong> No se requiere para calcular M</div>
                                </div>
                                <p class="text-xs text-indigo-700 mt-3 font-semibold">
                                    → Esto calcula el valor futuro acumulado (monto M)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ejemplo 3 -->
                <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50 p-5 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white text-sm font-bold flex-shrink-0">3</span>
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-900 mb-2">Fondo de inversión con retiros diferidos</h3>
                            <p class="text-sm text-slate-700 mb-3">
                                Se invierte un capital de $100,000 hoy, y se planea realizar 10 retiros mensuales iguales
                                dentro de 21 meses. La tasa es del 17.52% anual convertible mensualmente.
                                ¿Cuánto se puede retirar cada mes?
                            </p>
                            <div class="bg-white rounded-lg p-4 border border-emerald-300">
                                <p class="text-xs font-semibold text-slate-900 mb-2">Datos para la calculadora:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div><strong>Tipo de cálculo:</strong> Calcular pago R</div>
                                    <div><strong>C (Capital):</strong> $100,000</div>
                                    <div><strong>i (Tasa):</strong> 17.52% anual convertible mensual</div>
                                    <div><strong>n (Pagos):</strong> 10</div>
                                    <div><strong>k (Diferimiento):</strong> 21 meses</div>
                                </div>
                                <p class="text-xs text-emerald-700 mt-3 font-semibold">
                                    → Esto calcula cuánto se puede retirar mensualmente (renta R)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ejemplo 4 -->
                <div class="rounded-xl border-2 border-cyan-200 bg-cyan-50 p-5 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-cyan-600 text-white text-sm font-bold flex-shrink-0">4</span>
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-900 mb-2">Calcular el número de pagos</h3>
                            <p class="text-sm text-slate-700 mb-3">
                                Se tiene un capital de $50,000 y se desea saber cuántos pagos mensuales de $3,500
                                se pueden realizar, si el primer pago será dentro de 6 meses y la tasa es del
                                24% anual convertible mensualmente.
                            </p>
                            <div class="bg-white rounded-lg p-4 border border-cyan-300">
                                <p class="text-xs font-semibold text-slate-900 mb-2">Datos para la calculadora:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div><strong>Tipo de cálculo:</strong> Calcular número de pagos n</div>
                                    <div><strong>C (Capital):</strong> $50,000</div>
                                    <div><strong>R (Renta):</strong> $3,500</div>
                                    <div><strong>i (Tasa):</strong> 24% anual convertible mensual</div>
                                    <div><strong>k (Diferimiento):</strong> 6 meses</div>
                                </div>
                                <p class="text-xs text-cyan-700 mt-3 font-semibold">
                                    → Esto calcula cuántos pagos se pueden realizar (n)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ejemplo 5 -->
                <div class="rounded-xl border-2 border-purple-200 bg-purple-50 p-5 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-purple-600 text-white text-sm font-bold flex-shrink-0">5</span>
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-900 mb-2">Calcular el período de diferimiento</h3>
                            <p class="text-sm text-slate-700 mb-3">
                                Un préstamo de $80,000 se pagará con 24 mensualidades de $5,200, con una tasa del
                                30% anual convertible mensualmente. ¿Después de cuántos meses debe iniciar el primer pago?
                            </p>
                            <div class="bg-white rounded-lg p-4 border border-purple-300">
                                <p class="text-xs font-semibold text-slate-900 mb-2">Datos para la calculadora:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div><strong>Tipo de cálculo:</strong> Calcular períodos diferidos k</div>
                                    <div><strong>C (Capital):</strong> $80,000</div>
                                    <div><strong>R (Renta):</strong> $5,200</div>
                                    <div><strong>i (Tasa):</strong> 30% anual convertible mensual</div>
                                    <div><strong>n (Pagos):</strong> 24</div>
                                </div>
                                <p class="text-xs text-purple-700 mt-3 font-semibold">
                                    → Esto calcula cuántos meses de gracia hay (k)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
