<?php

namespace App\Services;

use App\Actions\Donation\ApproveDonationAction;
use App\Actions\Donation\CreateDonationAction;
use App\Actions\Donation\RejectDonationAction;
use App\DTO\Donation\CreateDonationDTO;
use App\Enums\DonationStatus;
use App\Exceptions\DonationNotPendingException;
use App\Finders\DonationFinder;
use App\Models\Main\Donation;
use App\Queries\DonationQueries;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DonationService
{
    public function __construct(
        private DonationFinder $finder,
        private DonationQueries $queries,
        private CreateDonationAction $createAction,
        private ApproveDonationAction $approveAction,
        private RejectDonationAction $rejectAction,
    ) {}

    public function create(CreateDonationDTO $dto): Donation
    {
        return $this->createAction->handle($dto);
    }

    public function approve(Donation $donation, int $reviewerUserId): Donation
    {
        if ($donation->status !== DonationStatus::Pending) {
            throw new DonationNotPendingException();
        }

        return $this->approveAction->handle($donation, $reviewerUserId);
    }

    public function reject(Donation $donation, int $reviewerUserId): Donation
    {
        if ($donation->status !== DonationStatus::Pending) {
            throw new DonationNotPendingException();
        }

        return $this->rejectAction->handle($donation, $reviewerUserId);
    }

    public function getApprovedHistory(int $guildId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->queries->getApprovedDonationsWithDonors($guildId, $perPage);
    }

    public function findPendingByGuild(int $guildId): Collection
    {
        return $this->finder->findPendingByGuild($guildId);
    }
}
