<?php


namespace Modules\Esia\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\Esia\Entities\EsiaAccount;


class EsiaLoginController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider(Request $request)
    {
        $redirectUrl = Socialite::driver('esia')->stateless()->buildUrl();
        return redirect()->to($redirectUrl);
    }

    /**
     *
     */
    public function handleProviderCallback(Request $request)
    {
        try {
            $socUser = Socialite::driver('esia')->stateless()->user();

            if (! $user = $this->checkUser($socUser)) {
                $user = $this->createNewUser($socUser);
            }

            auth()->logout();
            auth()->login($user, true);

        } catch (\Exception $e) {
            return redirect()->to('/');
        }

        $request->session()->put('esia_oid', $socUser->id);

        if (!$account = EsiaAccount::where('oid', $socUser->id)->first()) {
            $account = new EsiaAccount([
                'user_id' => $user->id,
                'oid' => $socUser->id,
                'token' => $socUser->token,
            ]);
            $account->save();
        }

        return redirect()->to('/');
    }

    /**
     * @param $socUser
     * @return mixed
     */
    protected function checkUser($socUser)
    {
        $email = $socUser->user['email'] ?? null;
        $email = $email ?: $socUser->user['oid'] . '@esia.org';
        return User::where('email', $email)->first();
    }

    /**
     * @param $socUser
     * @return User
     */
    protected function createNewUser($socUser)
    {
        $email = $socUser->user['email'] ?? null;
        $email = $email ?: $socUser->user['oid'] . '@esia.org';
        $password = $socUser->user['oid'] . Str::random(10);
        $firstName = $socUser->user['firstName'] ?? '';
        $lastName = $socUser->user['lastName'] ?? '';
        $middleName = $socUser->user['middleName'] ?? '';

        $user = new User([
            'name' => $firstName . ' ' . $lastName . ' ' . $middleName,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $user->save();

        $user->attachRoles([config('esia.default_role')]);

        return $user;
    }
}
