<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use App\Utils\Uploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    #[Route('/list/{page}', name: 'list', requirements:['page'=>'\d+'], methods: 'GET')]
    // SerieRepository en param pour récupérer la liste des séries en BDD
    // $page en paramètre avec valeur par défaut : page 1
    public function list(SerieRepository $serieRepository, int $page = 1): Response
    {
        // Récupérer la liste des séries en BDD
        // grâce aux méthodes générées automatiquement dans repository
        // $series = $serieRepository->findAll();

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

        // nombre de séries dans la table
        $nbSerieMax = $serieRepository->count([]);
        $maxPage = ceil($nbSerieMax / SerieRepository::SERIE_LIMIT);

        if ($page >=1 && $page <= $maxPage) {
            // appel à la requête DQL de la classe SerieRepository
            $series = $serieRepository->findBestSeries($page);
        } else {
            throw $this->createNotFoundException('Oups ! Page not found !');
        }

        // dump($series);

        // renvoyer la liste de toutes les series à la vue (= au fichier twig)
        return $this->render('serie/list.html.twig', [
            'series' => $series,
            // garder en mémoire numéro page courante
            'currentPage' => $page,
            'maxPage' => $maxPage
        ]);
    }


    // contrôleur pour ajouter un film à la liste de séries
    #[Route('/add', name: 'add')]
    // Mettre IsGranted que si pas sécuriser globalement dans security.yaml
    // #[IsGranted("ROLE_USER")]

    //injection de dépendance = autowiring pour créer une instance de serieRepository
        // qui renvoie un singleton
        // de même pour entityManager (n'est plus utilisé depuis Symfony
        // version 5.4)
    public function add(
        SerieRepository $serieRepository,
        Request $request,
        Uploader $uploader
    ): Response
    {
        //renvoie une 403
        //$this->>createAccessDeniedException();

        // créer une instance de serie
        $serie = new Serie();
        // instance de formulaire liée à une instance de serie
        $serieForm = $this->createForm(SerieType::class, $serie);

        // SOUMISSION DU FORMULAIRE (grâce à handleRequest de HttpFoundation)
        // méthode qui extrait les éléments du formulaire de la requête
        $serieForm->handleRequest($request);

        if ($serieForm->isSubmitted() && $serieForm->isValid()) {

            // upload photo
            // récup des infos non mappées dans le champ poster
            // @var type le $file pour permettre l'autocomplétion
            /**
            * @var UploadedFile $file
             */

//            $file = $serieForm->get('poster')->getData();
//            // renommer l'objet récupéré, uniqid permet de générer un nombre aléatoire
//            $newFileName = $serie->getName() . "-" . uniqid() . "-" . $file->guessExtension();
//            // copie du fichier dans répertoire de sauvegarde + nouveau nom
//            $file->move('img/posters/series', $newFileName);
//
            // upload crée en service
            $file = $serieForm->get('poster')->getData();
            // appel de l'uploader
            $newFileName = $uploader->upload(
                $file,
                // récupérer le paramètre de services.yaml
                $this->getParameter('upload_serie_poster'),
                $serie->getName()
            );

            // setter le nouveau nom du fichier
            $serie->setPoster($newFileName);

            // sauvegarde en BDD de la nouvelle série
            $serieRepository->save($serie, true);

            // message flash qui stocke un cookie pour le lire et le supprimer après
            // demander à twig de checker si messages flash (dans base.html.twig)

            $this->addFlash('success', 'Serie added !');
            // redirection vers page détail de la série après soumission du formulaire
            return $this->redirectToRoute('serie_show', ['id'=>$serie->getId()]);
        }
        // renvoi du formulaire à la vue
        return $this->render('serie/add.html.twig', [
            'serieForm' => $serieForm->createView()
        ]);


//        // sauvegarder l'entité $serie créée ci-dessus
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
            // lancer une erreur 404 si la série n'existe pas
            throw $this->createNotFoundException('Oops, serie not found !');
        }
        // dump($serie);
        // récupération des infos de la série
        return $this->render('serie/show.html.twig', [
            // 'serie' = nom de la variable dans twig dont la valeur est $serie
            'serie' => $serie
        ]);
    }

    #[Route('/remove/{id}',name: 'remove')]
    // injection de $serieRepository pour avoir accès à sa méthode remove
    public function remove(int $id, SerieRepository $serieRepository)
    {
        // récupérer la série grâce à son id
        $serie = $serieRepository->find($id);

        // la supprimer si elle existe
        if ($serie) {
            // true pour flusher
            $serieRepository->remove($serie, true);
            $this->addFlash('warning', 'Serie deleted !');
        } else {
            // sinon exception
            throw $this->createNotFoundException("This serie can't be deleted !");
        }
        // rediriger vers page d'accueil
        return $this->redirectToRoute('serie_list');
    }
}
