<x-app-layout>
<link rel="stylesheet" href="{{ asset('css/simulator.css') }}">

<a href="{{ route('products.index') }}">← Volver</a>

<div class="card card-dark">
    <h2>{{ $product->name }}</h2>
    <p>Costo: ${{ number_format($product->cost,0,',','.') }}</p>
    <p>Stock: {{ $product->stock }}</p>
</div>

{{-- SUGERENCIAS --}}
<div class="card">
    <h3>Sugerencias automáticas</h3>

    <div class="card">
        <strong>Margen mínimo (15%)</strong>
        <p>Precio sugerido: ${{ number_format($product->cost * 1.15,0,',','.') }}</p>
        <button class="btn btn-success">Usar este precio</button>
    </div>

    <div class="card">
        <strong>Margen recomendado (25%)</strong>
        <p>Precio sugerido: ${{ number_format($product->cost * 1.25,0,',','.') }}</p>
        <button class="btn btn-success">Usar este precio</button>
    </div>
</div>

{{-- MANUAL --}}
<div class="card">
    <h3>Precio manual</h3>

    <input type="number" id="sale_price" placeholder="Precio venta">

    <select id="marketplace">
        <option value="">Web (sin comisión)</option>
        @foreach(\App\Models\Marketplace::all() as $m)
            <option value="{{ $m->id }}">
                {{ $m->name }} ({{ $m->commission_percent }}%)
            </option>
        @endforeach
    </select>

    <button class="btn btn-primary" onclick="simulate()">Calcular</button>

    <div id="result"></div>
</div>

<script>
function simulate(){
    fetch('{{ route("products.simulate",$product) }}',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body:JSON.stringify({
            sale_price:document.getElementById('sale_price').value,
            marketplace_id:document.getElementById('marketplace').value
        })
    })
    .then(r=>r.json())
    .then(d=>{
        document.getElementById('result').innerHTML =
            `<p>Margen real: ${d.pricing.real_margin_percent}%</p>
             <p>${d.final_status === 'ok' ? '🟢 OK' : '🔴 Fuera de política'}</p>`;
    });
}
</script>
</x-app-layout>
