<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// ATTRIBUT de la class qui permet de mutualiser les infos
// = faire commencer toutes les routes par /series
// et les noms de routes par serie_
#[Route('/serie', name: 'serie_')]
class SerieController extends AbstractController
{
    // contrôleur pour afficher liste de séries
    #[Route('/list', name: 'list')]
    // SerieRepository en param pour récupérer la liste des séries en BDD
    public function list(SerieRepository $serieRepository): Response
    {
        // TODO récupérer la liste des séries en BDD
        // grâce aux méthodes générées automatiquement dans repository
         $series = $serieRepository->findAll();

        // récupérer la liste des séries terminées triées par popularité
        // avec un tableau de clauses WHERE et ORDER BY
        // en limitant la liste de résultats à 10 à partir du 5ᵉ résultat
        // $series = $serieRepository->findBy(['status' => 'ended'],
        // ['popularity' => 'DESC'], 10, 5);

        // récupération des 50 series les mieux notées
        // $series = $serieRepository->findBy([], ['vote'=>'DESC'], 50);

        // autre façon de faire une requête :
        // méthode magique qui crée dynamiquement une requête en fct des
        // attributs de l'instance de l'objet
        // $series = $serieRepository->findByStatus('ended');

        // appel à la requête DQL de la classe SerieRepository
        // $series = $serieRepository->findBestSeries();

        dump($series);

        // renvoyer la liste de toutes les series à la vue (= au fichier twig)
        return $this->render('serie/list.html.twig',['series' => $series]);
    }


    // contrôleur pour ajouter un film à la liste de séries
    #[Route('/add', name: 'add')]
    //injection de dépendance = autowiring pour créer une instance de serieRepository
        // qui renvoie un singleton
        // de même pour entityManager (n'est plus utilisé depuis Symfony
        // version 5.4)
    public function add(
        SerieRepository $serieRepository,
        Request $request
    ): Response
    {
        // créer une instance de serie
        $serie = new Serie();
        // instance de formulaire
        $serieForm = $this->createForm(SerieType::class, $serie);

        // SOUMISSION DU FORMULAIRE (grâce à handleRequest de HttpFoundation)
        // méthode qui extrait les éléments du formulaire de la requête
        $serieForm->handleRequest($request);

        if($serieForm->isSubmitted() && $serieForm->isValid()){
            // sauvegarde en BDD de la nouvelle série
            $serieRepository->save($serie, true);

            // message flash qui stocke un cookie pour le lire et le supprimer après
            // demander à twig de checker si messages flash (dans base.html.twig)
            $this->addFlash('success','Serie added !');

            // redirection vers page détail de la série après soumission du formulaire
            return $this->redirectToRoute('serie_show',['id'=>$serie->getId()]);
        }
        // renvoi du formulaire à la vue
        return $this->render('serie/add.html.twig', [
            'serieForm' => $serieForm->createView()
        ]);


//        // sauvegarder l'entité $serie créee ci-dessus
//        // en l'enregistrant dans la BDD
//        $serieRepository ->save($serie, true);
//
//        dump($serie);
//
//        // quand id détecté, objet updaté
//        $serie ->setName("The Last Of Us");
//        $serieRepository ->save($serie, true);
//
//        dump($serie);
//
//        $serieRepository->remove($serie, true);

    }

    // contrôleur pour voir le détail d'une série
    // passage d'un paramètre id dans l'URL pour choisir la série
    // ATTENTION AU CONFLIT DE ROUTES :
    // donc utiliser requirements qui attend un int (\d+ = au moins 1 chiffre)
    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    // PAram converter : Si Serie $id au lieu de int $id, Serie $id récupère
    // tout l'objet
    public function show(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);
        // si $serie est null, renvoyer 1 erreur 404
        if (!$serie) {
            throw $this->createNotFoundException('Oups, serie not found !');
        }
        dump($serie);
        // récupération des infos de la série
        return $this->render('serie/show.html.twig', [
            // 'serie' = nom de la variable dans twig dont la valeur est $serie
            'serie' => $serie
        ]);
    }




}
