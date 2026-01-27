<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold fs-4 text-dark">
            Productos
        </h2>
    </x-slot>

    <div class="container mt-4">

        {{-- BUSCADOR --}}
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-4">
            <div class="col-md-8">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    class="form-control"
                    placeholder="Buscar por SKU o nombre">
            </div>

            <div class="col-md-2">
                <button class="btn btn-dark w-100">Buscar</button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
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
                            <button
                                class="btn btn-primary btn-sm"
                                onclick="openSimulator({{ $product->id }})">
                                ▶ Simular
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No se encontraron productos
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="simulatorModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Simulación de Precio</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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

                            <a href="#" class="d-block mt-2" onclick="showManual(event)">
                                Usar precio manual
                            </a>
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
                                <option value="">Web propia (sin comisión)</option>
                                @foreach(\App\Models\Marketplace::all() as $m)
                                    <option value="{{ $m->id }}">
                                        {{ $m->name }} ({{ $m->commission_percent }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button class="btn btn-success w-100" onclick="runSimulation()">
                            Calcular
                        </button>

                        <div id="simulationResult" class="alert d-none mt-3"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- JS --}}
    <script>
        let modal;

        function openSimulator(productId) {
            document.getElementById('product_id').value = productId;
            document.getElementById('manualSection').classList.add('d-none');
            document.getElementById('simulationResult').classList.add('d-none');

            modal = bootstrap.Modal.getOrCreateInstance(
                document.getElementById('simulatorModal')
            );
            modal.show();

            loadSuggestions(productId);
        }

        function loadSuggestions(productId) {
            fetch(`/products/${productId}/suggestions`)
                .then(r => r.json())
                .then(data => {
                    const web = data.suggestions.web;
                    const mp  = data.suggestions.marketplace;

                    const btnWeb = document.getElementById('btnWeb');
                    const btnMp  = document.getElementById('btnMarketplace');

                    btnWeb.innerText = `Web: $ ${web.price.toLocaleString()}`;
                    btnMp.innerText  = `Marketplace: $ ${mp.price.toLocaleString()}`;

                    btnWeb.onclick = () => useSuggested(web.price, '');
                    btnMp.onclick  = () => useSuggested(mp.price, 1);
                });
        }

        function showManual(e) {
            e.preventDefault();
            document.getElementById('manualSection').classList.remove('d-none');
        }

        function useSuggested(price, marketplaceId) {
            showManual(new Event('click'));
            document.getElementById('sale_price').value = price;
            document.getElementById('marketplace_id').value = marketplaceId;
            runSimulation();
        }

        function runSimulation() {
            fetch(`/products/${product_id.value}/simulate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    sale_price: sale_price.value,
                    marketplace_id: marketplace_id.value
                })
            })
            .then(r => r.json())
            .then(data => {
                const box = document.getElementById('simulationResult');
                const ok = data.final_status === 'ok';

                box.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
                box.innerHTML = `
                    Margen bruto: <b>${data.pricing.gross_margin_percent}%</b><br>
                    Margen real: <b>${data.pricing.real_margin_percent}%</b>
                `;
                box.classList.remove('d-none');
            });
        }
    </script>
</x-app-layout>
