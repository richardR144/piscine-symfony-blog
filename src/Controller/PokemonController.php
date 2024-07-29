<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PokemonController extends AbstractController
{


    #[Route('/pokemons/delete/{id}', name: 'delete_pokemon')]
    public function deletePokemon(int $id, EntityManagerInterface $entityManager, PokemonRepository $pokemonRepository): Response
    {
        // j'utilise la méthode find pour trouver l'id du pokemom
        $pokemon = $pokemonRepository->find($id);
        $entityManager->remove($pokemon);
        $entityManager->flush();

        return $this->redirectToRoute('pokemon_list_db');
    }

    #[Route('/pokemon-list-db', name: 'pokemon_list_db')]
    public function listPokemonFromDb(PokemonRepository $pokemonRepository)
    {
        // récupèrer tous les pokemons en BDD

        $pokemons = $pokemonRepository->findAll();


        return $this->render('page/pokemon_list_db.html.twig', [
            'pokemons' => $pokemons
        ]);
    }

    #[Route('/pokemon-db/{id}', name: 'pokemon_by_id_db')]
    public function showPokemonById(int $id, PokemonRepository $pokemonRepository): Response
    {
        $pokemon = $pokemonRepository->find($id);

        return $this->render('page/pokemon_show_db.html.twig', [
            'pokemon' => $pokemon
        ]);
    }

    #[Route('/pokemons/delete/{id}', name: 'delete_pokemon')]
    public function deletePokemons(int $id, PokemonRepository $pokemonRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $pokemonRepository->find($id);
if (!$pokemon)
    $html = $this->renderView(view:'page/404.html.twig');
return new Response($html, status:404);
        // j'utilise la classe entity manager
        // pour préparer la requête SQL de suppression
        // cette requête n'est pas executée tout de suite
        $entityManager->remove($pokemon);
        // j'execute la / les requête SQL préparée
        $entityManager->flush();

        return $this->redirectToRoute('pokemon_list_db');
    }

    #[Route('/pokemons/insert/without-form', name: 'insert_pokemon')]
    public function insertPokemon(EntityManagerInterface $entityManager, Request $request)
    {

        // j'initialise la variable
        // $pokemon à null
        // car on va l'envoyer à twig (et on fera une vérif dans twig)
        $pokemon = null;


        // je vérifie si la requête est du POST
        // donc si le form a été envoyé
        if ($request->getMethod() === 'POST') {


            // je récupère les données envoyées par l'utilisateur
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $image = $request->request->get('image');
            $type = $request->request->get('type');

            // j'instancie la classe pokemon
            $pokemon = new Pokemon();

            // je passe en valeur des propriétés de la classe
            // pokemon les données envoyées par l'utilisateur
            // grâce aux fonctions setters
            $pokemon->setTitle($title);
            $pokemon->setDescription( $description);
            $pokemon->setImage($image);
            $pokemon->setType($type);


            // j'enregistre l'instance de la classe
            // pokemon dans la table pokemon
            // grâce à la classe EntityManager
            $entityManager->persist($pokemon);
            $entityManager->flush();
        }


        // je retourne une réponse HTTP
        // avec le html du formulaire
        return $this->render('page/pokemon_insert_without_form.html.twig', [
            'pokemon' => $pokemon,
        ]);

    }

}








