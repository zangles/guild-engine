<?php

namespace App\Actions\Donation;

use App\Enums\DonationStatus;
use App\Models\Main\Donation;
use App\Repositories\DonationRepository;
use Illuminate\Support\Carbon;

class ApproveDonationAction
{
    public function __construct(private DonationRepository $repository) {}

    public function handle(Donation $donation, int $reviewerUserId): Donation
    {
        return $this->repository->update($donation, [
            'status'               => DonationStatus::Approved,
            'reviewed_by_user_id'  => $reviewerUserId,
            'reviewed_at'          => Carbon::now(),
        ]);
    }
}
