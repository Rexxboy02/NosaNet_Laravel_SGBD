<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class MigrateJsonToDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:json-to-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar datos de archivos JSON a la base de datos SQLite';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de datos de JSON a SQLite...');

        // Verificar si ya existen datos en la base de datos
        if (User::count() > 0 || Message::count() > 0) {
            if (!$this->confirm('Ya existen datos en la base de datos. ¿Desea continuar y agregar los datos del JSON?')) {
                $this->info('Migración cancelada.');
                return 0;
            }
        }

        // Migrar usuarios
        $this->info('Migrando usuarios...');
        $usersFile = storage_path('json/users.json');

        if (File::exists($usersFile)) {
            $usersData = json_decode(File::get($usersFile), true);

            if (is_array($usersData)) {
                foreach ($usersData as $userData) {
                    // Verificar si el usuario ya existe
                    $existingUser = User::find($userData['id'] ?? null);

                    if (!$existingUser) {
                        User::create([
                            'id' => $userData['id'] ?? uniqid(),
                            'username' => $userData['username'] ?? '',
                            'email' => $userData['email'] ?? '',
                            'password' => $userData['password'] ?? '',
                            'isProfessor' => $userData['isProfessor'] ?? 'False',
                            'theme' => $userData['theme'] ?? 'light',
                        ]);
                        $this->line('Usuario migrado: ' . ($userData['username'] ?? 'unknown'));
                    } else {
                        $this->line('Usuario ya existe: ' . ($userData['username'] ?? 'unknown'));
                    }
                }
                $this->info('Usuarios migrados correctamente.');
            } else {
                $this->warn('No se encontraron usuarios para migrar.');
            }
        } else {
            $this->warn('Archivo de usuarios no encontrado: ' . $usersFile);
        }

        // Migrar mensajes
        $this->info('Migrando mensajes...');
        $messagesFile = storage_path('json/messages.json');

        if (File::exists($messagesFile)) {
            $messagesData = json_decode(File::get($messagesFile), true);

            if (is_array($messagesData)) {
                foreach ($messagesData as $messageData) {
                    // Verificar si el mensaje ya existe
                    $existingMessage = Message::find($messageData['id'] ?? null);

                    if (!$existingMessage) {
                        Message::create([
                            'id' => $messageData['id'] ?? uniqid(),
                            'user' => $messageData['user'] ?? '',
                            'title' => $messageData['title'] ?? '',
                            'text' => $messageData['text'] ?? '',
                            'asignatura' => $messageData['asignatura'] ?? '',
                            'approved' => $messageData['approved'] ?? 'pending',
                            'status' => $messageData['status'] ?? 'active',
                            'timestamp' => $messageData['timestamp'] ?? date('H:i d/m/Y'),
                            'dangerous_content' => $messageData['dangerous_content'] ?? 'false',
                            'approve_reason' => $messageData['approve_reason'] ?? null,
                            'delete_reason' => $messageData['delete_reason'] ?? null,
                            'moderated_at' => $messageData['moderated_at'] ?? null,
                            'moderated_by' => $messageData['moderated_by'] ?? null,
                            'deleted_at' => $messageData['deleted_at'] ?? null,
                            'deleted_by' => $messageData['deleted_by'] ?? null,
                        ]);
                        $this->line('Mensaje migrado: ' . ($messageData['title'] ?? 'sin título'));
                    } else {
                        $this->line('Mensaje ya existe: ' . ($messageData['title'] ?? 'sin título'));
                    }
                }
                $this->info('Mensajes migrados correctamente.');
            } else {
                $this->warn('No se encontraron mensajes para migrar.');
            }
        } else {
            $this->warn('Archivo de mensajes no encontrado: ' . $messagesFile);
        }

        $this->info('¡Migración completada!');
        $this->info('Total de usuarios en la base de datos: ' . User::count());
        $this->info('Total de mensajes en la base de datos: ' . Message::count());

        return 0;
    }
}
