<?php

namespace App\Repositories;

use App\Models\Main\Donation;

class DonationRepository
{
    public function create(array $data): Donation
    {
        return Donation::create($data);
    }

    public function update(Donation $donation, array $data): Donation
    {
        $donation->update($data);
        return $donation->fresh();
    }
}
