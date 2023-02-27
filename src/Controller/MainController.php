<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    // Attribut symfony en php8 :
    // /home (nom du controller) pointe vers
    // url à partir de public, ici : http://localhost/bucket-list/public/home
    // et appelle la fonction main_home
    #[Route('/home', name: 'main_home')]
    public function home(): Response
    {
        //arrêt de l'exécution du programme et affichage du message
        // ex : die("Hello World !"); affiche hello world sur la page
        // http://localhost/series/public/home

        // passage de variables en paramètre
        $username = "<h1>Sandra</h1>";
        // variables en tableau associatif
        $serie = ['title' => 'Community', 'year' => 'Ouf', 'plateform' => 'NBC'];
        // ajout des paramètres dans un tableau associatif
        // la clé devient le nom de la variable côté twig
        return $this->render('main/home.html.twig', [
            "name" => $username,
            "serie" => $serie
        ]);
    }

    // Annotations : commentaires interprétés avant php8
    /**
     * @Route("/test", name="main_test")
     */
    public function test(): Response
    {
        return $this->render('main/test.html.twig');
    }
}
