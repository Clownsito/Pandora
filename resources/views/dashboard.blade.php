<x-app-layout>
    <style>
        .dashboard-container {
            margin-top: 4rem;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .dashboard-card {
            background: #fff;
            border: 1.5px solid #e3e8ee;
            border-radius: 1.25rem;
            box-shadow: 0 4px 16px 0 rgba(24, 62, 103, 0.08);
            flex: 1 1 280px;
            max-width: 340px;
            padding: 2.5rem 1.5rem 2rem 1.5rem;
            min-width: 280px;
            text-align: center;
            min-height: 225px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow .18s;
            margin-bottom: 1.5rem;
            cursor: pointer;
        }

        .dashboard-card:hover {
            box-shadow: 0 8px 24px 0 rgba(29, 113, 184, .19);
        }

        .dashboard-card .card-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #1d71b8;
            margin-bottom: 0.8em;
        }

        .dashboard-card .tool-btn {
            background: #1d71b8 !important;
            color: #fff !important;
            border: none;
            border-radius: 0.7rem;
            padding: 0.75rem 1.6rem;
            margin-bottom: 0.9rem;
            margin-top: auto;
            font-weight: 600;
            font-size: 1.11rem;
            box-shadow: 0 0 2px 0 rgba(29, 113, 184, .15);
            transition: background .16s;
        }

        .dashboard-card .tool-btn:hover {
            background: #16598f !important;
        }

        .disabled-btn {
            background: #b0b8c2 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .small-italic {
            color: #777;
            font-style: italic;
        }
    </style>

    <div class="dashboard-container">

        <!-- Subir productos -->
        <div class="dashboard-card">
            <div class="card-title">
                <i class="bi bi-upload" style="font-size:2.1rem;color:#1d71b8;"></i><br>
                Subir Productos
            </div>
            <button class="btn tool-btn disabled-btn" disabled>Próximamente</button>
            <div class="small-italic">Esta función estará disponible pronto</div>
        </div>

        <!-- Simulador -->
        <div class="dashboard-card">
            <div class="card-title">
                <i class="bi bi-calculator" style="font-size:2rem;color:#1d71b8;"></i><br>
                Calculadora de Margen
            </div>
            <a href="{{ url('/products') }}" class="btn tool-btn">Abrir simulador</a>
            <div>Simula precios y márgenes</div>
        </div>

        <!-- Alertas -->
        <div class="dashboard-card">
            <div class="card-title">
                <i class="bi bi-bell" style="font-size:2rem;color:#1d71b8;opacity:.5;"></i><br>
                Alertas
            </div>
            <button class="btn tool-btn disabled-btn" disabled>Próximamente</button>
            <div class="small-italic">Esta función estará disponible pronto</div>
        </div>

        <!-- ADMIN -->
        @if(Auth::user()->isAdmin())

            <!-- Usuarios -->
            <div class="dashboard-card">
                <div class="card-title">
                    <i class="bi bi-person-plus" style="font-size:2rem;color:#1d71b8;"></i><br>
                    Dar acceso
                </div>
                <a href="{{ route('admin.users.manage') }}" class="btn tool-btn">
                    Crear y gestionar usuarios
                </a>
            </div>

            <!-- NUEVO: Márgenes -->
            <div class="dashboard-card">
                <div class="card-title">
                    <i class="bi bi-graph-up-arrow" style="font-size:2rem;color:#1d71b8;"></i><br>
                    Configurar márgenes
                </div>
                <a href="{{ route('admin.margins.index') }}" class="btn tool-btn">
                    Editar porcentajes
                </a>
                <div>Controla sugerencias y rentabilidad</div>
            </div>

        @endif

    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</x-app-layout>
