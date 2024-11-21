<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class SuperUserSeeder extends Seeder
{
    public function run()
    {
        // Crear superusuario con rol de superuser
        $superuser = User::create([
            'name' => 'Super Usuario',
            'email' => 'superuser@example.com',
            'password' => Hash::make('password123'),
            'is_superuser' => true,
        ]);

        // Generar token para el superusuario
        $token = $superuser->createToken('superuser-token')->plainTextToken;

        // Mostrar el token en la consola o guardarlo en el log para uso futuro
        echo "Superusuario creado. Token: " . $token . "\n";
    }
}
