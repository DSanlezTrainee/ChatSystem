<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\User;

class TestApiConnection extends Command
{
    protected $signature = 'test:api-connection';
    protected $description = 'Test API connection and authentication';

    public function handle()
    {
        $this->info('Testando conexão com o banco de dados...');

        try {
            // Teste de conexão com banco de dados
            DB::connection()->getPdo();
            $this->info('✓ Conexão com o banco de dados estabelecida!');

            // Verificando se existem usuários
            $userCount = User::count();
            $this->info("✓ Total de usuários: {$userCount}");

            // Verificando se existem salas
            $roomCount = Room::count();
            $this->info("✓ Total de salas: {$roomCount}");

            // Listando salas
            $rooms = Room::all();
            $this->info('Lista de salas:');
            foreach ($rooms as $room) {
                $this->info("ID: {$room->id} | Nome: {$room->name} | Dono: {$room->owner_id}");

                // Verificando usuários na sala
                $users = $room->users;
                $this->info("  Usuários na sala: " . count($users));
                foreach ($users as $user) {
                    $this->info("  - {$user->name} (ID: {$user->id})");
                }
            }
        } catch (\Exception $e) {
            $this->error('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
