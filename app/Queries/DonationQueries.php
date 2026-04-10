<?php

namespace App\Queries;

use App\Enums\DonationStatus;
use App\Models\Main\Donation;
use App\Models\Main\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DonationQueries
{
    public function getApprovedDonationsWithDonors(int $guildId, int $perPage = 15): LengthAwarePaginator
    {
        $donations = Donation::where('guild_id', $guildId)
            ->where('status', DonationStatus::Approved)
            ->orderByDesc('reviewed_at')
            ->paginate($perPage);

        $userIds = $donations->pluck('user_id')->unique()->filter()->values()->toArray();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        foreach ($donations as $donation) {
            $donation->setRelation('donor', $users[$donation->user_id] ?? null);
        }

        return $donations;
    }
}
