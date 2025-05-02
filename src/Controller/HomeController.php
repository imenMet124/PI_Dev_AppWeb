<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(FormationRepository $formationRepository): Response
    {
        // Get recent formations for the homepage
        $recentFormations = $formationRepository->findBy([], ['dateCreation' => 'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'recent_formations' => $recentFormations,
        ]);
    }
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [

        ]);
    }
    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig', [

        ]);
    }
}
