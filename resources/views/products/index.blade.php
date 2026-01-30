<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold fs-4 text-dark">
            Productos
        </h2>
    </x-slot>

    <div class="container mt-4">
        {{-- IMPORTADOR CSV --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-3" style="color:#1d71b8;">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>Importar stock desde archivo CSV
                    </h5>
                    <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data" id="csvImportForm" class="d-flex align-items-center">
                        @csrf
                        <div class="mb-3 me-3" style="flex-grow:1; min-width: 300px;">
                            <div id="dropzone" class="border border-2 rounded py-4 px-3 text-center" style="cursor:pointer;background:#fafbfc;border-color:#dde4ee;color:#6c757d;"
                                 onclick="document.getElementById('csvFileInput').click()">
                                <div id="dropzoneMsg">
                                    <i class="bi bi-cloud-arrow-up fs-3 mb-1 text-primary"></i><br>
                                    Arrastra aquí tu archivo CSV exportado de KAME<br>
                                    <span class="d-block mt-1 small">o haz click para buscarlo</span>
                                </div>
                                <input type="file" accept=".csv,application/vnd.ms-excel" name="csv_file" id="csvFileInput" class="d-none" required onchange="onFileInput(event)">
                            </div>
                        </div>
                        <button type="submit" id="importBtn" class="btn btn-primary" style="background:#1d71b8;border:none;" disabled>
                            <i class="bi bi-upload me-1"></i>Importar stock
                        </button>
                    </form>
                </div>
                <div class="ms-3">
                    <form method="POST" action="{{ route('products.importBot') }}" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-outline-info">
                            <i class="bi bi-arrow-repeat me-1"></i>Reimportar archivo más actual
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- BUSCADOR --}}
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-4">
            <div class="col-md-8">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por SKU o nombre">
            </div>

            <div class="col-md-2">
                <button class="btn btn-dark w-100">Buscar</button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
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
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" onclick="openSimulator({{ $product->id }})">▶ Simular</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No se encontraron productos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="simulatorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Simulación de Precio</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="product_id">
                    {{-- SUGERENCIAS --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>💡 Precios sugeridos</h6>
                            <div class="d-flex gap-3 mt-2">
                                <button id="btnWeb" class="btn btn-outline-success w-50"></button>
                                <button id="btnMarketplace" class="btn btn-outline-primary w-50"></button>
                            </div>
                            <a href="#" class="d-block mt-2" onclick="showManual(event)">Usar precio manual</a>
                        </div>
                    </div>

                    {{-- MANUAL --}}
                    <div id="manualSection" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Precio de venta</label>
                            <input type="number" id="sale_price" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Canal</label>
                            <select id="marketplace_id" class="form-select">
                                @foreach(\App\Models\Marketplace::where('company_id', auth()->user()->company_id)->get() as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ number_format($m->commission_percent,0) }}%)</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-success w-100" onclick="runSimulation()">Calcular</button>
                        <div id="simulationResult" class="alert d-none mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let dropzone = document.getElementById('dropzone');
            let csvInput = document.getElementById('csvFileInput');
            let importBtn = document.getElementById('importBtn');

            if (dropzone && csvInput && importBtn) {
                dropzone.ondragover = function(e) {
                    e.preventDefault();
                    dropzone.classList.add('border-primary');
                    dropzone.style.background = '#eaf4fd';
                };
                dropzone.ondragleave = function() {
                    dropzone.classList.remove('border-primary');
                    dropzone.style.background = '#fafbfc';
                };
                dropzone.ondrop = function(e) {
                    e.preventDefault();
                    dropzone.classList.remove('border-primary');
                    dropzone.style.background = '#fafbfc';
                    if(e.dataTransfer.files.length) {
                        csvInput.files = e.dataTransfer.files;
                        onFileInput({ target: csvInput });
                    }
                };
            }

            window.onFileInput = function(e) {
                if(e.target.files.length) {
                    importBtn.disabled = false;
                } else {
                    importBtn.disabled = true;
                }
            };
        });

        function openSimulator(productId) {
            document.getElementById('product_id').value = productId;
            document.getElementById('sale_price').value = '';
            document.getElementById('manualSection').classList.add('d-none');
            document.getElementById('simulationResult').classList.add('d-none');

            const modal = new bootstrap.Modal(document.getElementById('simulatorModal'));
            modal.show();

            loadSuggestions(productId);
        }

        function loadSuggestions(productId) {
            fetch(`/products/${productId}/suggestions`)
                .then(response => response.json())
                .then(data => {
                    const web = data.suggestions.web;
                    const mp = data.suggestions.marketplace;

                    const btnWeb = document.getElementById('btnWeb');
                    const btnMarketplace = document.getElementById('btnMarketplace');

                    btnWeb.innerText = `Web: $${web.price.toLocaleString()} (Margen ${web.margin}%)`;
                    btnMarketplace.innerText = `Marketplace: $${mp.price.toLocaleString()} (Margen ${mp.margin}%)`;

                    btnWeb.onclick = () => {
                        showManual();
                        document.getElementById('sale_price').value = web.price;
                        document.getElementById('marketplace_id').selectedIndex = 0;
                        runSimulation();
                    };

                    btnMarketplace.onclick = () => {
                        const select = document.getElementById('marketplace_id');
                        const option = [...select.options].find(o => o.text.toLowerCase().includes('market'));
                        if (!option) {
                            alert('Marketplace no configurado');
                            return;
                        }
                        showManual();
                        document.getElementById('sale_price').value = mp.price;
                        select.value = option.value;
                        runSimulation();
                    };
                });
        }

        function showManual(event) {
            if(event) event.preventDefault();
            document.getElementById('manualSection').classList.remove('d-none');
        }

        function runSimulation() {
            const productId = document.getElementById('product_id').value;
            const salePrice = document.getElementById('sale_price').value;
            const marketplaceId = document.getElementById('marketplace_id').value;

            fetch(`/products/${productId}/simulate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ sale_price: salePrice, marketplace_id: marketplaceId })
            })
            .then(response => response.json())
            .then(data => {
                const box = document.getElementById('simulationResult');
                const ok = data.final_status === 'ok';
                box.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
                box.innerHTML = `Margen bruto: <b>${data.pricing.gross_margin_percent}%</b><br>Margen real: <b>${data.pricing.real_margin_percent}%</b><br>Resultado: <b>${ok ? 'OK' : 'Fuera de política'}</b>`;
                box.classList.remove('d-none');
            });
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</x-app-layout>
