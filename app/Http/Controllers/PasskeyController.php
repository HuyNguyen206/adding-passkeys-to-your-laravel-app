<?php

namespace App\Http\Controllers;

use App\Models\Passkey;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Gate;

class PasskeyController extends Controller
{
    use Authorizable;

    public function destroy(Passkey $passkey)
    {
        Gate::authorize('destroy', $passkey);
        $passkey->delete();

        return redirect()->back();
    }
}
