<?php

namespace App\Services\Kame;

use Illuminate\Support\Facades\Http;

class KameClient
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.kame.base_url');
        $this->token   = config('services.kame.token');
    }

    /**
     * Obtiene stock / artículos desde KAME
     * IMPORTANTE: este endpoint es POST
     */
        public function getStock(): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept'        => 'application/json',
        ])->get($this->baseUrl . '/Inventario/getStock');

        $response->throw();

        return $response->json();
    }
}