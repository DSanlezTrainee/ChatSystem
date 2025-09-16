<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Room;

class AddAdminToAllTalk extends Command
{
    protected $signature = 'rooms:add-admin-to-all-talk';
    protected $description = 'Add the admin user to the All Talk room';

    public function handle()
    {
        $this->info('Adicionando usuário admin à sala All Talk...');

        try {
            // Encontra o usuário admin
            $admin = User::where('email', 'test@example.com')->first();

            if (!$admin) {
                $this->error('Usuário admin não encontrado');
                return Command::FAILURE;
            }

            $this->info("Admin encontrado: {$admin->name} (ID: {$admin->id})");

            // Encontra a sala All Talk
            $allTalk = Room::where('name', 'All Talk')->first();

            if (!$allTalk) {
                $this->error('Sala All Talk não encontrada');
                return Command::FAILURE;
            }

            $this->info("Sala encontrada: {$allTalk->name} (ID: {$allTalk->id})");

            // Verifica se o usuário já está na sala
            if ($allTalk->users()->where('users.id', $admin->id)->exists()) {
                $this->info('O admin já está na sala All Talk');
            } else {
                // Adiciona o usuário à sala
                $allTalk->users()->attach($admin->id);
                $this->info('Admin adicionado à sala All Talk com sucesso!');
            }

            // Verifica quantos usuários estão na sala
            $userCount = $allTalk->users()->count();
            $this->info("Total de usuários na sala agora: {$userCount}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro ao adicionar admin à sala: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
