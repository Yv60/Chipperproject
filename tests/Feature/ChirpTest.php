<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Chirp;

class ChirpTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->get('/chirps');
        //$contents = $this->view("chirps.index");  

        //$contents ->assertSee("Chirps");
        $response->assertStatus(200);
        //$contents ->assertSee("Renseigner votre information?");
        //$contents ->assertSee("Laravel is good between");
    }

    /*   Création de "chirps"   */
    public function test_un_utilisateur_peut_creer_un_chirp()
    {
        // Simuler un utilisateur connecté
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);
        // Envoyer une requête POST pour créer un chirp
        $reponse = $this->post('/chirps', ['message' => 'Mon premier chirp !']);

        // Vérifier que le chirp a été ajouté à la base de donnée
        
        $reponse->assertStatus(302);
        $this->assertDatabaseHas('chirps', ['message' => 'Mon premier chirp !','user_id' => $utilisateur->id,]);

        /* 
            J'ai eu à corriger deux erreurs dont le premier est not found 201 en 302 et le second 
            Failed asserting that a row in the table [chirps] matches the attributes {
                "content": "Mon premier chirp !",
                "user_id": 17
            } 
            je l'ai corriger en remplaçant message pas content dans le controller et dans le models
        */

    }
    

    /*  Validation des "chirps"  */

    public function test_un_chirp_ne_peut_pas_avoir_un_contenu_vide()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);
        $reponse = $this->post('/chirps', ['message' => ''
        ]);
        $reponse->assertSessionHasErrors(['message']);
    }


    public function test_un_chirp_ne_peut_pas_depasse_255_caracteres()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);
        $reponse = $this->post('/chirps', ['message' => str_repeat('a', 256)]);
        $reponse->assertSessionHasErrors(['message']);
    }

    /* 
        J'ai eu à corriger une erreur dont 
        Failed asserting that false is true.

        The following errors occurred during the last request:

        The content field is required.
        Je l'ai corriger en remplaçant contenu pas content
        
    */



    /*  Affichage des "chirps"  */

    public function test_les_chirps_sont_affiches_sur_la_page_d_accueil()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        $chirps = Chirp::factory(3)->create();
        $reponse = $this->get('/chirps');
        $this-> assertSame(3,$chirps->count());
        foreach ($chirps as $chirp) 
        {
            $reponse->assertSee($chirp->message);
        }

    }

    /*
        Voici l'erreur que j'ai rencontrer 
            Call to undefined method App\Models\Chirp::factory()
        pour le résourdre j'ai créer un nouveau factories avec la commande
        php artisan make:factory ChirpFactory --model=Chirp
    */

    public function test_un_utilisateur_peut_modifier_son_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
        $this->actingAs($utilisateur);
        $reponse = $this->put("/chirps/{$chirp->id}", ['message' => 'Chirp modifié']);
        $reponse->assertStatus(302);
        // Vérifie si le chirp existe dans la base de donnée.
        $this->assertDatabaseHas('chirps', ['id' => $chirp->id,'message' => 'Chirp modifié',]);
    }

    public function test_un_utilisateur_peut_supprimer_son_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
        $this->actingAs($utilisateur);
        $reponse = $this->delete("/chirps/{$chirp->id}");
        $reponse->assertStatus(302);
        $this->assertDatabaseMissing('chirps', ['id' => $chirp->id,]);
    }

    public function test_permissions_pour_les_chirps()
    {
        $utilisateur1 = User::factory()->create();
        $utilisateur2 = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur1->id]);
        $this->actingAs($utilisateur2);
        /* Modification d'un chirps de l'utilisateur1 par l'utilisateur2 */

        $reponse = $this->put("/chirps/{$chirp->id}", ['content' => 'Chirp modifié']);
        $reponse->assertStatus(403);

        /* Suppression d'un chirps de l'utilisateur1 par l'utilisateur2 */
        $result = $this->delete("/chirps/{$chirp->id}");
        $result->assertStatus(403);
        $this->assertDatabaseMissing('chirps', ['user_id' => $chirp->id,]);
    }
}
