<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_formation')]
    public function index(FormationRepository $repository): Response
    {

        $formations = $repository->findAll();

        dump($formations);

        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
            'formations' => $formations
        ]);
    }

    #[Route('/formations/{id}', name: 'app_formation_show', requirements: ['id' => '\d+'])]
    public function detail(FormationRepository $repository, int $id): Response
    {
        $formation = $repository->find($id);

        if (!$formation) {
            throw $this->createNotFoundException(
                'No formation found for id '.$id
            );
        }

        return $this->render('formation/show.html.twig', [
            'controller_name' => 'FormationController',
            'formation' => $formation
        ]);
    }
}
