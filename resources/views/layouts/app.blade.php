<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pandora</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS propio -->
     <link href="{{ asset('css/custom-style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pricing.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand navbar-light bg-white shadow-sm">
        <div class="container">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary px-4 py-2 rounded">
                ← Volver al Dashboard
            </a>
        </div>
    </nav>

    <main class="py-4 container border rounded bg-white shadow-sm">
        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

