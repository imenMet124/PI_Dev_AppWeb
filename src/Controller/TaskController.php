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
use App\Service\TodoistService;
use App\Enum\UserRole;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/task')]
class TaskController extends AbstractController
{
    private TodoistService $todoistService;

    public function __construct(TodoistService $todoistService)
    {
        $this->todoistService = $todoistService;
    }

    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(Request $request, TacheRepository $tacheRepository, PaginatorInterface $paginator): Response
    {
        $isAdmin = true; // Change this to false to test employee view

        $search = $request->query->get('search');
        $page = $request->query->getInt('page', 1);
        $queryBuilder = $tacheRepository->getSearchQueryBuilder($search);
        $pagination = $paginator->paginate($queryBuilder, $page, 8);

        if ($isAdmin) {
            return $this->render('task/admin/index.html.twig', [
                'tasks' => $pagination,
                'search' => $search,
            ]);
        } else {
            return $this->redirectToRoute('app_employee_tasks');
        }
    }

    #[Route('/my-tasks', name: 'app_employee_tasks', methods: ['GET'])]
    public function employeeTasks(Request $request, TacheRepository $tacheRepository, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to view your tasks.');
        }
        $search = $request->query->get('search');
        $page = $request->query->getInt('page', 1);
        $queryBuilder = $tacheRepository->getSearchQueryBuilder($search);
        $queryBuilder->leftJoin('t.affectations', 'a')
            ->leftJoin('a.employe', 'e')
            ->andWhere('e.id = :employeeId')
            ->setParameter('employeeId', $user->getId());
        $pagination = $paginator->paginate($queryBuilder, $page, 8);
        return $this->render('task/employee/index.html.twig', [
            'tasks' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Restrict task creation to specific roles
        if (!$user || !in_array($user->getRole(), [UserRole::RESPONSABLE_RH->value, UserRole::CHEF_PROJET->value])) {
            throw $this->createAccessDeniedException('Only HR Managers and Project Managers can create tasks.');
        }

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

                    // --- Sync with Todoist ---
                    $todoistToken = $user->getTodoistAccessToken();
                    if ($todoistToken) {
                        $this->todoistService->createTask(
                            $todoistToken,
                            $tache->getTitreTache(),
                            $tache->getDeadline() ? $tache->getDeadline()->format('Y-m-d') : null
                        );
                    }
                    // --- End sync ---
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
            // First, remove all related affectations
            foreach ($tache->getAffectations() as $affectation) {
                $entityManager->remove($affectation);
            }
            
            // Then remove the task
            $entityManager->remove($tache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/task/{id_tache}/update-progress', name: 'app_task_update_progress', methods: ['POST'])]
    public function updateProgress(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $newProgression = $request->request->get('progression');

        if ($newProgression !== null && is_numeric($newProgression)) {
            $newProgression = max(0, min(100, (int) $newProgression)); // Ensure progression is between 0 and 100

            // Only allow progression to increase
            if ($newProgression > $tache->getProgression()) {
                $tache->setProgression($newProgression);

                // Update task status if it was "Not Started"
                if ($tache->getStatutTache() === 'Not Started') {
                    $tache->setStatutTache('In Progress');
                }

                $entityManager->flush();

                $this->addFlash('success', 'Task progress updated successfully.');
            } else {
                $this->addFlash('error', 'Progression can only increase.');
            }
        } else {
            $this->addFlash('error', 'Invalid progress value.');
        }

        return $this->redirectToRoute('app_employee_tasks');
    }

    #[Route('/todoist/oauth', name: 'task_todoist_oauth')]
    public function oauth(): Response
    {
        $oauthUrl = $this->todoistService->getOAuthUrl();
        return $this->redirect($oauthUrl);
    }

    #[Route('/todoist/callback', name: 'task_todoist_callback')]
    public function callback(Request $request): Response
    {
        $authorizationCode = $request->query->get('code');

        if (!$authorizationCode) {
            $this->addFlash('error', 'Authorization code not provided.');
            return $this->redirectToRoute('app_task_index');
        }

        $accessToken = $this->todoistService->exchangeAuthCodeForToken($authorizationCode);

        if (!$accessToken) {
            $this->addFlash('error', 'Failed to retrieve access token.');
            return $this->redirectToRoute('app_task_index');
        }

        // Store the access token in the session or database
        $this->get('session')->set('todoist_access_token', $accessToken);

        $this->addFlash('success', 'Successfully connected to Todoist.');
        return $this->redirectToRoute('task_todoist_tasks');
    }

    #[Route('/todoist/tasks', name: 'task_todoist_tasks')]
    public function tasks(): Response
    {
        $accessToken = $this->get('session')->get('todoist_access_token');

        if (!$accessToken) {
            $this->addFlash('error', 'You need to connect to Todoist first.');
            return $this->redirectToRoute('task_todoist_oauth');
        }

        $tasks = $this->todoistService->getTasks($accessToken);

        return $this->render('task/todoist_tasks.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/todoist/task/create', name: 'task_todoist_task_create', methods: ['POST'])]
    public function createTask(Request $request): Response
    {
        $accessToken = $this->get('session')->get('todoist_access_token');

        if (!$accessToken) {
            $this->addFlash('error', 'You need to connect to Todoist first.');
            return $this->redirectToRoute('task_todoist_oauth');
        }

        $content = $request->request->get('content');
        $dueDate = $request->request->get('due_date');

        if (!$content) {
            $this->addFlash('error', 'Task content is required.');
            return $this->redirectToRoute('task_todoist_tasks');
        }

        $success = $this->todoistService->createTask($accessToken, $content, $dueDate);

        if ($success) {
            $this->addFlash('success', 'Task created successfully in Todoist.');
        } else {
            $this->addFlash('error', 'Failed to create task in Todoist.');
        }

        return $this->redirectToRoute('task_todoist_tasks');
    }
}