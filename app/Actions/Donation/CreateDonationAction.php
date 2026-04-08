<?php

namespace App\Actions\Donation;

use App\DTO\Donation\CreateDonationDTO;
use App\Enums\DonationStatus;
use App\Models\Main\Donation;
use App\Repositories\DonationRepository;

class CreateDonationAction
{
    public function __construct(private DonationRepository $repository) {}

    public function handle(CreateDonationDTO $dto): Donation
    {
        return $this->repository->create([
            'guild_id' => $dto->guild_id,
            'user_id'  => $dto->user_id,
            'amount'   => $dto->amount,
            'note'     => $dto->note,
            'status'   => DonationStatus::Pending,
        ]);
    }
}
