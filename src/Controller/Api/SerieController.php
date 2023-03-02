<?php

namespace App\Controller\Api;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/serie', name: 'api_serie_')]
class SerieController extends AbstractController
{
    // return all series
    #[Route('', name: 'retrieve_all', methods: "GET")]
    public function retrieveAll(SerieRepository $serieRepository): Response
    {
        // récup du serieRepository en le passant en param de la fonction
        // puis récup de toutes les series grâce au findAll du Repository
        $series = $serieRepository->findAll();
        //dump($series) pour voir ce qu'on récupère
        // renvoie des données au format json en utilisant le groups
        return $this->json($series, 200, [], ['groups'=>'serie_api']);
    }

    // return this serie
    #[Route('/{id}', name: 'retrieve_one', methods: "GET")]
    public function retrieveOne(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);
        return $this->json($serie, 200, [], ['groups'=>'serie_api']);
    }

    // add a serie
    #[Route('', name: 'add', methods: "POST")]
    public function add(Request $request, SerializerInterface $serializer): Response
    {
        // récupérer le fichier json grâce à l'objet request passé en param
        $data = $request->getContent();
        // $serializer permet de transformer la donnée json en instance de serie
        // désérialiser le content récupéré et le transformer en objet 'Serie::class'
        $serie = $serializer->deserialize($data, Serie::class, 'json');
        // TODO mettre info récupérées en BDD avec le Repository->save

        return $this->json("OK");
    }

    // delete this serie
    #[Route('/{id}', name: 'remove', methods: "DELETE")]
    public function remove(int $id): Response
    {
        // TODO delete this serie
    }

    // modifier une série en update asynchrone
    // pour rajouter option like-dislike
    #[Route('/{id}', name: 'update', methods: "PUT")]
    public function update(int $id): Response
    {

    }
}
