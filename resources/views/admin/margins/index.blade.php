<x-app-layout>
    <x-slot name="header">
        <h2>Configuración de Márgenes</h2>
    </x-slot>

    <style>
        .margin-box{
            max-width:900px;
            margin:40px auto;
            background:#fff;
            padding:30px;
            border-radius:18px;
            box-shadow:0 8px 28px rgba(0,0,0,.12);
        }

        .section-title{
            font-size:1.4rem;
            font-weight:700;
            color:#1d71b8;
            margin:25px 0 15px;
        }

        .margin-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:14px 0;
            border-bottom:1px solid #edf2f7;
        }

        .margin-row:last-child{
            border-bottom:none;
        }

        input{
            width:120px;
            padding:10px;
            border-radius:10px;
            border:1px solid #cbd5e0;
            text-align:center;
            font-weight:600;
        }

        .save-btn{
            margin-top:30px;
            background:#1d71b8;
            color:white;
            border:none;
            padding:14px 30px;
            border-radius:14px;
            font-weight:700;
            font-size:1.1rem;
            cursor:pointer;
            box-shadow:0 6px 18px rgba(29,113,184,.3);
        }

        .save-btn:hover{
            background:#155a93;
        }
    </style>

    <div class="margin-box">

        @if(session('success'))
            <div style="background:#d1fae5;padding:12px;border-radius:10px;margin-bottom:20px;color:#065f46;font-weight:600;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.margins.update') }}">
            @csrf
            @method('PUT') {{-- 🔥 ESTA ERA LA CLAVE --}}

            <div class="section-title">🌐 Web</div>

            @foreach($rules->where('channel','web') as $rule)
                <div class="margin-row">
                    <div>{{ ucfirst($rule->type) }}</div>

                    <input 
                        type="number"
                        step="0.01"
                        name="margins[{{ $rule->id }}]"
                        value="{{ $rule->margin_percent }}"
                        required
                    > %
                </div>
            @endforeach

            <div class="section-title">🛒 Marketplace</div>

            @foreach($rules->where('channel','marketplace') as $rule)
                <div class="margin-row">
                    <div>{{ ucfirst($rule->type) }}</div>

                    <input 
                        type="number"
                        step="0.01"
                        name="margins[{{ $rule->id }}]"
                        value="{{ $rule->margin_percent }}"
                        required
                    > %
                </div>
            @endforeach

            <div style="text-align:center;">
                <button class="save-btn">
                    Guardar cambios
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
