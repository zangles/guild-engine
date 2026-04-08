<?php

namespace App\DTO\Donation;

readonly class ReviewDonationDTO
{
    public function __construct(
        public int $donation_id,
        public int $reviewer_user_id,
        public string $decision, // 'approved' | 'rejected'
    ) {}
}
