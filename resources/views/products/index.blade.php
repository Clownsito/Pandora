<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold fs-4 text-dark">
            Productos
        </h2>
    </x-slot>

    <div class="container mt-4">

        {{-- IMPORTADOR CSV KAME --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-3" style="color:#1d71b8;">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>Importar stock desde archivo CSV
                    </h5>

                    <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data"
                          id="csvImportForm" class="d-flex align-items-center">
                        @csrf

                        <div class="mb-3 me-3" style="flex-grow:1; min-width: 300px;">
                            <div id="dropzone" class="border border-2 rounded py-4 px-3 text-center"
                                 style="cursor:pointer;background:#fafbfc;border-color:#dde4ee;color:#6c757d;"
                                 onclick="document.getElementById('csvFileInput').click()">

                                <div id="dropzoneMsg">
                                    <i class="bi bi-cloud-arrow-up fs-3 mb-1 text-primary"></i><br>
                                    Arrastra aquí tu archivo CSV exportado de KAME<br>
                                    <span class="d-block mt-1 small">o haz click para buscarlo</span>
                                </div>

                                <input type="file"
                                       accept=".csv,application/vnd.ms-excel"
                                       name="csv_file"
                                       id="csvFileInput"
                                       class="d-none"
                                       required>
                            </div>
                        </div>

                        <button type="submit" id="importBtn"
                                class="btn btn-primary"
                                style="background:#1d71b8;border:none;"
                                disabled>
                            <i class="bi bi-upload me-1"></i>Importar stock
                        </button>
                    </form>
                </div>

                {{-- BOTONES LATERALES --}}
                <div class="ms-3 d-flex align-items-center">

                    {{-- Reimportar KAME --}}
                    <form method="POST" action="{{ route('products.importBot') }}" class="me-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-info">
                            <i class="bi bi-arrow-repeat me-1"></i>Reimportar archivo más actual
                        </button>
                    </form>

                    {{-- ⭐ Importar estratégicos --}}
                    <button id="btnImportStrategic" class="btn btn-warning">
                        ⭐ Importar estratégicos
                    </button>

                    <form id="formImportStrategic"
                          method="POST"
                          action="{{ route('products.importStrategic') }}"
                          enctype="multipart/form-data"
                          style="display:none;">
                        @csrf
                        <input type="file"
                               name="strategic_csv"
                               accept=".csv"
                               id="inputImportStrategic">
                    </form>
                </div>
            </div>
        </div>

        {{-- BUSCADOR + FILTRO --}}
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-4">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control"
                       placeholder="Buscar por SKU o nombre">
            </div>

            <div class="col-md-2">
                <button class="btn btn-dark w-100">Buscar</button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('products.index') }}"
                   class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>

            <div class="col-md-2">
                <a href="{{ route('products.index', ['featured' => 1]) }}"
                   class="btn btn-warning w-100">
                    ⭐ Estratégicos
                </a>
            </div>
        </form>

        {{-- TABLA --}}
        <div class="card shadow-sm">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th class="text-end">Costo</th>
                        <th class="text-end">Stock</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">⭐</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td class="text-end">$ {{ number_format($product->cost, 0, ',', '.') }}</td>
                            <td class="text-end">{{ $product->stock }}</td>

                            <td class="text-center">
                                @if($product->stock_status === 'rojo')
                                    <span class="badge bg-danger">Crítico</span>
                                @elseif($product->stock_status === 'amarillo')
                                    <span class="badge bg-warning text-dark">Alerta</span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>

                            {{-- ⭐ ESTADO ESTRATÉGICO --}}
                            <td class="text-center fs-4">
                                @if($product->productStrategy)
                                    <span class="text-warning">⭐</span>
                                @else
                                    <span class="text-secondary">☆</span>
                                @endif
                            </td>

                            {{-- SIMULAR --}}
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm"
                                    onclick="window.location.href='{{ route('products.simulate.view', $product) }}'">
                                    ▶ Simular
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No se encontraron productos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ICONOS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    {{-- DRAG & DROP KAME --}}
    <script>
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('csvFileInput');
const importBtn = document.getElementById('importBtn');

function updateButton(){
    importBtn.disabled = !fileInput.files.length;
}

fileInput.addEventListener('change', updateButton);

dropzone.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone.style.borderColor = '#1d71b8';
    dropzone.style.background = '#eef5ff';
});

dropzone.addEventListener('dragleave', () => {
    dropzone.style.borderColor = '#dde4ee';
    dropzone.style.background = '#fafbfc';
});

dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.style.borderColor = '#dde4ee';
    dropzone.style.background = '#fafbfc';

    if (!e.dataTransfer.files.length) return;
    fileInput.files = e.dataTransfer.files;
    updateButton();
});
</script>

{{-- MINI UPLOADER ESTRATÉGICOS --}}
<script>
document.getElementById('btnImportStrategic').addEventListener('click', () => {
    document.getElementById('inputImportStrategic').click();
});

document.getElementById('inputImportStrategic').addEventListener('change', () => {
    if (document.getElementById('inputImportStrategic').files.length) {
        document.getElementById('formImportStrategic').submit();
    }
});
</script>

</x-app-layout>
