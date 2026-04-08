<?php

namespace App\Queries;

use App\Enums\DonationStatus;
use App\Models\Main\Donation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DonationQueries
{
    public function getApprovedDonationsWithDonors(int $guildId, int $perPage = 15): LengthAwarePaginator
    {
        return Donation::with('donor')
            ->where('guild_id', $guildId)
            ->where('status', DonationStatus::Approved)
            ->orderByDesc('reviewed_at')
            ->paginate($perPage);
    }
}
