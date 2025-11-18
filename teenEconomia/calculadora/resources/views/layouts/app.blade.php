<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Calculadora de Anualidades Diferidas')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('inicio') }}">Grupo Universitario</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('inicio') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('documentacion') }}">Documentación</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('calculadora.form') }}">Calculadora</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container mb-5">
    @yield('content')
</main>

<footer class="bg-white border-top py-3 mt-auto">
    <div class="container text-center">
        <small>Proyecto académico de anualidades diferidas &copy; {{ date('Y') }}</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

