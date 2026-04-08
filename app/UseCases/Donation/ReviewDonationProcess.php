<?php

namespace App\UseCases\Donation;

use App\DTO\Donation\ReviewDonationDTO;
use App\Enums\DonationStatus;
use App\Exceptions\DonationNotPendingException;
use App\Finders\DonationFinder;
use App\Models\Main\Donation;
use App\Services\AuditLogService;
use App\Services\DonationService;

class ReviewDonationProcess
{
    public function __construct(
        private DonationFinder $finder,
        private DonationService $donationService,
        private AuditLogService $auditLogService,
    ) {}

    public function execute(ReviewDonationDTO $dto): Donation
    {
        $donation = $this->finder->findByIdOrFail($dto->donation_id);

        if ($donation->status !== DonationStatus::Pending) {
            throw new DonationNotPendingException();
        }

        if ($dto->decision === 'approved') {
            $donation = $this->donationService->approve($donation, $dto->reviewer_user_id);
            $eventType = 'donation.approved';
        } else {
            $donation = $this->donationService->reject($donation, $dto->reviewer_user_id);
            $eventType = 'donation.rejected';
        }

        $this->auditLogService->log(
            $donation->guild_id,
            $dto->reviewer_user_id,
            $donation->user_id,
            $eventType,
            [
                'donation_id' => $donation->id,
                'amount'      => $donation->amount,
                'note'        => $donation->note,
                'decision'    => $dto->decision,
            ]
        );

        return $donation;
    }
}
