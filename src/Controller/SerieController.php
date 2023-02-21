<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// ATTRIBUT de la class qui permet de mutualiser les infos
// = faire commencer toutes les routes par /series
// et les noms de routes par serie_
#[Route('/serie', name: 'serie_')]
class SerieController extends AbstractController
{
    // contrôleur pour afficher liste de séries
    #[Route('/list', name: 'list')]
    public function list(): Response
    {
        // TODO Récupérer la liste des séries en BDD
        return $this->render('serie/list.html.twig');
    }

    // contrôleur pour ajouter un film à la liste de séries
    #[Route('/add', name: 'add')]
    public function add(): Response
    {
        // TODO Créer un formulaire d'ajout de série
        return $this->render('serie/add.html.twig');
    }

    // contrôleur pour voir le détail d'une série
    // passage d'un paramètre id dans l'URL pour choisir la série
    // ATTENTION AU CONFLIT DE ROUTES :
    // donc utiliser requirements qui attend un int (\d+ = au moins 1 chiffre)
    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        dump($id);
        // TODO Récupération des infos de la série
        return $this->render('serie/show.html.twig');
    }


}
