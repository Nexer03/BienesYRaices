<?php

namespace App\Http\Controllers;

use App\Models\AlertCriteria;
use App\Models\AlertDeliveryLog;
use App\Services\AlertMatchingService;
use App\Services\AlertDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function __construct(
        private readonly AlertMatchingService $matcher,
        private readonly AlertDispatchService $dispatcher
    ) {
    }

    public function index(Request $request): View
    {
        $criteria = $request->user()
            ->alertCriteria()
            ->with(['channelPreferences', 'deliveryLogs' => fn ($q) => $q->latest()->limit(5)])
            ->latest()
            ->get();

        $nextMatches = [];
        foreach ($criteria as $item) {
            $nextMatches[$item->id] = $this->matcher->findNewProperties($item)->count();
        }

        return view('alerts.index', [
            'criteria' => $criteria,
            'nextMatches' => $nextMatches,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'frequency' => ['required', 'in:immediate,daily,weekly'],
            'city' => ['nullable', 'string', 'max:255'],
            'listing_type' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'price_min' => ['nullable', 'numeric'],
            'price_max' => ['nullable', 'numeric'],
            'bedrooms' => ['nullable', 'numeric'],
            'bathrooms' => ['nullable', 'numeric'],
        ]);

        $filters = collect($data)->only(['city', 'listing_type', 'type', 'price_min', 'price_max', 'bedrooms', 'bathrooms'])->filter()->all();

        $criteria = $request->user()->alertCriteria()->create([
            'name' => $data['name'],
            'frequency' => $data['frequency'],
            'filters' => $filters,
            'consented_at' => now(),
            'last_consent_refresh_at' => now(),
        ]);

        $criteria->channelPreferences()->create([
            'channel' => 'bell',
            'frequency' => $data['frequency'],
            'enabled' => true,
            'consented_at' => now(),
        ]);

        $criteria->load('channelPreferences');
        $matches = $this->matcher->findNewProperties($criteria);
        foreach ($criteria->channelPreferences as $channel) {
            $this->dispatcher->dispatchDigest($criteria, $matches, $channel, 'manual');
        }

        return redirect()->route('alerts.index')->with('status', 'Alerta creada y consentimientos registrados.');
    }

    public function update(Request $request, AlertCriteria $alert): RedirectResponse
    {
        abort_unless($alert->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'frequency' => ['required', 'in:immediate,daily,weekly'],
            'city' => ['nullable', 'string', 'max:255'],
            'listing_type' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'price_min' => ['nullable', 'numeric'],
            'price_max' => ['nullable', 'numeric'],
            'bedrooms' => ['nullable', 'numeric'],
            'bathrooms' => ['nullable', 'numeric'],
            'is_paused' => ['boolean'],
        ]);

        $filters = collect($data)
            ->only(['city', 'listing_type', 'type', 'price_min', 'price_max', 'bedrooms', 'bathrooms'])
            ->filter()
            ->all();

        $alert->update([
            'name' => $data['name'],
            'frequency' => $data['frequency'],
            'filters' => $filters,
            'is_paused' => $data['is_paused'] ?? false,
            'last_consent_refresh_at' => now(),
        ]);

        $bellChannel = $alert->channelPreferences()->firstOrNew(['channel' => 'bell']);
        $bellChannel->fill([
            'frequency' => $data['frequency'],
            'enabled' => true,
            'unsubscribed_at' => null,
            'consented_at' => $bellChannel->consented_at ?? now(),
        ]);
        $bellChannel->save();

        $alert->channelPreferences()
            ->where('channel', '!=', 'bell')
            ->update(['enabled' => false, 'unsubscribed_at' => now()]);

        return redirect()->route('alerts.index')->with('status', 'Preferencias de alerta actualizadas.');
    }

    public function destroy(Request $request, AlertCriteria $alert): RedirectResponse
    {
        abort_unless($alert->user_id === $request->user()->id, 403);
        $alert->delete();

        return redirect()->route('alerts.index')->with('status', 'Alerta eliminada.');
    }

    public function pause(Request $request, AlertCriteria $alert): RedirectResponse
    {
        abort_unless($alert->user_id === $request->user()->id, 403);
        $alert->update(['is_paused' => true]);

        return redirect()->route('alerts.index')->with('status', 'Alerta pausada.');
    }

    public function resume(Request $request, AlertCriteria $alert): RedirectResponse
    {
        abort_unless($alert->user_id === $request->user()->id, 403);
        $alert->update(['is_paused' => false]);

        return redirect()->route('alerts.index')->with('status', 'Alerta reactivada.');
    }

    public function unsubscribe(AlertCriteria $alert, string $channel)
    {
        $preference = $alert->channelPreferences()->where('channel', $channel)->first();

        if ($preference) {
            $preference->update([
                'unsubscribed_at' => now(),
                'enabled' => false,
            ]);
        }

        $alert->update(['last_unsubscribe_at' => now()]);

        return redirect()->route('home')->with('status', 'Te diste de baja del canal ' . $channel);
    }

    public function metrics(Request $request)
    {
        $logs = AlertDeliveryLog::whereHas('criteria', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->get();

        return response()->json([
            'total' => $logs->count(),
            'por_canal' => $logs->groupBy('channel')->map->count(),
            'errores' => $logs->where('status', 'failed')->count(),
        ]);
    }
}
