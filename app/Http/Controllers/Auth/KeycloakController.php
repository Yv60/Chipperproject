<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;

class KeycloakController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    public function handleProviderCallback()
    {
        $user = Socialite::driver('keycloak')->user();

        // Vous pouvez ici enregistrer ou authentifier l'utilisateur
        // Par exemple, vous pouvez créer un nouvel utilisateur ou connecter un utilisateur existant
        Auth::login($user);

        return redirect()->route('user.dashboard');
    }
}
