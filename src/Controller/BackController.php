<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackController extends AbstractController{
    #[Route('/back', name: 'app_back')]
    public function index(): Response
    {
        $user = $this->getUser();

        // Check if the user has the "employee" role
        if ($user && in_array('ROLE_EMPLOYEE', $user->getRoles())) {
            // Render the error template
            return $this->render('error/no_permission.html.twig', [
                'message' => 'You don\'t have permission to access this page.',
            ]);
        }

        // Render the back-office page for authorized users
        return $this->render('back/index.html.twig');
    }
}
