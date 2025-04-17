<?php

namespace App\Controller;

use App\Entity\Affectation;
use App\Form\AffectationType;
use App\Repository\AffectationRepository;
use App\Repository\UserRepository;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/assignment')]
class AssignmentController extends AbstractController
{
    #[Route('/', name: 'app_assignment_index', methods: ['GET'])]
    public function index(AffectationRepository $affectationRepository): Response
    {
        return $this->render('assignment/index.html.twig', [
            'affectations' => $affectationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_assignment_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        TacheRepository $tacheRepository
    ): Response {
        $affectation = new Affectation();
        $form = $this->createForm(AffectationType::class, $affectation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($affectation);
            $entityManager->flush();

            return $this->redirectToRoute('app_assignment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('assignment/new.html.twig', [
            'affectation' => $affectation,
            'form' => $form,
            'users' => $userRepository->findAll(),
            'tasks' => $tacheRepository->findAll(),
        ]);
    }

    #[Route('/{id_affectation}', name: 'app_assignment_show', methods: ['GET'])]
    public function show(Affectation $affectation): Response
    {
        return $this->render('assignment/show.html.twig', [
            'affectation' => $affectation,
        ]);
    }

    #[Route('/{id_affectation}/edit', name: 'app_assignment_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Affectation $affectation, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        TacheRepository $tacheRepository
    ): Response {
        $form = $this->createForm(AffectationType::class, $affectation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_assignment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('assignment/edit.html.twig', [
            'affectation' => $affectation,
            'form' => $form,
            'users' => $userRepository->findAll(),
            'tasks' => $tacheRepository->findAll(),
        ]);
    }

    #[Route('/{id_affectation}', name: 'app_assignment_delete', methods: ['POST'])]
    public function delete(Request $request, Affectation $affectation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$affectation->getIdAffectation(), $request->request->get('_token'))) {
            $entityManager->remove($affectation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_assignment_index', [], Response::HTTP_SEE_OTHER);
    }
} 