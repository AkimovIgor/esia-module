<?php


namespace Modules\Esia\Http\Controllers;

use App\Http\Controllers\Auth\LoginController as AppLoginController;

use App\Services\Robots\RobotsService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;


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

            if (! $user = $this->checkUser($socUser->user['oid'])) {
                $user = $this->createNewUser($socUser);
            }

            auth()->logout();
            auth()->login($user, true);

        } catch (\Exception $e) {
            return redirect()->to('/');
        }

        Session::put('esia_token', $socUser->token);
        Session::put('esia_oid', $socUser->id);

        return redirect()->to('/');
    }

    protected function checkUser($userOid)
    {
        $email = $userOid . '@esia.org';
        return User::where('email', $email)->first();
    }

    protected function createNewUser($socUser)
    {
        $email = $socUser->user['oid'] . '@esia.org';
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
