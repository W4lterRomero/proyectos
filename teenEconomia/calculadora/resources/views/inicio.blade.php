@extends('layouts.app')

@section('title', 'Inicio - Anualidades diferidas')

@section('content')
    <div class="max-w-5xl mx-auto space-y-12">
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 md:p-8 space-y-5">
            <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">
                Proyecto para Ingeniería Económica
            </p>
            <h1 class="text-3xl md:text-4xl font-semibold text-slate-900">
                Anualidades diferidas
            </h1>
            <p class="text-sm md:text-base text-slate-700 leading-relaxed">
                Esta página la hicimos para tener una calculadora sencilla
                y unos apuntes rápidos sobre anualidades diferidas. No es
                un súper portal financiero: solo queremos que puedas
                comprobar tus ejercicios y entender qué pasa con los números.
            </p>

            <div class="grid md:grid-cols-3 gap-6 pt-2">
                <div class="space-y-2 text-sm text-slate-700 md:col-span-2">
                    <h2 class="font-semibold text-slate-900">Con esta herramienta puedes:</h2>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Calcular el capital (C) y el monto (M) de una anualidad diferida.</li>
                        <li>Probar distintos valores de tasa, número de pagos y periodo de diferimiento.</li>
                        <li>Ver un resumen de la teoría que usamos en clase.</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-800 transition hover:border-blue-200 hover:bg-blue-50/60">
                    <p class="font-semibold mb-2">Ejemplo típico</p>
                    <p>
                        “Se desea saber cuánto vale hoy una serie de pagos iguales
                        que empiezan dentro de k periodos…”
                    </p>
                    <p class="mt-2">
                        Aquí puedes ingresar los datos del ejercicio y comparar con
                        tu procedimiento a mano.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-4">
                <a href="{{ route('calculadora.form') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-600 transition">
                    Ir a la calculadora
                </a>
                <a href="{{ route('documentacion') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-800 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-slate-300 transition">
                    Ver resumen teórico
                </a>
            </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 space-y-5">
            <h2 class="text-xl font-semibold text-slate-900">
                ¿Cómo está organizado el sitio?
            </h2>
            <div class="grid md:grid-cols-3 gap-4 text-sm text-slate-700">
                <div class="rounded-xl border border-transparent hover:border-blue-200 hover:bg-blue-50/60 p-4 transition cursor-default">
                    <p class="font-semibold text-slate-900 mb-1">Inicio</p>
                    <p>Página de entrada con una explicación rápida de qué hace la herramienta.</p>
                </div>
                <div class="rounded-xl border border-transparent hover:border-blue-200 hover:bg-blue-50/60 p-4 transition cursor-default">
                    <p class="font-semibold text-slate-900 mb-1">Documentación</p>
                    <p>Notas resumidas de anualidades diferidas: definiciones, fórmulas y un par de ejemplos.</p>
                </div>
                <div class="rounded-xl border border-transparent hover:border-blue-200 hover:bg-blue-50/60 p-4 transition cursor-default">
                    <p class="font-semibold text-slate-900 mb-1">Calculadora</p>
                    <p>Formulario donde puedes jugar con los datos del ejercicio y ver los resultados numéricos.</p>
                </div>
            </div>
        </section>
    </div>
@endsection

