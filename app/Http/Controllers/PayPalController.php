<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PropertyReservation;
use App\Models\Sale;
use App\Models\Visit;


class PayPalController extends Controller
{
    /**
     * Cliente HTTP con cacert (PayPal requiere SSL válido).
     */
    protected function http()
    {
        $caPath = storage_path('app/certs/cacert.pem');

        return Http::withOptions([
            'verify'  => file_exists($caPath) ? $caPath : false, // ⚠️ false sólo local
            'timeout' => 20,
        ]);
    }

    protected function apiBase(): string
    {
        return config('services.paypal.mode') === 'live'
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
            Log::error('PayPal token error', [
                'status' => $resp->status(),
                'body' => $resp->body()
            ]);
            abort(500, 'No se pudo obtener token de PayPal.');
        }

        return $resp->json()['access_token'];
    }

   /**
     * Crear orden en PayPal
     */
    public function createOrder(Request $request)
    {
        $data = $request->validate([
            'reservation_id' => ['required','integer','exists:property_reservations,id'],
            'pay_mode'       => ['nullable','in:full,partial'],
            'host_message'   => ['nullable','string','max:500'], // 👈 SE AGREGA
        ]);

        $reservation = PropertyReservation::with('property')->findOrFail($data['reservation_id']);

        abort_unless($reservation->user_id === $request->user()->id, 403);

        // TOTAL A PAGAR
        $totalToPay = (float) ($reservation->total_price ?? 0);

        // Pago parcial
        $payNow = ($data['pay_mode'] === 'partial')
            ? round($totalToPay * 0.20, 2)
            : $totalToPay;

        $currency = config('services.paypal.currency', 'MXN');

        try {
            $accessToken = $this->getAccessToken();

            /**
             *  GUARDAR EL MENSAJE EN META
             */
            if (!empty($data['host_message'])) {
                $reservation->meta = array_merge((array)$reservation->meta, [
                    'host_message' => $data['host_message']
                ]);
                $reservation->save();
            }

            /**
             * Crear orden PayPal
             */
            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => 'res-'.$reservation->id,
                    'amount' => [
                        'currency_code' => $currency,
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

            // Guardar monto intentado
            $reservation->meta = array_merge((array) $reservation->meta, [
                'paypal_intent_amount' => number_format($payNow, 2, '.', '')
            ]);
            $reservation->save();

            return response()->json(['id' => $json['id']]);

        } catch (\Throwable $e) {
            Log::error('PayPal createOrder Exception', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Excepción al crear la orden.'], 500);
        }
    }



    /**
     * Capturar pago después de aprobar
     */
        public function captureOrder(Request $request)
            {
            $data = $request->validate([
                'order_id' => ['required', 'string'],
                'sale_id'  => ['nullable', 'integer', 'exists:sales,id'],
            ]);

            try {
                $accessToken = $this->getAccessToken();

                $url = $this->apiBase().'/v2/checkout/orders/'.$data['order_id'].'/capture';

                $resp = $this->http()
                    ->withToken($accessToken)
                    ->acceptJson()
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->withBody('{}', 'application/json')
                    ->post($url);

                if (!$resp->successful()) {
                    Log::error('PayPal capture error', [
                        'status' => $resp->status(),
                        'body'   => $resp->body()
                    ]);
                    return response()->json(['error' => 'No se pudo capturar el pago.'], 500);
                }

                $json = $resp->json();

                if (($json['status'] ?? null) !== 'COMPLETED') {
                    Log::warning('PayPal capture no COMPLETED', ['json' => $json]);
                    return response()->json(['error' => 'Pago no completado.'], 400);
                }

                /** ----------------------------
                 *  FLUJO EXISTENTE: RESERVACIÓN
                 *  ---------------------------*/
                $reference = $json['purchase_units'][0]['reference_id'] ?? null;

                if ($reference && str_starts_with($reference, 'res-')) {
                    $reservationId = (int) substr($reference, 4);

                    $reservation = PropertyReservation::find($reservationId);

                    if ($reservation) {

                        $reservation->fill([
                            'status'         => 'confirmed',
                            'payment_status' => 'paid',
                            'payment_method' => 'paypal',
                            'payment_id'     => $json['id'] ?? null,
                            'payer_email'    => data_get($json, 'payer.email_address'),
                            'paid_at'        => now(),
                        ]);

                        $reservation->save();

                        event(new \App\Events\ReservationPaid($reservation));

                        // (aquí dejas tu bloque de conversación automática tal cual lo tenías)
                        try {

                            $property = $reservation->property;
                            $agentId  = $property->user_id;
                            $clientId = $reservation->user_id;

                            $conversation = \App\Models\Conversation::firstOrCreate([
                                'property_id' => $property->id,
                                'agent_id'    => $agentId,
                                'client_id'   => $clientId,
                            ]);

                            $msgText = $reservation->meta['host_message'] ?? 'Hola, acabo de confirmar la reserva.';

                            $conversation->messages()->create([
                                'sender_id' => $clientId,
                                'body'      => $msgText,
                            ]);

                            $conversation->touch();

                        } catch (\Throwable $e) {
                            \Log::error('No se pudo enviar mensaje al agente', ['msg' => $e->getMessage()]);
                        }
                    }
                }

                /** ---------------------------------
                 *  NUEVO: PAGO DE COMISIÓN POR VENTA
                 *  --------------------------------*/
                $redirectUrl = null;

                if (!empty($data['sale_id'])) {
                    $sale = Sale::with('property')->findOrFail($data['sale_id']);

                    // Marcar la comisión como pagada (solo si no lo estaba ya)
                    if (is_null($sale->commission_paid_at)) {
                        $sale->commission_paid_at = now();
                        $sale->save();
                    }

                    // Marcar la propiedad como vendida, si aún no lo está
                    if ($sale->property && is_null($sale->property->sold_at)) {
                        $sale->property->sold_at = now();
                        $sale->property->save();
                    }

                    // Después de pagar comisión, mandamos al dashboard del agente
                    $redirectUrl = route('agent.analytics');
                }

                return response()->json([
                    'success'      => true,
                    'order'        => $json,
                    'redirect_url' => $redirectUrl,   // null si fue solo reserva
                ]);

            } catch (\Throwable $e) {
                Log::error('PayPal captureOrder Exception', ['msg' => $e->getMessage()]);
                return response()->json(['error' => 'Excepción al capturar el pago.'], 500);
            }
        }

    /**
     * Si usas return_url/cancel_url (opcional)
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
                return redirect()->route('properties.map')->with('error', 'Error al confirmar pago.');
            }

            $json = $resp->json();

            if (($json['status'] ?? null) !== 'COMPLETED') {
                return redirect()->route('properties.map')->with('error', 'Pago no completado.');
            }

            $reference = $json['purchase_units'][0]['reference_id'] ?? null;

            if ($reference && str_starts_with($reference, 'res-')) {
                $reservationId = (int) substr($reference, 4);
                $reservation = PropertyReservation::find($reservationId);

                if ($reservation) {
                    $reservation->status         = 'confirmed';
                    $reservation->payment_status = 'paid';
                    $reservation->payment_method = 'paypal';
                    $reservation->payment_id     = $json['id'] ?? null;
                    $reservation->paid_at        = now();
                    $reservation->save();
                }
            }

            return redirect()->route('visits.my')->with('success', '¡Pago confirmado!');

        } catch (\Throwable $e) {
            return redirect()->route('properties.map')->with('error', 'Error al confirmar pago.');
        }
    }

    public function cancelReturn(Request $request)
    {
        return redirect()->route('properties.map')->with('error', 'Pago cancelado en PayPal.');
    }



    // Creaciones de ordenes de comisiones y capturas para ventas
    public function createCommissionOrder(Request $request, Visit $visit)
    {
        abort_unless($visit->agent_id === $request->user()->id, 403);

        $sale = Sale::with('property')->where('visit_id', $visit->id)->latest()->first();

        if (!$sale) {
            return response()->json(['error' => 'No hay venta registrada.'], 404);
        }

        if ($sale->commission_paid_at) {
            return response()->json(['error' => 'La comisión ya fue pagada.'], 400);
        }

        $amount   = $sale->commission_amount;
        $currency = config('services.paypal.currency', 'MXN');

        try {
            $accessToken = $this->getAccessToken();

            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => 'sale-'.$sale->id,
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                    'description' => 'Pago de comisión por venta de propiedad #'.$sale->property_id,
                ]],
                'application_context' => [
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action'         => 'PAY_NOW',
                ],
            ];

            $resp = $this->http()
                ->withToken($accessToken)
                ->acceptJson()
                ->post($this->apiBase().'/v2/checkout/orders', $payload);

            if (!$resp->successful()) {
                Log::error('PayPal createCommissionOrder error', [
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);
                return response()->json(['error' => 'No se pudo crear la orden con PayPal.'], 500);
            }

            return response()->json(['id' => $resp->json('id')]);
        } catch (\Throwable $e) {
            Log::error('PayPal createCommissionOrder Exception', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Excepción al crear la orden.'], 500);
        }
    }


    // Captura de orden de comisión
    public function captureCommissionOrder(Request $request, Visit $visit)
    {
        abort_unless($visit->agent_id === $request->user()->id, 403);

        $data = $request->validate([
            'order_id' => ['required', 'string'],
            'sale_id'  => ['required', 'integer', 'exists:sales,id'],
        ]);

        try {
            $sale = Sale::with('property')
                ->where('id', $data['sale_id'])
                ->where('visit_id', $visit->id)
                ->latest()
                ->first();

            if (!$sale) {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }

            if ($sale->commission_paid_at) {
                return response()->json(['error' => 'La comisión ya fue pagada.'], 400);
            }

            $accessToken = $this->getAccessToken();
            $url         = $this->apiBase().'/v2/checkout/orders/'.$data['order_id'].'/capture';

            $resp = $this->http()
                ->withToken($accessToken)
                ->acceptJson()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withBody('{}', 'application/json')
                ->post($url);

            if (!$resp->successful()) {
                Log::error('PayPal captureCommissionOrder error', [
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);
                return response()->json(['error' => 'No se pudo capturar el pago.'], 500);
            }

            $json = $resp->json();

            if (($json['status'] ?? null) !== 'COMPLETED') {
                Log::warning('PayPal captureCommissionOrder no COMPLETED', ['json' => $json]);
                return response()->json(['error' => 'Pago no completado.'], 400);
            }

            $sale->commission_paid_at = now();
            $sale->save();

            if ($sale->property && is_null($sale->property->sold_at)) {
                $sale->property->sold_at = now();
                $sale->property->save();
            }

            return response()->json([
                'success'  => true,
                'redirect' => route('agent.analytics'),
            ]);
        } catch (\Throwable $e) {
            Log::error('PayPal captureCommissionOrder Exception', ['msg' => $e->getMessage()]);
            return response()->json([
                'error' => 'Excepción al capturar el pago.'
            ], 500);
        }
    }

}
