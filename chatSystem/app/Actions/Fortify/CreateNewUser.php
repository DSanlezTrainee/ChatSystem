<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\ServerInvite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();


        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'status' => 'online',
        ]);

        // Verificar se há um token de convite na sessão
        $inviteToken = Session::get('invite_token');
        if ($inviteToken) {
            // Apenas associar o usuário ao convite, sem expirar o link
            $invite = ServerInvite::where('token', $inviteToken)
                ->where('status', 'pending')
                ->first();

            if ($invite) {
                $invite->update([
                    'invitee_id' => $user->id,
                ]);
            }

            // Limpar o token da sessão
            Session::forget('invite_token');
        }

        return $user;
    }
}
