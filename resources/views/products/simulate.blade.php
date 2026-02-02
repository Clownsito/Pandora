<x-app-layout>

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
body, html{
    background:linear-gradient(135deg,#f0f4f8,#d9e2ec);
    font-family:Segoe UI, sans-serif;
}

.container-centered{
    max-width:900px;
    margin:30px auto;
    padding:30px;
    background:white;
    border-radius:16px;
    box-shadow:0 6px 20px rgba(0,0,0,.12);
}

.cards-row{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:24px;
    margin:35px 0;
}

.card-modern{
    padding:22px;
    background:#fff;
    border-radius:14px;
    box-shadow:0 4px 14px rgba(0,0,0,.08);
    text-align:center;
}

.card-modern h6{
    font-weight:600;
    color:#334e68;
}

.price-large{
    font-size:1.8rem;
    font-weight:700;
    color:#2f855a;
    margin:12px 0;
}

.btn-compact{
    border:1px solid #3182ce;
    background:white;
    color:#3182ce;
    padding:6px 16px;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
}

input,select{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #cbd5e0;
    margin-bottom:16px;
}

.btn-primary{
    background:#3182ce;
    color:white;
    border:none;
    border-radius:10px;
    padding:14px;
    font-weight:700;
    width:100%;
}

#result{
    margin-top:20px;
    font-weight:600;
}

.money{ color:#2f855a; }
</style>

<div class="container-centered">

<a href="{{ route('products.index') }}">← Volver</a>

<h2>{{ $product->name }}</h2>
<p>Costo: ${{ number_format($product->cost,0,',','.') }}</p>
<p>Stock: {{ $product->stock }}</p>

<div class="cards-row">

@foreach([
    'web.normal' => 'Web normal',
    'web.oferta' => 'Web oferta',
    'marketplace.normal' => 'Marketplace normal',
    'marketplace.oferta' => 'Marketplace oferta'
] as $key => $label)

@php
[$channel,$type] = explode('.', $key);
$data = $suggestions[$channel][$type] ?? null;
@endphp

@if($data)
<div class="card-modern">
    <h6>{{ $label }} ({{ $data['margin'] }}%)</h6>
    <div class="price-large">
        ${{ number_format($data['price'],0,',','.') }}
    </div>
    <button class="btn-compact" onclick="usePrice({{ $data['price'] }})">Usar</button>
</div>
@else
<div class="card-modern">
    <h6>{{ $label }}</h6>
    <div class="price-large">N/A</div>
</div>
@endif

@endforeach

</div>

<h3>Precio manual</h3>

<input type="number" id="sale_price">

<select id="marketplace">
    <option value="">Web (sin comisión)</option>
    @foreach(\App\Models\Marketplace::where('company_id',$product->company_id)->get() as $m)
        <option value="{{ $m->id }}">
            {{ $m->name }} ({{ $m->commission_percent }}%)
        </option>
    @endforeach
</select>

<button class="btn-primary" onclick="simulate()">Simular</button>

<div id="result"></div>

</div>

<script>
function usePrice(p){
    document.getElementById('sale_price').value = p
}

function formatMoney(n){
    return '$' + new Intl.NumberFormat('es-CL').format(n)
}

function simulate(){

fetch('{{ route("products.simulate",$product) }}', {
    method:'POST',
    headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
    },
    body:JSON.stringify({
        sale_price:document.getElementById('sale_price').value,
        marketplace_id:document.getElementById('marketplace').value || null
    })
})
.then(r=>r.json())
.then(d=>{
    if(!d.pricing){
        document.getElementById('result').innerHTML = 'Error en simulación'
        return
    }

    const p = d.pricing

    document.getElementById('result').innerHTML = `
        Margen bruto: ${p.gross_margin_percent}% → <span class="money">${formatMoney(p.gross_profit)}</span><br>
        Comisión: ${p.commission_percent}% → <span class="money">${formatMoney(p.commission_amount)}</span><br>
        Margen real: ${p.real_margin_percent}% → <span class="money">${formatMoney(p.real_profit)}</span>
    `
})
.catch(()=>{
    document.getElementById('result').innerHTML = 'Error de servidor'
})
}
</script>

<!-- BOTÓN CALCULADORA -->
<div id="calc-btn">🧮</div>

<!-- CALCULADORA -->
<div id="calculator">
    <div class="calc-header">
        Calculadora <span onclick="toggleCalc()">✖</span>
    </div>
    <input type="text" id="calc-display">

    <div class="calc-grid">
        <button onclick="clearCalc()">C</button>
        <button onclick="del()">⌫</button>
        <button onclick="press('/')">÷</button>
        <button onclick="press('*')">×</button>

        <button onclick="press('7')">7</button>
        <button onclick="press('8')">8</button>
        <button onclick="press('9')">9</button>
        <button onclick="press('-')">−</button>

        <button onclick="press('4')">4</button>
        <button onclick="press('5')">5</button>
        <button onclick="press('6')">6</button>
        <button onclick="press('+')">+</button>

        <button onclick="press('1')">1</button>
        <button onclick="press('2')">2</button>
        <button onclick="press('3')">3</button>

        <button onclick="press('0')" style="grid-column:span 2;">0</button>
        <button onclick="press('.')">.</button>
        <button onclick="calculate()" class="equal">=</button>
    </div>
</div>

<style>
#calc-btn{
    position:fixed;
    bottom:22px;
    right:22px;
    background:#3182ce;
    color:white;
    font-size:26px;
    width:58px;
    height:58px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    box-shadow:0 6px 18px rgba(0,0,0,.25);
    z-index:9999;
}

#calculator{
    position:fixed;
    bottom:90px;
    right:22px;
    width:260px;
    background:white;
    border-radius:14px;
    box-shadow:0 10px 30px rgba(0,0,0,.25);
    display:none;
    z-index:9999;
}

.calc-header{
    background:#3182ce;
    color:white;
    padding:10px;
    display:flex;
    justify-content:space-between;
}

#calc-display{
    width:100%;
    padding:12px;
    font-size:1.4rem;
    text-align:right;
    border:none;
}

.calc-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
}

.calc-grid button{
    padding:16px;
    border:1px solid #eee;
    background:white;
    font-size:1.1rem;
}

.equal{
    background:#3182ce;
    color:white;
}
</style>

<script>
const calc = document.getElementById('calculator')
const display = document.getElementById('calc-display')

document.getElementById('calc-btn').onclick = toggleCalc

function toggleCalc(){
    calc.style.display = calc.style.display === 'block' ? 'none' : 'block'
}

function press(v){ display.value += v }
function clearCalc(){ display.value='' }
function del(){ display.value = display.value.slice(0,-1) }

function calculate(){
    try{ display.value = eval(display.value) }
    catch{ display.value='Error' }
}

document.addEventListener('keydown', e=>{
    if(calc.style.display!=='block') return

    if(!isNaN(e.key)||e.key=='.') press(e.key)
    if(['+','-','*','/'].includes(e.key)) press(e.key)
    if(e.key==='Enter') calculate()
    if(e.key==='Backspace') del()
})
</script>

</x-app-layout>
