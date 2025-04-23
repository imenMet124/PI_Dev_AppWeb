<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/back')]
#[IsGranted('ROLE_USER')]
final class BackController extends AbstractController
{
    #[Route('', name: 'app_back')]
    public function index(): Response
    {
        return $this->render('back/index.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
    #[Route('/admin/candidatures', name: 'admin_applications')]
public function adminIndex(ApplicationRepository $repo): Response
{
    $applications = $repo->findAll(); // ou avec pagination si besoin
    return $this->render('/application/index.html.twig', [
        'applications' => $applications,
    ]);
}

}
