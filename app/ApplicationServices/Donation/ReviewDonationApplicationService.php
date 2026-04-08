<?php

namespace App\ApplicationServices\Donation;

use App\DTO\Donation\ReviewDonationDTO;
use App\Models\Main\Donation;
use App\UseCases\Donation\ReviewDonationProcess;
use Illuminate\Support\Facades\DB;

class ReviewDonationApplicationService
{
    public function __construct(private ReviewDonationProcess $process) {}

    public function handle(ReviewDonationDTO $dto): Donation
    {
        return DB::transaction(fn () => $this->process->execute($dto));
    }
}
