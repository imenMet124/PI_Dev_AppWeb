<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\JobOfferRepository;
use App\Entity\JobOffer;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Candidat;
use App\Form\CandidatType;

<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
        ]);
    }
   
#[Route('/offres', name: 'app_job_offer_public')]
public function publicOffers(JobOfferRepository $jobOfferRepository): Response
{
    $offers = $jobOfferRepository->findBy(['isActive' => true]);

    return $this->render('home/offers.html.twig', [
        'offers' => $offers,
    ]);
}
#[Route('/offres/{id}', name: 'app_job_offer_front_show')]
public function showOffer(JobOffer $offer): Response
{
    return $this->render('home/show_offers.html.twig', [
        'offer' => $offer,
    ]);
}
#[Route('/candidat/new/{offerId}', name: 'app_candidat_new')]
public function new(Request $request, int $offerId = null, EntityManagerInterface $em, JobOfferRepository $jobRepo): Response
{
    $candidat = new Candidat();

    if ($offerId) {
        $offer = $jobRepo->find($offerId);
        if ($offer) {
            $candidat->setJobOffer($offer); // si relation existante
        }
    }

    $form = $this->createForm(CandidatType::class, $candidat);
    // suite comme avant...
}



}
