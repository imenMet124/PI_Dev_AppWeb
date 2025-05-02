<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/formations')]
class FrontFormationController extends AbstractController
{
    #[Route('/', name: 'app_front_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        // Get all active formations
        $formations = $formationRepository->findAllOrderedByDate();

        return $this->render('front/formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/{id}', name: 'app_front_formation_show', methods: ['GET'])]
    public function show(Formation $formation, FormationRepository $formationRepository): Response
    {
        // Get the formation with its quiz
        $formation = $formationRepository->findWithQuiz($formation->getId());

        return $this->render('front/formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }
}
