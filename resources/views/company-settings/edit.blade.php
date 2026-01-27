<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Configuración de Empresa
        </h2>
    </x-slot>

    <div class="py-8 pb-32 max-w-4xl mx-auto">
        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('company-settings.update') }}"
            class="bg-white p-6 rounded-lg shadow-md"
        >
            @csrf
            @method('PUT')

            {{-- ===================== --}}
            {{-- SEMÁFORO DE STOCK --}}
            {{-- ===================== --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">
                Semáforo de Stock
            </h3>

            <div class="mb-4">
                <label class="block mb-1 font-medium">
                    Stock Rojo (≤)
                </label>
                <input
                    type="number"
                    name="stock_rojo_max"
                    value="{{ old('stock_rojo_max', $settings->stock_rojo_max) }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">
                    Stock Amarillo (≥)
                </label>
                <input
                    type="number"
                    name="stock_amarillo_min"
                    value="{{ old('stock_amarillo_min', $settings->stock_amarillo_min) }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                    required
                >
            </div>

            <div class="mb-6">
                <label class="block mb-1 font-medium">
                    Stock Verde (≥)
                </label>
                <input
                    type="number"
                    name="stock_verde_min"
                    value="{{ old('stock_verde_min', $settings->stock_verde_min) }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                    required
                >
            </div>

            {{-- ===================== --}}
            {{-- MÁRGENES --}}
            {{-- ===================== --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">
                Márgenes
            </h3>

            <div class="mb-4">
                <label class="block mb-1 font-medium">
                    Margen mínimo (%)
                </label>
                <input
                    type="number"
                    step="0.01"
                    name="margen_min_percent"
                    value="{{ old('margen_min_percent', $settings->margen_min_percent) }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                    required
                >
            </div>

            <div class="mb-6">
                <label class="block mb-1 font-medium">
                    Margen máximo (%)
                </label>
                <input
                    type="number"
                    step="0.01"
                    name="margen_max_percent"
                    value="{{ old('margen_max_percent', $settings->margen_max_percent) }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                    required
                >
            </div>

            {{-- ===================== --}}
            {{-- BOTÓN GUARDAR --}}
            {{-- ===================== --}}
            <div class="mt-8 pt-6 border-t flex justify-end">
             <x-primary-button>
                    Guardar cambios
                </x-primary-button>
            </div>
            </div>
        </form>
    </div>
</x-app-layout>
