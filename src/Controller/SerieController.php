<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    // SerieRepository en param pour récupérer la liste des séries en BDD
    public function list(SerieRepository $serieRepository): Response
    {
        // TODO récupérer la liste des séries en BDD
        // grâce aux méthodes générées automatiquement dans repository
        // $series = $serieRepository->findAll();

        // récupérer la liste des séries terminées triées par popularité
        // avec un tableau de clauses WHERE et ORDER BY
        // en limitant la liste de résultats à 10 à partir du 5ème résultat
        // $series = $serieRepository->findBy(['status' => 'ended'],
        // ['popularity' => 'DESC'], 10, 5);

        // récupération des 50 series les mieux notées
        $series = $serieRepository->findBy([], ['vote'=>'DESC'], 50);

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
        EntityManagerInterface $entityManager): Response
    {
        // créer une instance de serie
        $serie = new Serie();
        // appel aux setters de Serie.php pour créer une nouvelle série
        $serie
            ->setName('the Magician')
            ->setStatus('Ended')
            ->setBackdrop('backdrop.png')
            ->setDateCreated(new \DateTime())
            ->setGenres("Comedy")
            ->setFirstAirDate(new \DateTime("2022-02-02"))
            // date du jour moins 6 mois
            ->setLastAirDate(new \DateTime("- 6 month"))
            ->setPopularity(850.52)
            ->setPoster("poster.png")
            ->setTmdbId(123456)
            ->setVote(8.5);

        dump($serie);

        // utilisation de l'entityManager
        // persist() prépare la requête, flush() l'enregistre dans la BDD
        $entityManager->persist($serie);
        $entityManager->flush();

        dump($serie);

// OU
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

        $serieRepository->remove($serie, true);

        dump($serie);

        // TODO Créer un formulaire d'ajout de série
        return $this->render('serie/add.html.twig');
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
        if(!$serie){
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
