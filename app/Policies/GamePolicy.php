<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
{
    public function manage(User $user, ?Game $game = null): bool
    {
        return $user->isAdmin();
    }
}
