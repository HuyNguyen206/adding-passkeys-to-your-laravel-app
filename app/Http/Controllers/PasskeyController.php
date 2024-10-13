<?php

namespace App\Http\Controllers;

use App\Models\Passkey;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;

class PasskeyController extends Controller
{
    use Authorizable;

    public function destroy(Passkey $passkey)
    {
        Gate::authorize('destroy', $passkey);
        $passkey->delete();

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $data = $request->validateWithBag('createPasskey', [
            'passkey' => ['required', 'json'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        /**
         * @var PublicKeyCredential $publicKeyCredential
         */
        $publicKeyCredential = (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
            ->create()
            ->deserialize($data['passkey'], PublicKeyCredential::class, 'json');

        if (!$publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
            return to_route('login');
        }

        try {
            $publicKeyCredentialSource = AuthenticatorAttestationResponseValidator::create()->check(
                authenticatorAttestationResponse: $publicKeyCredential->response,
                publicKeyCredentialCreationOptions: Session::get('passkey-registration-options'),
                request: $request->getHost(),
                securedRelyingPartyId: ['localhost']
            );

        } catch (\Throwable $ex) {
            throw ValidationException::withMessages([
                'name' => $ex->getMessage()
            ])->errorBag('createPasskey');
        }

        $request->user()->passkeys()->create([
            'name' => $data['name'],
            'credential_id' => $publicKeyCredentialSource->publicKeyCredentialId,
            'data' => $publicKeyCredentialSource
        ]);

        return to_route('profile.edit')->withFragment('managePasskeys');
    }
}
