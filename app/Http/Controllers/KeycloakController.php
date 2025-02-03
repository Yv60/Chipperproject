<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

use App\Models\Chirp; // Votre modèle Chirp
use Illuminate\Support\Facades\Auth;

class KeycloakController extends Controller
{
    /**
     * Redirige vers Keycloak pour l'authentification.
     */
    public function redirectToProvider()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    /**
     * Handle the callback from Keycloak.
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('keycloak')->user();

        // Utilisez le modèle Chirp pour récupérer ou créer un utilisateur
        $authUser = Chirp::updateOrCreate(
            ['keycloak_id' => $user->getId()], // Créez un champ keycloak_id dans la table Chirp si nécessaire
            ['name' => $user->getName(), 'email' => $user->getEmail()]
        );

        // Connecter l'utilisateur à Laravel
        Auth::login($authUser, true);

        // Redirigez l'utilisateur vers le tableau de bord ou la page de son espace personnel
        return redirect()->route('dashboard');
    }
}
