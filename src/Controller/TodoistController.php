<?php

namespace App\Controller;

use App\Service\TodoistService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoistController extends AbstractController
{
    private TodoistService $todoistService;

    public function __construct(TodoistService $todoistService)
    {
        $this->todoistService = $todoistService;
    }

    #[Route('/todoist/oauth', name: 'todoist_oauth')]
    public function oauth(): Response
    {
        $oauthUrl = $this->todoistService->getOAuthUrl();
        return $this->redirect($oauthUrl);
    }

    #[Route('/todoist/callback', name: 'todoist_callback')]
    public function callback(Request $request, EntityManagerInterface $entityManager): Response
    {
        $authorizationCode = $request->query->get('code');

        if (!$authorizationCode) {
            $this->addFlash('error', 'Authorization code not provided.');
            return $this->redirectToRoute('home');
        }

        $accessToken = $this->todoistService->exchangeAuthCodeForToken($authorizationCode);

        if (!$accessToken) {
            $this->addFlash('error', 'Failed to retrieve access token.');
            return $this->redirectToRoute('home');
        }

        // Store the access token in the user entity
        $user = $this->getUser();
        if ($user) {
            $user->setTodoistAccessToken($accessToken);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Successfully connected to Todoist.');
        return $this->redirectToRoute('todoist_tasks');
    }

    #[Route('/todoist/tasks', name: 'todoist_tasks')]
    public function tasks(): Response
    {
        $user = $this->getUser();
        $accessToken = $user ? $user->getTodoistAccessToken() : null;

        if (!$accessToken) {
            $this->addFlash('error', 'You need to connect to Todoist first.');
            return $this->redirectToRoute('todoist_oauth');
        }

        $tasks = $this->todoistService->getTasks($accessToken);

        return $this->render('todoist/tasks.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/todoist/task/create', name: 'todoist_task_create', methods: ['POST'])]
    public function createTask(Request $request): Response
    {
        $user = $this->getUser();
        $accessToken = $user ? $user->getTodoistAccessToken() : null;

        if (!$accessToken) {
            $this->addFlash('error', 'You need to connect to Todoist first.');
            return $this->redirectToRoute('todoist_oauth');
        }

        $content = $request->request->get('content');
        $dueDate = $request->request->get('due_date');

        if (!$content) {
            $this->addFlash('error', 'Task content is required.');
            return $this->redirectToRoute('todoist_tasks');
        }

        $success = $this->todoistService->createTask($accessToken, $content, $dueDate);

        if ($success) {
            $this->addFlash('success', 'Task created successfully.');
        } else {
            $this->addFlash('error', 'Failed to create task.');
        }

        return $this->redirectToRoute('todoist_tasks');
    }
}