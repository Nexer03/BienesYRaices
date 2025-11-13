<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\SystemCommission;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * POST /reservations/preview
     */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after:start_date'],
        ]);

        $property = Property::with('images')->findOrFail($data['property_id']);

        $start  = Carbon::parse($data['start_date'])->startOfDay();
        $end    = Carbon::parse($data['end_date'])->startOfDay();
        $nights = max(1, $start->diffInDays($end));

        $price    = (float) $property->price;
        $subtotal = round($price * $nights, 2);

        //  total que paga el cliente SIN cargos extras
        $total = $subtotal;

        //  tipo de operación
        $listingType = 'rent';

        //  agente dueño de la propiedad
        $agentId = $property->user_id;

        //  comisión específica del agente
        $agentCommissionConf = SystemCommission::query()
            ->where('user_id', $agentId)
            ->whereIn('listing_type', [$listingType, 'both'])
            ->orderByDesc('effective_from')
            ->first();

        //  comisión general del sistema
        $globalCommissionConf = SystemCommission::query()
            ->whereNull('user_id')
            ->whereIn('listing_type', [$listingType, 'both'])
            ->orderByDesc('effective_from')
            ->first();

        //  resultado final
        $commissionConf = $agentCommissionConf ?? $globalCommissionConf;
        $commissionPercent = $commissionConf ? (float) $commissionConf->percentage : 0.0;

        //  Cálculo de comisiones
        $agentCommission = round($subtotal * ($commissionPercent / 100), 2);
        $agentEarnings   = round($subtotal - $agentCommission, 2);
        $platformEarnings = $agentCommission;
        // si el usuario escribió mensaje, se guarda temporalmente
        if ($request->filled('host_message')) {
            $reservation->meta = array_merge((array)$reservation->meta, [
                'host_message' => $request->host_message
            ]);
            $reservation->save();
        }

        // Guardar en DB
        $reservation = PropertyReservation::updateOrCreate(
            [
                'property_id' => $property->id,
                'user_id'     => $request->user()->id,
                'status'      => 'pending',
            ],
            [
                'start_date'            => $start->toDateString(),
                'end_date'              => $end->toDateString(),
                'total_price'           => $total,
                'payment_status'        => 'unpaid',
                'agent_commission'      => $agentCommission,
                'agent_earnings'        => $agentEarnings,
                'platform_earnings'     => $platformEarnings,
                'commission_percentage' => $commissionPercent,
            ]
        );

        session(['current_reservation_id' => $reservation->id]);

        $reservation->setAttribute('nights', $nights);

        return view('reservations.checkout', [
            'property'    => $property,
            'reservation' => $reservation,
            'price'       => $price,
            'subtotal'    => $subtotal,
            'total'       => $total,

            // Para depuración
            'agentCommission'  => $agentCommission,
            'agentEarnings'    => $agentEarnings,
            'platformEarnings' => $platformEarnings,
            'commissionPercent'=> $commissionPercent,
        ]);
    }

    /**
     * GET /reservations/{reservation}/checkout
     */
    public function checkout(PropertyReservation $reservation, Request $request)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);

        $reservation->load('property.images');

        $nights = $reservation->nights;
        $price  = (float) $reservation->property->price;
        $subtotal = round($price * $nights, 2);
        $total = $reservation->total_price ?? $subtotal;

        return view('reservations.checkout', [
            'reservation' => $reservation->setAttribute('nights', $nights),
            'property'    => $reservation->property,
            'price'       => $price,
            'subtotal'    => $subtotal,
            'total'       => $total,
            'agentCommission'  => $reservation->agent_commission,
            'agentEarnings'    => $reservation->agent_earnings,
            'platformEarnings' => $reservation->platform_earnings,
            'commissionPercent'=> $reservation->commission_percentage,
        ]);
    }
}
