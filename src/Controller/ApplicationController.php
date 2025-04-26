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
use Knp\Component\Pager\PaginatorInterface;



#[Route('/application')]
final class ApplicationController extends AbstractController
{
    #[Route('/application', name: 'app_application_index', methods: ['GET'])]
    public function index(ApplicationRepository $applicationRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search');
        $status = $request->query->get('status');
        $sort = $request->query->get('sort', 'submittedAt');
        $direction = $request->query->get('direction', 'desc');
    
        $queryBuilder = $applicationRepository->createQueryBuilder('a')
            ->join('a.candidat', 'c');
    
        if ($search) {
            $queryBuilder
                ->andWhere('c.firstName LIKE :search OR c.lastName LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
    
        if ($status !== null && $status !== '') {
            $queryBuilder
                ->andWhere('a.status = :status')
                ->setParameter('status', $status);
        }
    
        // Tri manuel
        if (in_array($sort, ['submittedAt', 'status'])) {
            $queryBuilder->orderBy('a.' . $sort, $direction);
        } elseif ($sort === 'candidat') {
            $queryBuilder->orderBy('c.lastName', $direction);
        } else {
            $queryBuilder->orderBy('a.submittedAt', 'DESC');
        }
    
        $query = $queryBuilder->getQuery();
    
        $applications = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5,
            [
                'defaultSortFieldName' => null,
                'defaultSortDirection' => null,
                'sortFieldParameterName' => null,
                'sortDirectionParameterName' => null,
            ]
        );
    
        return $this->render('application/index.html.twig', [
            'applications' => $applications,
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
        // Récupérer l'offre
        $offer = $jobRepo->find($offerId);
        if (!$offer) {
            throw $this->createNotFoundException('Offre non trouvée');
        }
    
        // Créer un nouveau candidat
        $candidat = new \App\Entity\Candidat();
    
        // Créer le formulaire
        $form = $this->createForm(\App\Form\CandidatType::class, $candidat);
    
        // Gérer la requête
        $form->handleRequest($request);
    
        // 🔥 Ici tu remplaces ton IF par ton vrai traitement :
        if ($form->isSubmitted() && $form->isValid()) {
            $resumeFile = $form->get('resumeFile')->getData();
            $coverLetterFile = $form->get('coverLetterFile')->getData();
    
            if ($resumeFile) {
                $newFilename = uniqid().'.'.$resumeFile->guessExtension();
                $resumeFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/cv/',
                    $newFilename
                );
                $candidat->setResumePath('/uploads/cv/' . $newFilename);
            }
    
            if ($coverLetterFile) {
                $newFilename2 = uniqid().'.'.$coverLetterFile->guessExtension();
                $coverLetterFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/lettres/',
                    $newFilename2
                );
                $candidat->setCoverLetterPath('/uploads/lettres/' . $newFilename2);
            }
    
            // Enregistrer le candidat
            $em->persist($candidat);
    
            // Créer et enregistrer l'application liée
            $application = new Application();
            $application->setCandidat($candidat);
            $application->setJobOffer($offer);
            $application->setStatus(\App\Enum\ApplicationStatus::EN_ATTENTE);
            $application->setSubmittedAt(new \DateTimeImmutable());
    
            $em->persist($application);
    
            // Sauvegarde en base
            $em->flush();
            $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');

            // Redirection vers accueil ou confirmation
            return $this->redirectToRoute('app_home');
        }
    
        // Sinon afficher le formulaire
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
