@extends('layouts.app')

@section('title', 'Documentación - Anualidades Diferidas')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="card-title mb-3">Documentación del proyecto</h1>
                    <p class="lead">
                        Este proyecto fue desarrollado por un grupo universitario de la
                        <strong>Universidad de El Salvador</strong> como apoyo didáctico
                        para el estudio de las <strong>anualidades diferidas</strong> en Ingeniería Económica.
                    </p>
                    <h2 class="h5 mt-4">Objetivo</h2>
                    <p>
                        Ofrecer una herramienta sencilla y visual que permita comprender
                        cómo se valoran las anualidades diferidas y cómo influyen la tasa de interés,
                        la duración de la renta y el periodo de diferimiento en el valor del dinero en el tiempo.
                    </p>
                    <h2 class="h5 mt-4">Integrantes del grupo</h2>
                    <p>
                        Aquí puedes colocar los nombres de los integrantes de tu grupo, carrera y universidad, por ejemplo:
                    </p>
                    <ul>
                        <li>Integrante 1 – Ingeniería __________</li>
                        <li>Integrante 2 – Ingeniería __________</li>
                        <li>Integrante 3 – Ingeniería __________</li>
                        <li>Integrante 4 – Ingeniería __________</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">¿Qué es una anualidad diferida?</h2>
                    <p>
                        Una <strong>anualidad</strong> es una serie de pagos iguales que se realizan a intervalos
                        de tiempo iguales (por ejemplo, mensual, bimestral, semestral o anual).
                    </p>
                    <p>
                        Una <strong>anualidad diferida</strong> es aquella en la que los pagos no inician de inmediato,
                        sino después de un cierto número de periodos de espera o <em>diferimiento</em>. Es decir,
                        la operación se formaliza hoy, pero los pagos empiezan más adelante.
                    </p>

                    <h3 class="h5 mt-4">Parámetros principales</h3>
                    <ul>
                        <li><strong>R</strong>: monto de cada pago periódico (renta).</li>
                        <li><strong>i</strong>: tasa de interés por periodo, en forma decimal.</li>
                        <li><strong>n</strong>: número de pagos de la anualidad.</li>
                        <li><strong>k</strong>: número de periodos de diferimiento antes del primer pago.</li>
                        <li><strong>VP</strong>: valor presente, equivalente al tiempo 0.</li>
                        <li><strong>VF</strong>: valor futuro, equivalente al final del último pago.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">Fórmulas principales (anualidad vencida diferida)</h2>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h3 class="h6 text-uppercase text-muted">Valor presente de una anualidad ordinaria</h3>
                                <p class="mb-2">
                                    Para una anualidad vencida <em>sin diferimiento</em> (pagos al final de cada periodo):
                                </p>
                                <p class="display-6 fs-5 text-center mb-2">
                                    <span class="fw-semibold">VP<sub>anualidad</sub></span>
                                    =
                                    <span class="fw-semibold">R</span>
                                    ·
                                    <span class="d-inline-block align-middle">
                                        <span class="border-bottom d-block px-1">
                                            1 − (1 + i)<sup>−n</sup>
                                        </span>
                                        <span class="d-block text-center">i</span>
                                    </span>
                                </p>
                                <p class="text-muted small mb-0">
                                    Esta expresión da el valor presente en el momento del <strong>primer pago</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h3 class="h6 text-uppercase text-muted">Valor presente de anualidad diferida</h3>
                                <p class="mb-2">
                                    Si los pagos empiezan después de <strong>k</strong> periodos de diferimiento:
                                </p>
                                <p class="display-6 fs-5 text-center mb-2">
                                    <span class="fw-semibold">VP</span>
                                    =
                                    <span class="fw-semibold">R</span>
                                    ·
                                    <span class="d-inline-block align-middle">
                                        <span class="border-bottom d-block px-1">
                                            1 − (1 + i)<sup>−n</sup>
                                        </span>
                                        <span class="d-block text-center">i</span>
                                    </span>
                                    · (1 + i)<sup>−k</sup>
                                </p>
                                <p class="text-muted small mb-0">
                                    Se descuenta la anualidad <em>n</em> periodos hasta el momento del primer pago
                                    y luego se descuenta <em>k</em> periodos más hasta el tiempo 0.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h3 class="h6 text-uppercase text-muted">Valor futuro de una anualidad ordinaria</h3>
                                <p class="mb-2">
                                    Valor equivalente al final del último pago (momento n):
                                </p>
                                <p class="display-6 fs-5 text-center mb-2">
                                    <span class="fw-semibold">VF</span>
                                    =
                                    <span class="fw-semibold">R</span>
                                    ·
                                    <span class="d-inline-block align-middle">
                                        <span class="border-bottom d-block px-1">
                                            (1 + i)<sup>n</sup> − 1
                                        </span>
                                        <span class="d-block text-center">i</span>
                                    </span>
                                </p>
                                <p class="text-muted small mb-0">
                                    El diferimiento no afecta el monto al final del último pago, solo el valor presente.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h3 class="h6 text-uppercase text-muted">Formas despejadas que usa la calculadora</h3>
                                <p class="small mb-1">
                                    A partir de las expresiones anteriores, la calculadora también resuelve:
                                </p>
                                <ul class="small mb-0">
                                    <li>
                                        <strong>R</strong> a partir de un valor presente conocido:
                                        <br>
                                        <span class="d-inline-block">
                                            R =
                                            VP ÷
                                            [
                                            <span class="d-inline-block align-middle">
                                                <span class="border-bottom d-block px-1">
                                                    1 − (1 + i)<sup>−n</sup>
                                                </span>
                                                <span class="d-block text-center">i</span>
                                            </span>
                                            · (1 + i)<sup>−k</sup>
                                            ]
                                        </span>
                                    </li>
                                    <li>
                                        <strong>n</strong> a partir de VP, R, i y k (usando logaritmos).
                                    </li>
                                    <li>
                                        <strong>k</strong> a partir de VP, R, i y n (usando logaritmos).
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">Cómo usar la calculadora de la aplicación</h2>
                    <ol class="mb-3">
                        <li>En el menú superior, haz clic en <strong>Calculadora</strong>.</li>
                        <li>Selecciona el <strong>tipo de cálculo</strong>:
                            <ul>
                                <li><em>Calcular VP y VF</em> (conocidos R, i, n, k).</li>
                                <li><em>Calcular R</em> (conocidos VP, i, n, k).</li>
                                <li><em>Calcular n</em> (conocidos VP, R, i, k).</li>
                                <li><em>Calcular k</em> (conocidos VP, R, i, n).</li>
                            </ul>
                        </li>
                        <li>Introduce los datos en los campos correspondientes (los que no se necesitan se ocultan).</li>
                        <li>Puedes usar los botones de <strong>“Ejemplos rápidos”</strong> para cargar datos de un ejercicio típico.</li>
                        <li>Presiona <strong>Calcular</strong> y revisa la sección de resultados.</li>
                    </ol>
                    <p class="text-muted mb-0">
                        La calculadora trabaja con tasas por periodo:
                        si la tasa es anual capitalizable mensualmente, primero se divide entre 12 para obtener la tasa mensual.
                    </p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">Ejemplos guía utilizados en la calculadora</h2>

                    <h3 class="h6 mt-3">Ejemplo 1 – Plan “Compre ahora y pague después”</h3>
                    <p class="mb-1">
                        Un arquitecto recibe hoy un escritorio y lo paga con 12 mensualidades de 180 que empiezan
                        dentro de 12 meses. Tasa 36 % anual convertible mensualmente.
                    </p>
                    <ul class="small mb-2">
                        <li>Datos en la calculadora: R = 180, i = 3 % mensual, n = 12, k = 12.</li>
                        <li>Tipo de cálculo: <strong>Calcular VP y VF</strong>.</li>
                        <li>Resultado esperado: VP ≈ <strong>1,256.68</strong>.</li>
                    </ul>

                    <h3 class="h6 mt-3">Ejemplo 2 – Renta semestral diferida</h3>
                    <p class="mb-1">
                        Renta de 6,000 cada semestre durante 7 años; el primer pago se realiza dentro de 3 años.
                        Tasa 17 % semestral.
                    </p>
                    <ul class="small mb-2">
                        <li>Datos: R = 6,000; i = 17 % por semestre; n = 14; k = 6 semestres.</li>
                        <li>Tipo de cálculo: <strong>Calcular VP y VF</strong>.</li>
                        <li>Valor actual: VP ≈ <strong>12,231.50</strong>. Valor futuro: VF ≈ <strong>282,616.03</strong>.</li>
                    </ul>

                    <h3 class="h6 mt-3">Ejemplo 3 – Fondo de inversión con retiros futuros</h3>
                    <p class="mb-1">
                        El 14 de mayo del año 1 se depositan 100,000 en un fondo. Se desean 10 retiros mensuales
                        a partir del 14 de febrero del año 3. Tasa 17.52 % anual capitalizable mensualmente.
                    </p>
                    <ul class="small mb-2">
                        <li>Períodos: de mayo del año 1 a febrero del año 3 hay 21 meses → k = 21.</li>
                        <li>Datos: VP = 100,000; i ≈ 1.46 % mensual; n = 10; k = 21.</li>
                        <li>Tipo de cálculo: <strong>Calcular R</strong> (monto de cada retiro).</li>
                        <li>Resultado esperado: R ≈ <strong>14,670.25</strong>.</li>
                    </ul>

                    <h3 class="h6 mt-3">Ejemplo 4 – Número de retiros empezando dentro de 6 meses</h3>
                    <p class="mb-1">
                        Hoy se depositan 8,000 en una cuenta al 6 % anual capitalizable mensualmente.
                        Se harán retiros mensuales de 500, comenzando dentro de 6 meses.
                    </p>
                    <ul class="small mb-2">
                        <li>Datos: VP = 8,000; R = 500; i = 0.5 % mensual; k = 6.</li>
                        <li>Tipo de cálculo: <strong>Calcular n</strong> (número de retiros).</li>
                        <li>Resultado: n ≈ 17.25 → se pueden hacer <strong>17 retiros completos</strong>.</li>
                    </ul>

                    <p class="text-muted small mb-0">
                        Otros problemas de anualidades diferidas pueden modelarse de forma similar ajustando los
                        valores de R, i, n y k y eligiendo el tipo de cálculo adecuado.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

