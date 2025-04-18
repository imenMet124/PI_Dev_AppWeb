<?php

namespace App\Controller;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Enum\ApplicationStatus;



#[Route('/application')]
final class ApplicationController extends AbstractController
{
    #[Route(name: 'app_application_index', methods: ['GET'])]
    public function index(ApplicationRepository $applicationRepository): Response
    {
        return $this->render('application/index.html.twig', [
            'applications' => $applicationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_application_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($application);
            $entityManager->flush();

            return $this->redirectToRoute('app_application_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/new.html.twig', [
            'application' => $application,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_show', methods: ['GET'])]
    public function show(Application $application): Response
    {
        return $this->render('application/show.html.twig', [
            'application' => $application,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_application_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Application $application, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_application_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/edit.html.twig', [
            'application' => $application,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_delete', methods: ['POST'])]
    public function delete(Request $request, Application $application, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$application->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($application);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_application_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/postuler/{offerId}', name: 'app_application_postuler', methods: ['GET', 'POST'])]
public function postuler(
    int $offerId,
    Request $request,
    EntityManagerInterface $em,
    \App\Repository\JobOfferRepository $jobRepo
): Response {
    // 1. Récupérer l'offre
    $offer = $jobRepo->find($offerId);
    if (!$offer) {
        throw $this->createNotFoundException('Offre non trouvée');
    }

    // 2. Créer un nouveau candidat
    $candidat = new \App\Entity\Candidat();

    // ✅ 3. Créer le formulaire à partir de CandidatType
    $form = $this->createForm(\App\Form\CandidatType::class, $candidat);

    // 4. Gérer la requête
    $form->handleRequest($request);

    // 5. Si formulaire valide → traitement
    if ($form->isSubmitted() && $form->isValid()) {
        // traitement des fichiers + enregistrement
        // ...
    }

    // 6. Afficher le formulaire dans le template
    return $this->render('application/postuler.html.twig', [
        'form' => $form->createView(),
        'offer' => $offer,
    ]);
}

#[Route('/file/cv/{id}', name: 'app_application_cv', methods: ['GET'])]
public function viewCv(Application $application): Response
{
    $cvPath = $application->getCandidat()->getResumePath();

    if (!$cvPath || !file_exists($this->getParameter('kernel.project_dir') . '/public' . $cvPath)) {
        throw $this->createNotFoundException('CV introuvable');
    }

    return $this->file($this->getParameter('kernel.project_dir') . '/public' . $cvPath);
}
#[Route('/file/lettre/{id}', name: 'app_application_lettre', methods: ['GET'])]
public function viewLettre(Application $application): Response
{
    $lettrePath = $application->getCandidat()->getCoverLetterPath();

    if (!$lettrePath || !file_exists($this->getParameter('kernel.project_dir') . '/public' . $lettrePath)) {
        throw $this->createNotFoundException('Lettre de motivation introuvable');
    }

    return $this->file($this->getParameter('kernel.project_dir') . '/public' . $lettrePath);
}

}
