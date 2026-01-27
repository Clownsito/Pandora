<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size:20px;font-weight:600">
            Productos
        </h2>
    </x-slot>

    <div style="max-width:1200px;margin:24px auto">

        {{-- TABLA --}}
        <div style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.1)">
            <table style="width:100%;border-collapse:collapse">
                <thead style="background:#f3f4f6">
                    <tr>
                        <th style="padding:12px;text-align:left">SKU</th>
                        <th style="padding:12px;text-align:left">Nombre</th>
                        <th style="padding:12px;text-align:right">Costo</th>
                        <th style="padding:12px;text-align:right">Stock</th>
                        <th style="padding:12px;text-align:center">Estado</th>
                        <th style="padding:12px;text-align:center">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($products as $product)
                        <tr style="border-top:1px solid #e5e7eb">
                            <td style="padding:12px">{{ $product->sku }}</td>
                            <td style="padding:12px">{{ $product->name }}</td>
                            <td style="padding:12px;text-align:right">
                                $ {{ number_format($product->cost, 0, ',', '.') }}
                            </td>
                            <td style="padding:12px;text-align:right">
                                {{ $product->stock }}
                            </td>

                            <td style="padding:12px;text-align:center">
                                @if($product->stock_status === 'rojo')
                                    <span style="background:#dc2626;color:#fff;padding:6px 12px;border-radius:999px;font-weight:600">
                                        Rojo
                                    </span>
                                @elseif($product->stock_status === 'verde')
                                    <span style="background:#16a34a;color:#fff;padding:6px 12px;border-radius:999px;font-weight:600">
                                        Verde
                                    </span>
                                @else
                                    <span style="background:#facc15;color:#000;padding:6px 12px;border-radius:999px;font-weight:600">
                                        Amarillo
                                    </span>
                                @endif
                            </td>

                            {{-- BOTÓN VISIBLE SÍ O SÍ --}}
                            <td style="padding:12px;text-align:center">
                                <button
                                    onclick="openSimulator({{ $product->id }})"
                                    style="
                                        background:#2563eb;
                                        color:white;
                                        padding:8px 16px;
                                        border-radius:6px;
                                        font-weight:600;
                                        cursor:pointer;
                                        border:none;
                                    ">
                                    Simular
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL REAL --}}
    <div id="simulatorModal"
         style="
            display:none;
            position:fixed;
            inset:0;
            background:rgba(0,0,0,.6);
            z-index:9999;
            align-items:center;
            justify-content:center;
         ">

        <div style="
            background:white;
            width:100%;
            max-width:520px;
            padding:24px;
            border-radius:10px;
            position:relative;
        ">

            <h3 style="font-size:18px;font-weight:600;margin-bottom:16px">
                Simulación de Precio
            </h3>

            <input type="hidden" id="product_id">

            <div style="margin-bottom:12px">
                <label>Precio de venta</label>
                <input id="sale_price" type="number"
                       style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
            </div>

            <div style="margin-bottom:16px">
                <label>Canal</label>
                <select id="marketplace_id"
                        style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
                    <option value="">Web propia (sin comisión)</option>
                    @foreach(\App\Models\Marketplace::all() as $m)
                        <option value="{{ $m->id }}">
                            {{ $m->name }} ({{ $m->commission_percent }}%)
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- BOTÓN CONFIRMACIÓN --}}
            <button onclick="runSimulation()"
                    style="
                        width:100%;
                        background:#16a34a;
                        color:white;
                        padding:10px;
                        border-radius:6px;
                        font-weight:600;
                        cursor:pointer;
                        border:none;
                    ">
                Calcular simulación
            </button>

            <div id="simulationResult"
                 style="margin-top:16px;display:none"></div>

            <button onclick="closeSimulator()"
                    style="
                        position:absolute;
                        top:10px;
                        right:12px;
                        font-size:22px;
                        background:none;
                        border:none;
                        cursor:pointer;
                    ">
                ×
            </button>
        </div>
    </div>

    {{-- JS --}}
    <script>
        function openSimulator(id) {
            document.getElementById('product_id').value = id;
            document.getElementById('sale_price').value = '';
            document.getElementById('marketplace_id').value = '';
            document.getElementById('simulationResult').style.display = 'none';
            document.getElementById('simulatorModal').style.display = 'flex';
        }

        function closeSimulator() {
            document.getElementById('simulatorModal').style.display = 'none';
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
                body: JSON.stringify({
                    sale_price: salePrice,
                    marketplace_id: marketplaceId
                })
            })
            .then(r => r.json())
            .then(data => {
                const box = document.getElementById('simulationResult');
                const ok = data.final_status === 'ok';

                box.style.display = 'block';
                box.style.padding = '12px';
                box.style.borderRadius = '6px';
                box.style.background = ok ? '#dcfce7' : '#fee2e2';

                box.innerHTML = `
                    <p><b>Margen bruto:</b> ${data.pricing.gross_margin_percent}%</p>
                    <p><b>Margen real:</b> ${data.pricing.real_margin_percent}%</p>
                    <p style="margin-top:10px;font-weight:600">
                        ${ok ? '✔ Dentro de política' : '✖ Fuera de política'}
                    </p>
                `;
            });
        }
    </script>
</x-app-layout>
