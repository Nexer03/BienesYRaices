<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PropertyReservation;

class PayPalController extends Controller
{
    /**
     * Cliente HTTP centralizado.
     * Usa el cacert.pem si existe; en su ausencia, desactiva verify SOLO en local.
     * Para producción: asegúrate de tener el cacert y dejar verify apuntando al archivo.
     */
    protected function http()
    {
        $caPath = storage_path('app/certs/cacert.pem');

        return Http::withOptions([
            'verify'  => file_exists($caPath) ? $caPath : false, // ⚠️ false solo para pruebas locales
            'timeout' => 20,
        ]);
    }

    protected function apiBase(): string
    {
        $mode = config('services.paypal.mode', 'sandbox');
        return $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    protected function getAccessToken(): string
    {
        $clientId = config('services.paypal.client_id');
        $secret   = config('services.paypal.secret');

        $resp = $this->http()
            ->asForm()
            ->withBasicAuth($clientId, $secret)
            ->post($this->apiBase().'/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$resp->successful()) {
            Log::error('PayPal token error', ['status' => $resp->status(), 'body' => $resp->body()]);
            abort(500, 'No se pudo obtener token de PayPal.');
        }

        return $resp->json()['access_token'] ?? '';
    }

    public function createOrder(Request $request)
    {
        $data = $request->validate([
            'reservation_id' => ['required','integer','exists:property_reservations,id'],
            'pay_mode'       => ['nullable','in:full,partial'],
        ]);

        $reservation = PropertyReservation::with('property')->findOrFail($data['reservation_id']);

        // Seguridad: solo el dueño puede crear la orden
        abort_unless($reservation->user_id === $request->user()->id, 403, 'No autorizado');

        // Desglose (debe coincidir con tu checkout)
        $price      = (float) $reservation->property->price;
        $nights     = max(1, (new \Carbon\Carbon($reservation->start_date))
                            ->diffInDays((new \Carbon\Carbon($reservation->end_date))));
        $subtotal   = round($price * $nights, 2);
        $serviceFee = round($subtotal * 0.05, 2);
        $taxes      = round(($subtotal + $serviceFee) * 0.16, 2);
        $total      = round($subtotal + $serviceFee + $taxes, 2);

        $payNow = ($data['pay_mode'] ?? 'full') === 'partial'
            ? round($total * 0.20, 2)
            : $total;

        $currency = config('services.paypal.currency', 'MXN');

        try {
            $accessToken = $this->getAccessToken();

            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => 'res-'.$reservation->id,
                    'amount' => [
                        'currency_code' => $currency,
                        // SIEMPRE string con 2 decimales
                        'value' => number_format($payNow, 2, '.', ''),
                    ],
                    'description' => 'Reserva #'.$reservation->id.' | Propiedad: '.$reservation->property->title,
                ]],
                'application_context' => [
                    'brand_name'          => 'Sin Beca No Hay Renta',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action'         => 'PAY_NOW',
                ],
            ];

            $resp = $this->http()
                ->withToken($accessToken)
                ->acceptJson()
                ->post($this->apiBase().'/v2/checkout/orders', $payload);

            if (!$resp->successful()) {
                Log::error('PayPal createOrder error', [
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);
                return response()->json(['error' => 'No se pudo crear la orden con PayPal.'], 500);
            }

            $json = $resp->json();
            if (empty($json['id'])) {
                Log::error('PayPal createOrder sin id', ['json' => $json]);
                return response()->json(['error' => 'Respuesta inválida de PayPal.'], 500);
            }

            // Guardar meta (si tienes columna JSON 'meta')
            try {
                $reservation->meta = array_merge((array)($reservation->meta ?? []), [
                    'paypal_intent_amount' => number_format($payNow, 2, '.', ''),
                ]);
                $reservation->save();
            } catch (\Throwable $e) {
                Log::warning('No se pudo guardar meta de reserva', ['msg' => $e->getMessage()]);
            }

            return response()->json(['id' => $json['id']]);

        } catch (\Throwable $e) {
            Log::error('PayPal createOrder EX', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Excepción al crear la orden.'], 500);
        }
    }

    public function captureOrder(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required','string'],
        ]);

        try {
            $accessToken = $this->getAccessToken();

            $url = $this->apiBase().'/v2/checkout/orders/'.$data['order_id'].'/capture';

            // Enviar cuerpo JSON vacío y Content-Type correcto
            $resp = $this->http()
                ->withToken($accessToken)
                ->acceptJson()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withBody('{}', 'application/json')
                ->post($url);

            if (!$resp->successful()) {
                Log::error('PayPal capture error', [
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);
                return response()->json(['error' => 'No se pudo capturar el pago.'], 500);
            }

            $json = $resp->json();

            if (($json['status'] ?? '') !== 'COMPLETED') {
                Log::warning('PayPal capture no COMPLETED', ['json' => $json]);
                return response()->json(['error' => 'Pago no completado.'], 400);
            }

            // Marca la reserva como pagada (reference_id: 'res-123')
            $reference = $json['purchase_units'][0]['reference_id'] ?? null;
            if ($reference && str_starts_with($reference, 'res-')) {
                $reservationId = (int) substr($reference, 4);
                if ($reservationId > 0) {
                    $reservation = PropertyReservation::find($reservationId);
                    if ($reservation) {
                        $reservation->fill([
                            'status' => 'confirmed',
                            'payment_status' => 'paid',
                            'payment_method' => 'paypal',
                            'payment_id' => $json['id'] ?? null,
                            'payer_email' => data_get($json, 'payer.email_address', $reservation->payer_email),
                        ]);
                        $reservation->save();

                        event(new \App\Events\ReservationPaid($reservation));
                    }
                }
            }

            return response()->json([
                'success' => true,
                'order'   => $json,
            ]);

        } catch (\Throwable $e) {
            Log::error('PayPal captureOrder EX', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Excepción al capturar el pago.'], 500);
        }
    }

    /**
     * Endpoint para el return GET del Smart Buttons (si lo usas con return_url/cancel_url)
     * Redirige a una ruta amigable después de confirmar.
     */
    public function captureReturn(Request $request)
    {
        $orderId = $request->query('token');
        if (!$orderId) {
            return redirect()->route('properties.map')->with('error', 'Falta token de PayPal.');
        }

        try {
            $accessToken = $this->getAccessToken();

            $resp = $this->http()
                ->withToken($accessToken)
                ->acceptJson()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withBody('{}', 'application/json')
                ->post($this->apiBase().'/v2/checkout/orders/'.$orderId.'/capture');

            if (!$resp->successful()) {
                Log::error('PayPal capture (GET return) error', [
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);
                return redirect()->route('properties.map')->with('error', 'Ocurrió un error al confirmar el pago.');
            }

            $json = $resp->json();
            if (($json['status'] ?? '') !== 'COMPLETED') {
                Log::warning('PayPal capture (GET return) no COMPLETED', ['json' => $json]);
                return redirect()->route('properties.map')->with('error', 'Pago no completado.');
            }

            $reference = $json['purchase_units'][0]['reference_id'] ?? null;
            if ($reference && str_starts_with($reference, 'res-')) {
                $reservationId = (int) substr($reference, 4);
                if ($reservationId > 0) {
                    $reservation = PropertyReservation::find($reservationId);
                    if ($reservation) {
                        $reservation->status         = 'paid';
                        $reservation->payment_method = 'paypal';
                        $reservation->payment_id     = $json['id'] ?? null;
                        $reservation->save();

                        event(new \App\Events\ReservationPaid($reservation));
                    }
                }
            }

            return redirect()->route('visits.my')->with('success', '¡Pago confirmado!');

        } catch (\Throwable $e) {
            Log::error('PayPal captureReturn EX', ['msg' => $e->getMessage()]);
            return redirect()->route('properties.map')->with('error', 'Excepción al confirmar el pago.');
        }
    }

    public function cancelReturn(Request $request)
    {
        return redirect()->route('properties.map')->with('error', 'Pago cancelado en PayPal.');
    }
}
