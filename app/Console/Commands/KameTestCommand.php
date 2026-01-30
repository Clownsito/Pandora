<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Kame\KameClient;

class KameTestCommand extends Command
{
    protected $signature = 'kame:test';
    protected $description = 'Prueba conexión y datos de KAME';

        public function handle(KameClient $client)
    {
        $this->info('Consultando stock desde KAME...');

        try {
            $data = $client->getStock();
        } catch (\Throwable $e) {
            $this->error('Error al conectar con KAME');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        if (!is_array($data)) {
            $this->error('La respuesta no es un array');
            dump($data);
            return Command::FAILURE;
        }

        $this->info('Respuesta recibida correctamente');

        // Mostramos SOLO el primer item
        if (count($data) > 0) {
            $this->line('Primer producto recibido:');
            dump($data[0]);
        } else {
            $this->warn('La API respondió vacío');
        }

        return Command::SUCCESS;
    }

}
