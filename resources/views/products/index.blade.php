<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold fs-4 text-dark">Productos</h2>
    </x-slot>

    <div class="container mt-4">

        {{-- IMPORTADOR CSV KAME --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>Importar stock desde CSV
                    </h5>

                    <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data"
                          id="csvImportForm" class="d-flex align-items-center">
                        @csrf

                        <div class="mb-3 me-3 flex-grow-1" style="min-width:300px">
                            <div id="dropzone" class="border border-2 rounded py-4 px-3 text-center"
                                 style="cursor:pointer;background:#fafbfc;color:#6c757d;"
                                 onclick="csvFileInput.click()">
                                <i class="bi bi-cloud-arrow-up fs-3 text-primary"></i><br>
                                Arrastra CSV de KAME o haz click
                                <input type="file" name="csv_file" id="csvFileInput"
                                       accept=".csv" class="d-none" required>
                            </div>
                        </div>

                        <button id="importBtn" disabled class="btn btn-primary">
                            <i class="bi bi-upload"></i> Importar
                        </button>
                    </form>
                </div>

                {{-- BOTONES --}}
                <div class="ms-3 d-flex align-items-center">

                    <form method="POST" action="{{ route('products.importBot') }}" class="me-2">
                        @csrf
                        <button class="btn btn-outline-info">
                            <i class="bi bi-arrow-repeat"></i> Reimportar
                        </button>
                    </form>

                    <button id="btnImportStrategic" class="btn btn-warning">
                        ⭐ Importar estratégicos
                    </button>

                    <form id="formImportStrategic" method="POST"
                          action="{{ route('products.importStrategic') }}"
                          enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="file" name="strategic_csv" id="inputImportStrategic" accept=".csv">
                    </form>
                </div>
            </div>
        </div>

        {{-- BUSCADOR --}}
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-4">
            <div class="col-md-6">
                <input class="form-control" name="q" value="{{ request('q') }}"
                       placeholder="Buscar SKU o nombre">
            </div>

            <div class="col-md-2">
                <button class="btn btn-dark w-100">Buscar</button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>

            <div class="col-md-2">
                <a href="{{ route('products.index',['featured'=>1]) }}"
                   class="btn btn-warning w-100">
                    ⭐ Estratégicos
                </a>
            </div>
        </form>

        {{-- TABLA --}}
        <div class="card shadow-sm">
            <table class="table table-hover align-middle mb-0">
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
                        <td class="text-end">$ {{ number_format($product->cost,0,',','.') }}</td>
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

                        {{-- ⭐ CLICKABLE --}}
                        <td class="text-center fs-4">
                            <span
                                class="strategic-star {{ $product->productStrategy ? 'text-warning' : 'text-secondary' }}"
                                data-id="{{ $product->id }}"
                                style="cursor:pointer">
                                {{ $product->productStrategy ? '⭐' : '☆' }}
                            </span>
                        </td>

                        <td class="text-center">
                            <button class="btn btn-primary btn-sm"
                                onclick="location.href='{{ route('products.simulate.view',$product) }}'">
                                ▶ Simular
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
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

    {{-- DRAG DROP --}}
    <script>
const csvFileInput = document.getElementById('csvFileInput')
const dropzone = document.getElementById('dropzone')
const importBtn = document.getElementById('importBtn')

csvFileInput.addEventListener('change',()=>importBtn.disabled=!csvFileInput.files.length)

dropzone.addEventListener('dragover',e=>{
    e.preventDefault()
    dropzone.style.background='#eef5ff'
})
dropzone.addEventListener('dragleave',()=>dropzone.style.background='#fafbfc')
dropzone.addEventListener('drop',e=>{
    e.preventDefault()
    csvFileInput.files = e.dataTransfer.files
    importBtn.disabled=false
})
</script>

{{-- MINI CSV ESTRATÉGICOS --}}
<script>
btnImportStrategic.onclick=()=>inputImportStrategic.click()
inputImportStrategic.onchange=()=>formImportStrategic.submit()
</script>

{{-- ⭐ TOGGLE MANUAL --}}
<script>
document.querySelectorAll('.strategic-star').forEach(star=>{
    star.onclick=async()=>{

        const res = await fetch(`/products/${star.dataset.id}/toggle-strategic`,{
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                'Accept':'application/json'
            }
        })

        const d = await res.json()

        if(d.status==='added'){
            star.textContent='⭐'
            star.classList.replace('text-secondary','text-warning')
        }else{
            star.textContent='☆'
            star.classList.replace('text-warning','text-secondary')
        }
    }
})
</script>

</x-app-layout>
