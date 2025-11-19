<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\PropertyReservation;
use App\Services\ConversationMessenger;
use App\Services\CommissionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChatReservationController extends Controller
{
    public function store(Request $request, Conversation $conversation, ConversationMessenger $messenger)
    {
        $this->authorizeConversation($request, $conversation);

        abort_unless($conversation->client_id === $request->user()->id, 403);

        $conversation->loadMissing('property');
        $property = $conversation->property;

        abort_unless($property && $property->listing_type === 'rent', 422, 'Esta propiedad no admite reservas.');

        if ($property->user_id === $request->user()->id) {
            return $this->respond($request, [
                'status'  => 'error',
                'message' => 'No puedes reservar tu propia propiedad.',
            ], back()->with('error', 'No puedes reservar tu propia propiedad.'));
        }

        $data = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ]);

        $existingDraft = PropertyReservation::where('property_id', $property->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->first();

        if (PropertyReservation::overlaps($property->id, $data['start_date'], $data['end_date'], $existingDraft?->id)) {
            return $this->respond($request, [
                'status'  => 'error',
                'message' => 'Las fechas seleccionadas ya están reservadas.',
            ], back()->withInput()->withErrors(['start_date' => 'Las fechas se traslapan con otra reserva.']));
        }

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end   = Carbon::parse($data['end_date'])->startOfDay();
        $nights = max(1, $start->diffInDays($end));

        $pricePerNight = (float) $property->price;
        $subtotal = round($pricePerNight * $nights, 2);

        $commissionPercent = app(CommissionService::class)->rateFor($property->user_id, 'rent') ?? 0.0;
        $agentCommission = round($subtotal * ($commissionPercent / 100), 2);
        $agentEarnings = round($subtotal - $agentCommission, 2);

        $reservation = PropertyReservation::updateOrCreate(
            [
                'property_id' => $property->id,
                'user_id'     => $request->user()->id,
                'status'      => 'pending',
            ],
            [
                'start_date'            => $start->toDateString(),
                'end_date'              => $end->toDateString(),
                'total_price'           => $subtotal,
                'payment_status'        => 'unpaid',
                'agent_commission'      => $agentCommission,
                'agent_earnings'        => $agentEarnings,
                'platform_earnings'     => $agentCommission,
                'commission_percentage' => $commissionPercent,
            ]
        );

        session(['current_reservation_id' => $reservation->id]);

        $messenger->send(
            $conversation,
            $request->user()->id,
            sprintf(
                '🧾 Inicié una reservación del %s al %s (%d noche%s).',
                $start->format('d/m/Y'),
                $end->format('d/m/Y'),
                $nights,
                $nights === 1 ? '' : 's'
            )
        );

        return $this->respond($request, [
            'status'       => 'ok',
            'message'      => 'Reserva creada. Completa el pago para confirmarla.',
            'reservation'  => $reservation->only(['id', 'start_date', 'end_date', 'status', 'payment_status', 'total_price']),
            'checkout_url' => route('reservations.checkout', $reservation),
        ], back()->with('success', 'Reserva creada. Completa el pago para finalizar.'));
    }

    protected function authorizeConversation(Request $request, Conversation $conversation): void
    {
        abort_unless(in_array($request->user()->id, [$conversation->agent_id, $conversation->client_id]), 403);
    }

    protected function respond(Request $request, array $payload, Response|RedirectResponse $fallback)
    {
        if ($request->wantsJson()) {
            return response()->json($payload);
        }

        return $fallback;
    }
}
