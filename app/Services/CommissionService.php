<?php

namespace App\Services;

use App\Models\SystemCommission;
use Illuminate\Support\Collection;

class CommissionService
{
    protected Collection $commissions;

    public function __construct()
    {
        $this->commissions = SystemCommission::query()
            ->orderByDesc('user_id')
            ->get();
    }

    public function rateFor(?int $agentId, string $listingType): ?float
    {
        $listingType = $listingType === 'sale' ? 'sale' : ($listingType === 'rent' ? 'rent' : 'both');

        $match = $this->commissions
            ->filter(function (SystemCommission $commission) use ($agentId, $listingType) {
                $matchesAgent = $commission->user_id === $agentId;
                $matchesDefault = $commission->user_id === null;
                $matchesType = in_array($commission->listing_type, ['both', $listingType], true);

                return $matchesType && ($matchesAgent || $matchesDefault);
            })
            ->sortByDesc(fn (SystemCommission $commission) => $commission->user_id === $agentId ? 1 : 0)
            ->first();

        return $match?->percentage;
    }

    public function calculate(?int $agentId, string $listingType, float $amount): float
    {
        $rate = $this->rateFor($agentId, $listingType);

        if ($rate === null) {
            return 0.0;
        }

        return round(($amount * $rate) / 100, 2);
    }
}
