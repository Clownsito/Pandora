<div class="modal fade" id="simulatorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Simulación de Precio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="product_id">
        {{-- SUGERENCIAS --}}
        <div id="suggestedSection">
          <h6>💡 Precios sugeridos normales</h6>
          <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <strong>Web propia</strong><br>
                <span id="suggestedWebPriceNormal">—</span>
              </div>
              <button class="btn btn-outline-primary btn-sm" onclick="useSuggested('webNormal')">Usar</button>
            </div>
          </div>
          <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <strong>Marketplace</strong><br>
                <span id="suggestedMarketplacePriceNormal">—</span>
              </div>
              <button class="btn btn-outline-primary btn-sm" onclick="useSuggested('marketplaceNormal')">Usar</button>
            </div>
          </div>

          <h6>🎉 Precios sugeridos en oferta</h6>
          <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <strong>Web propia</strong><br>
                <span id="suggestedWebPriceOffer">—</span>
              </div>
              <button class="btn btn-outline-success btn-sm" onclick="useSuggested('webOffer')">Usar</button>
            </div>
          </div>
          <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <strong>Marketplace</strong><br>
                <span id="suggestedMarketplacePriceOffer">—</span>
              </div>
              <button class="btn btn-outline-success btn-sm" onclick="useSuggested('marketplaceOffer')">Usar</button>
            </div>
          </div>

          <button class="btn btn-secondary w-100" onclick="showManual()">Modo manual</button>
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
        <button class="btn btn-primary d-none" id="confirmBtn" onclick="approvePrice()">Guardar precio</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const suggestedWebPriceNormal = document.getElementById('suggestedWebPriceNormal');
  const suggestedMarketplacePriceNormal = document.getElementById('suggestedMarketplacePriceNormal');
  const suggestedWebPriceOffer = document.getElementById('suggestedWebPriceOffer');
  const suggestedMarketplacePriceOffer = document.getElementById('suggestedMarketplacePriceOffer');
  const manualSection = document.getElementById('manualSection');
  const simulationResult = document.getElementById('simulationResult');
  const confirmBtn = document.getElementById('confirmBtn');

  window.useSuggested = function(channel) {
    switch(channel) {
      case 'webNormal':
        document.getElementById('sale_price').value = suggestedWebPriceNormal.textContent.trim() !== '—' ? suggestedWebPriceNormal.textContent.trim() : '';
        break;
      case 'marketplaceNormal':
        document.getElementById('sale_price').value = suggestedMarketplacePriceNormal.textContent.trim() !== '—' ? suggestedMarketplacePriceNormal.textContent.trim() : '';
        break;
      case 'webOffer':
        document.getElementById('sale_price').value = suggestedWebPriceOffer.textContent.trim() !== '—' ? suggestedWebPriceOffer.textContent.trim() : '';
        break;
      case 'marketplaceOffer':
        document.getElementById('sale_price').value = suggestedMarketplacePriceOffer.textContent.trim() !== '—' ? suggestedMarketplacePriceOffer.textContent.trim() : '';
        break;
    }
    simulationResult.classList.add('d-none');
    confirmBtn.classList.remove('d-none');
  };

  window.showManual = function() {
    manualSection.classList.remove('d-none');
    confirmBtn.classList.remove('d-none');
    simulationResult.classList.add('d-none');
  };

  window.runSimulation = function() {
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
    }).then(response => response.json())
      .then(data => {
        simulationResult.classList.remove('d-none');
        if (data.final_status === 'ok') {
          simulationResult.classList.remove('alert-danger');
          simulationResult.classList.add('alert-success');
        } else {
          simulationResult.classList.remove('alert-success');
          simulationResult.classList.add('alert-danger');
        }
        simulationResult.innerHTML = `
          Margen bruto: <strong>${data.pricing.gross_margin_percent}%</strong><br>
          Margen real: <strong>${data.pricing.real_margin_percent}%</strong><br>
          Resultado: <strong>${data.final_status === 'ok' ? 'OK' : 'Fuera de política'}</strong>
        `;
      });
  };

  window.approvePrice = function() {
    alert('Función aprobar precio aún no implementada.');
  };
});
</script>