@extends('layouts.app')

@section('title', 'Inicio - Anualidades Diferidas')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="card-title mb-3">Calculadora de Anualidades Diferidas</h1>
                    <p class="lead">
                        Somos un grupo universitario dedicado a aplicar las matemáticas financieras
                        a situaciones reales. Como parte de nuestro curso de Economía/Finanzas hemos desarrollado
                        esta calculadora web para analizar <strong>anualidades diferidas</strong>.
                    </p>
                    <p>
                        En esta aplicación podrás:
                    </p>
                    <ul>
                        <li>Revisar la teoría básica de anualidades diferidas.</li>
                        <li>Conocer a los integrantes del equipo y el objetivo del proyecto.</li>
                        <li>Calcular el valor presente y futuro de una anualidad diferida con tus propios datos.</li>
                    </ul>
                    <p class="mb-0">
                        Te invitamos a comenzar revisando la
                        <a href="{{ route('documentacion') }}">documentación</a>
                        o ir directamente a la
                        <a href="{{ route('calculadora.form') }}">calculadora</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

