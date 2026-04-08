<?php

namespace App\Finders;

use App\Models\Main\Guild;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GuildFinder
{
    public function findById(int $id): ?Guild
    {
        return Guild::find($id);
    }

    public function findByIdOrFail(int $id): Guild
    {
        return Guild::findOrFail($id);
    }
}
