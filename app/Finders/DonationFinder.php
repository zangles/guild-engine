<?php

namespace App\Finders;

use App\Enums\DonationStatus;
use App\Models\Main\Donation;
use Illuminate\Database\Eloquent\Collection;

class DonationFinder
{
    public function findById(int $id): ?Donation
    {
        return Donation::find($id);
    }

    public function findByIdOrFail(int $id): Donation
    {
        return Donation::findOrFail($id);
    }

    public function findPendingByGuild(int $guildId): Collection
    {
        return Donation::where('guild_id', $guildId)
            ->where('status', DonationStatus::Pending)
            ->get();
    }
}
