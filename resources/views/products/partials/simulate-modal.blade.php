<div class="modal fade" id="simulatorModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Simulación de Precio</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="product_id">

                {{-- SUGERENCIAS --}}
                <div id="suggestedSection">
                    <h6 class="mb-3">Sugerencias automáticas</h6>

                    <div class="card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Web propia</strong><br>
                                <span id="suggestedWebPrice">—</span>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="useSuggested('web')">
                                Usar
                            </button>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Marketplace</strong><br>
                                <span id="suggestedMarketplacePrice">—</span>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="useSuggested('marketplace')">
                                Usar
                            </button>
                        </div>
                    </div>

                    <button class="btn btn-secondary w-100" onclick="showManual()">
                        Modo manual
                    </button>
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
                            <option value="">Web propia</option>
                            @foreach(\App\Models\Marketplace::all() as $m)
                                <option value="{{ $m->id }}">
                                    {{ $m->name }} ({{ $m->commission_percent }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-success w-100 mb-3" onclick="runSimulation()">
                        Calcular
                    </button>

                    <div id="simulationResult" class="alert d-none"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button class="btn btn-primary d-none" id="confirmBtn" onclick="approvePrice()">
                    Guardar precio
                </button>
            </div>

        </div>
    </div>
</div>
