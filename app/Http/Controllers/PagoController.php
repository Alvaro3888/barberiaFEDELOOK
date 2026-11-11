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
    /**
     * Prepara y genera una preferencia de pago en la API de Mercado Pago 
     * utilizando los datos temporales guardados en la sesión del cliente. 
     * Redirige automáticamente al usuario a la pasarela de pago (Sandbox o Producción).
     */
    public function crearPreferencia()
    {
        $reserva = session('reserva');

        if (!$reserva) {
            return redirect()->route('usuario.turnos.create')
                ->with('error', 'No hay ninguna reserva en proceso.');
        }

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        // 1. Buscamos todos los servicios seleccionados
        $serviciosElegidos = \App\Models\Servicio::whereIn('id', $reserva['servicios'] ?? [])->get();

        // 2. Creamos un texto detallado que liste los servicios (Ej: "Corte de Pelo + Perfilado de Barba")
        $detallesArray = [];
        foreach ($serviciosElegidos as $servicio) {
            $detallesArray[] = "{$servicio->nombre} (\${$servicio->precio_actual})";
        }

        $tituloCompleto = !empty($detallesArray) 
            ? implode(" + ", $detallesArray) 
            : "Reserva de Turno Múltiple";

        $precioTotal = (float) str_replace(',', '.', $reserva['precio_historico']);

        $client = new PreferenceClient();

        $preference = $client->create([
            "items" => [
                [
                    "title"       => $tituloCompleto, // <-- Acá viaja todo el detalle unido para que salga en la web y en el PDF
                    "description" => "Turno múltiple en barbería - Servicios agendados",
                    "quantity"    => 1,
                    "unit_price"  => $precioTotal,
                    "currency_id" => "ARS"
                ]
            ],
            "payer" => [
                "email" => "test_user_12345678@testuser.com",
            ],
            "statement_descriptor" => "BARBERIA ALVARO",
            "binary_mode"          => true,
            "back_urls" => [
                "success" => "https://genres-revealed-composed-retailer.trycloudflare.com/pago/success",
                "failure" => "https://genres-revealed-composed-retailer.trycloudflare.com/pago/failure",
                "pending" => "https://genres-revealed-composed-retailer.trycloudflare.com/pago/pending",
            ],
            "auto_return" => "approved",
        ]);

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

        // 1. Creación formal del Turno confirmado en el sistema
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

        // ¡IMPORTANTE! Vincular los múltiples servicios en la tabla intermedia
        if (!empty($reserva['servicios'])) {
            $turno->servicios()->sync($reserva['servicios']);
        }

        // 2. Registro del pago vinculado capturando los parámetros de respuesta devueltos por la pasarela
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

        // 3. Limpieza de la variable temporal en sesión
        session()->forget('reserva');

        return redirect()->route('usuario.turnos')
            ->with('success', '¡Pago realizado y turno reservado con éxito!');
    }

    /**
     * Maneja el retorno cuando el pago es rechazado o cancelado por el usuario (Failure URL).
     */
    public function failure()
    {
        return redirect()->route('usuario.turnos.create')
            ->with('error', 'El pago fue rechazado o cancelado. Intente nuevamente.');
    }

    /**
     * Maneja el retorno cuando el pago queda en estado pendiente de acreditación (Pending URL).
     */
    public function pending()
    {
        return redirect()->route('usuario.turnos')
            ->with('warning', 'El pago está pendiente de aprobación.');
    }

    /**
     * Método auxiliar estático para procesar el reembolso total de un pago 
     * mediante una petición HTTP POST a la API oficial de Mercado Pago.
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

        // Petición HTTP a la API de reembolsos de Mercado Pago
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ])->post("https://api.mercadopago.com/v1/payments/{$paymentId}/refunds");

        return $response->successful();
    }
}