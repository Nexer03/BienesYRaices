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
            ->orderByDesc('effective_from')
            ->get();
    }

    protected function matchCommission(?int $agentId, string $listingType): ?SystemCommission
    {
        $listingType = in_array($listingType, ['sale', 'rent'], true) ? $listingType : 'both';

        return $this->commissions
            ->filter(function (SystemCommission $commission) use ($agentId, $listingType) {
                $matchesAgent = $commission->user_id === $agentId;
                $matchesDefault = $commission->user_id === null;
                $matchesType = in_array($commission->listing_type, ['both', $listingType], true);

                return $matchesType && ($matchesAgent || $matchesDefault);
            })
            ->sortByDesc(function (SystemCommission $commission) use ($agentId) {
                $isExactAgent = $commission->user_id === $agentId ? 1 : 0;
                $effectiveFrom = $commission->effective_from?->getTimestamp() ?? 0;

                return ($isExactAgent * 10_000_000) + $effectiveFrom;
            })
            ->first();
    }

    public function rateFor(?int $agentId, string $listingType): ?float
    {
        return $this->matchCommission($agentId, $listingType)?->percentage;
    }

    public function calculate(?int $agentId, string $listingType, float $amount): float
    {
        $rate = $this->rateFor($agentId, $listingType);

        if ($rate === null) {
            return 0.0;
        }

        return $this->applyRate($rate, $amount);
    }

    public function customerRateFor(?int $agentId, string $listingType): ?float
    {
        return $this->matchCommission($agentId, $listingType)?->customer_percentage;
    }

    public function calculateCustomer(?int $agentId, string $listingType, float $amount): float
    {
        $rate = $this->customerRateFor($agentId, $listingType);

        if ($rate === null) {
            return 0.0;
        }

        return $this->applyRate($rate, $amount);
    }

    protected function applyRate(float $rate, float $amount): float
    {
        return round(($amount * $rate) / 100, 2);
    }
}
