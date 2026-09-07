<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'nombre' => 'Administrador',
                'email' => 'admin@barberia.com',
                'password' => Hash::make('password'),
                'telefono' => '3881111111',
                'rol_id' => 1,
            ],
            [
                'nombre' => 'Carlos Gómez',
                'email' => 'barbero@barberia.com',
                'password' => Hash::make('password'),
                'telefono' => '3882222222',
                'rol_id' => 2,
            ],
            [
                'nombre' => 'Luis Martínez',
                'email' => 'cliente@barberia.com',
                'password' => Hash::make('password'),
                'telefono' => '3883333333',
                'rol_id' => 3,
            ],
        ]);
    }
}
