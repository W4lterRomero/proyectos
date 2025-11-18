@extends('layouts.app')

@section('title', 'Documentación - Anualidades Diferidas')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="card-title mb-3">Documentación del proyecto</h1>
                    <p class="lead">
                        Este proyecto fue desarrollado por un grupo universitario como apoyo didáctico
                        para el estudio de las <strong>anualidades diferidas</strong> en matemáticas financieras.
                    </p>
                    <h2 class="h5 mt-4">Objetivo</h2>
                    <p>
                        El objetivo principal es ofrecer una herramienta sencilla que permita comprender
                        cómo se valoran las anualidades diferidas y cómo influyen la tasa de interés, la duración
                        y el periodo de diferimiento en el valor del dinero en el tiempo.
                    </p>
                    <h2 class="h5 mt-4">Integrantes del grupo</h2>
                    <p>
                        Aquí puedes colocar los nombres de los integrantes de tu grupo, carrera y universidad, por ejemplo:
                    </p>
                    <ul>
                        <li>Integrante 1 – Programa / Universidad</li>
                        <li>Integrante 2 – Programa / Universidad</li>
                        <li>Integrante 3 – Programa / Universidad</li>
                        <li>Integrante 4 – Programa / Universidad</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">¿Qué es una anualidad diferida?</h2>
                    <p>
                        Una <strong>anualidad</strong> es una serie de pagos iguales que se realizan a intervalos
                        de tiempo iguales (por ejemplo, mensual, trimestral o anualmente).
                    </p>
                    <p>
                        Una <strong>anualidad diferida</strong> es aquella en la que los pagos no inician de inmediato,
                        sino después de un cierto número de periodos de espera (diferimiento).
                    </p>

                    <h3 class="h5 mt-4">Parámetros principales</h3>
                    <ul>
                        <li><strong>R</strong>: monto de cada pago periódico.</li>
                        <li><strong>i</strong>: tasa de interés por periodo (en forma decimal).</li>
                        <li><strong>n</strong>: número de pagos de la anualidad.</li>
                        <li><strong>k</strong>: número de periodos de diferimiento antes del primer pago.</li>
                    </ul>

                    <h3 class="h5 mt-4">Fórmulas utilizadas</h3>
                    <p>
                        Para una anualidad <em>vencida</em> (pagos al final de cada periodo) diferida k periodos,
                        el valor presente en el tiempo 0 es:
                    </p>
                    <p class="bg-light p-3 border rounded">
                        VP = R &times; \(\dfrac{1 - (1 + i)^{-n}}{i}\) &times; (1 + i)<sup>-k</sup>
                    </p>
                    <p>
                        El valor futuro al final del último pago (momento n) se calcula como una anualidad ordinaria:
                    </p>
                    <p class="bg-light p-3 border rounded">
                        VF = R &times; \(\dfrac{(1 + i)^{n} - 1}{i}\)
                    </p>

                    <h3 class="h5 mt-4">Cómo usar la calculadora</h3>
                    <ol>
                        <li>Ingresa el monto de cada pago periódico R.</li>
                        <li>Ingresa la tasa de interés por periodo en porcentaje (por ejemplo, 5 para 5%).</li>
                        <li>Indica la cantidad total de pagos n.</li>
                        <li>Indica cuántos periodos pasan antes de que comience la serie de pagos (k).</li>
                        <li>Haz clic en “Calcular” para obtener el valor presente y el valor futuro.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

