<?php

namespace App\Policies;

use App\Models\Passkey;
use App\Models\User;

class PasskeyPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function destroy(User $user, Passkey $passkey)
    {
        return $passkey->user_id === $user->id;
    }
}
