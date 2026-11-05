<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\Pago;
use Illuminate\Support\Facades\Http;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class PagoController extends Controller
{
    public function crearPreferencia()
    {
        $reserva = session('reserva');

        if (!$reserva) {
            return redirect()->route('usuario.turnos.create')
                ->with('error', 'No hay ninguna reserva en proceso.');
        }

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $precio = (float) str_replace(',', '.', $reserva['precio_historico']);

        $client = new PreferenceClient();

        $preference = $client->create([
            "items" => [
                [
                    "title"       => "Reserva de Turno: " . ($reserva['servicio_nombre'] ?? 'Corte de Pelo'),
                    "quantity"    => 1,
                    "unit_price"  => $precio,
                    "currency_id" => "ARS"
                ]
            ],
            "payer" => [
                "email" => "test_user_12345678@testuser.com", // Email genérico de Sandbox
            ],
            "back_urls" => [
                "success" => "https://copy-end-ferrari-protected.trycloudflare.com/pago/success",
                "failure" => "https://copy-end-ferrari-protected.trycloudflare.com/pago/failure",
                "pending" => "https://copy-end-ferrari-protected.trycloudflare.com/pago/pending",
            ],
            "auto_return" => "approved",
        ]);

        // Obtenemos correctamente la URL de pago (sandbox o producción)
        $urlPago = $preference->sandbox_init_point ?? $preference->init_point;

        return redirect($urlPago);
    }

   public function success(Request $request)
    {
        $reserva = session('reserva');

        if (!$reserva) {
            return redirect()->route('usuario.turnos')
                ->with('error', 'La sesión de la reserva expiró.');
        }

        // 1. Crear el turno
        $turno = Turno::create([
            'cliente_id'       => $reserva['cliente_id'],
            'barbero_id'       => $reserva['barbero_id'],
            'servicio_id'      => $reserva['servicio_id'],
            'sucursal_id'      => $reserva['sucursal_id'],
            'fecha'            => $reserva['fecha'],
            'hora'             => $reserva['hora'],
            'estado'           => 'confirmado',
            'precio_historico' => $reserva['precio_historico'],
        ]);

        // 2. Registrar el pago relacionado
        Pago::create([
            'turno_id'             => $turno->id,
            'monto'                => $reserva['precio_historico'],
            'metodo'               => 'mercado_pago',
            'estado'               => 'pagado',
            'payment_id_mp'        => $request->query('payment_id'),
            'collection_id_mp'     => $request->query('collection_id'),
            'preference_id_mp'     => $request->query('preference_id'),
            'merchant_order_id_mp' => $request->query('merchant_order_id'),
        ]);

        // 3. Limpiar sesión
        session()->forget('reserva');

        return redirect()->route('usuario.turnos')
            ->with('success', '¡Pago realizado y turno reservado con éxito!');
    }
    public function failure()
    {
        return redirect()->route('usuario.turnos.create')
            ->with('error', 'El pago fue rechazado o cancelado. Intente nuevamente.');
    }

    public function pending()
    {
        return redirect()->route('usuario.turnos')
            ->with('warning', 'El pago está pendiente de aprobación.');
    }

    /**
     * Procesa el reembolso de un pago mediante la API de Mercado Pago.
     *
     * @param string|int $paymentId
     * @return bool
     */
    public static function reembolsarPagoMP($paymentId)
    {
        $accessToken = config('services.mercadopago.access_token');

        if (!$accessToken || !$paymentId) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ])->post("https://api.mercadopago.com/v1/payments/{$paymentId}/refunds");

        return $response->successful();
    }
}