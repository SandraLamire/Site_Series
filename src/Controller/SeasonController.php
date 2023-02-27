<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/season', name: 'season_')]
class SeasonController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(Request $request, SeasonRepository $seasonRepository): Response
    {
        $season = new Season();
        $seasonForm = $this->createForm(SeasonType::class, $season);
        // extrait les infos du formulaire
        $seasonForm->handleRequest($request);
        if ($seasonForm->isSubmitted() && $seasonForm->isValid()) {
            // sauvegarde
            $seasonRepository->save($season, true);
            // ajout flash
            $this->addFlash('success', 'Season added !');
            // rediriger vers page
            return $this->redirectToRoute('serie_show', ['id' => $season->getSerie()->getId()]);
        }
        return $this->render('season/add.html.twig', [
            // renvoi le formulaire à la vue
            'seasonForm' => $seasonForm -> createView()
        ]);
    }
}
