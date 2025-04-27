<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ApplicationRepository;
use App\Repository\JobOfferRepository;
use App\Repository\CandidatRepository;
use App\Enum\ApplicationStatus;
use Symfony\Component\HttpFoundation\JsonResponse;



final class BackController extends AbstractController{
    #[Route('/back', name: 'app_back')]
    public function index(
        ApplicationRepository $applicationRepository,
        JobOfferRepository $jobOfferRepository,
        CandidatRepository $candidatRepository
    ): Response {
        return $this->render('back/index.html.twig', [
            'controller_name' => 'BackController',
            'totalCandidates' => $candidatRepository->count([]),
            'totalApplications' => $applicationRepository->count([]),
            'acceptedApplications' => $applicationRepository->count(['status' => ApplicationStatus::ACCEPTEE]),
            'refusedApplications' => $applicationRepository->count(['status' => ApplicationStatus::REFUSEE]),
            'totalOffers' => $jobOfferRepository->count([]),
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
#[Route('/api/dashboard-data', name: 'dashboard_data')]
public function dashboardData(ApplicationRepository $applicationRepository): JsonResponse
{
    return $this->json([
        'accepted' => $applicationRepository->count(['status' => ApplicationStatus::ACCEPTEE->value]),
        'refused' => $applicationRepository->count(['status' => ApplicationStatus::REFUSEE->value]),
        'pending' => $applicationRepository->count(['status' => ApplicationStatus::EN_ATTENTE->value]),
    ]);
}

#[Route('/back', name: 'app_back')]
public function dashboard(ApplicationRepository $applicationRepository, JobOfferRepository $jobOfferRepository, CandidatRepository $candidatRepository): Response
{
    return $this->render('back/index.html.twig', [
        'controller_name' => 'BackController',
        'totalCandidates' => $candidatRepository->count([]),
        'totalApplications' => $applicationRepository->count([]),
        'acceptedApplications' => $applicationRepository->count(['status' => ApplicationStatus::ACCEPTEE->value]),
        'refusedApplications' => $applicationRepository->count(['status' => ApplicationStatus::REFUSEE->value]),
        'pendingApplications' => $applicationRepository->count(['status' => ApplicationStatus::EN_ATTENTE->value]),
        'applicationsPerMonth' => $applicationRepository->countApplicationsPerMonth(), // pour graphe évolution 📈
        'totalOffers' => $jobOfferRepository->count([]),
    ]);
    
}


}
