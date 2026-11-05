<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $ajustesIniciales = [
            'whatsapp_numero'  => '5493881234567',
            'whatsapp_mensaje' => 'Hola Fede looK, quiero hacer una consulta sobre un turno.',
            'instagram_url'   => 'https://instagram.com/fedelookbarberia',
            'facebook_url'    => 'https://facebook.com/fedelookbarberia',
            'tiktok_url'      => 'https://tiktok.com/@fedelookbarberia',
        ];

        foreach ($ajustesIniciales as $clave => $valor) {
            Configuracion::set($clave, $valor);
        }
    }
}