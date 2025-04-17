<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Entity\Affectation;
use App\Entity\User;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(TacheRepository $tacheRepository): Response
    {
        // Static check for admin/employee view
        $isAdmin = true; // Change this to false to test employee view

        if ($isAdmin) {
            return $this->render('task/admin/index.html.twig', [
                'tasks' => $tacheRepository->findAll(),
            ]);
        } else {
            return $this->redirectToRoute('app_employee_tasks');
        }
    }

    #[Route('/my-tasks', name: 'app_employee_tasks', methods: ['GET'])]
    public function employeeTasks(TacheRepository $tacheRepository): Response
    {
        // For now, using a static employee ID for testing
        // Later, this will come from the authenticated user
        return $this->render('task/employee/index.html.twig', [
            'tasks' => $tacheRepository->findTasksByEmployee(1), // Replace 1 with actual user ID when authentication is implemented
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache, ['is_employee' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle user assignments
            if ($form->has('users')) {
                $users = $form->get('users')->getData();
                foreach ($users as $user) {
                    $affectation = new Affectation();
                    $affectation->setTache($tache);
                    $affectation->setEmploye($user);
                    $entityManager->persist($affectation);
                }
            }

            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/admin/new.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    #[Route('/{id_tache}', name: 'app_task_show', methods: ['GET'])]
    public function show(Tache $tache): Response
    {
        // Static check for admin/employee view
        $isAdmin = true; // Change this to false to test employee view

        if ($isAdmin) {
            return $this->render('task/admin/show.html.twig', [
                'tache' => $tache,
            ]);
        } else {
            return $this->render('task/employee/show.html.twig', [
                'tache' => $tache,
            ]);
        }
    }

    #[Route('/{id_tache}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        // Static check for admin/employee view
        $isAdmin = true; // Change this to false to test employee view
        
        $form = $this->createForm(TacheType::class, $tache, ['is_employee' => !$isAdmin]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render($isAdmin ? 'task/admin/edit.html.twig' : 'task/employee/edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    #[Route('/{id_tache}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tache->getIdTache(), $request->request->get('_token'))) {
            $entityManager->remove($tache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }
} 