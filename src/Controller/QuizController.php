<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\DirectQuizGeneratorType;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use App\Service\QuizGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(['/back/quiz', '/back/quiz/'])]
class QuizController extends AbstractController
{
    #[Route('/', name: 'app_quiz_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('back/quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quiz);
            $entityManager->flush();

            $this->addFlash('success', 'Le quiz a été créé avec succès');
            return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }
    
    #[Route('/generate', name: 'app_quiz_generate', methods: ['GET', 'POST'])]
    public function generateQuiz(
        Request $request,
        QuizGeneratorService $quizGeneratorService
    ): Response {
        // Create the form for direct quiz generation
        $form = $this->createForm(DirectQuizGeneratorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            // Extract form data
            $title = $formData['title'];
            $description = $formData['description'] ?? null;

            // Extract options
            $options = [
                'numQuestions' => $formData['numQuestions'],
                'numOptions' => $formData['numOptions'],
                'difficulty' => $formData['difficulty'],
                'language' => $formData['language'],
            ];

            try {
                // Generate the quiz
                $result = $quizGeneratorService->generateQuizFromTitle($title, $description, $options);

                if (!$result['success']) {
                    $this->addFlash('danger', 'Erreur lors de la génération du quiz: ' . $result['error']);
                    return $this->render('back/quiz/generate.html.twig', [
                        'form' => $form,
                    ]);
                }

                // Create the quiz entity
                $quiz = $quizGeneratorService->createQuizEntityFromTitle($title, $result['data']);

                $this->addFlash('success', 'Le quiz a été généré avec succès');
                return $this->redirectToRoute('app_quiz_show', ['id' => $quiz->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la génération du quiz: ' . $e->getMessage());
            }
        }

        return $this->render('back/quiz/generate.html.twig', [
            'form' => $form,
        ]);
    }
    
    #[Route('/generate-ajax', name: 'app_quiz_generate_ajax', methods: ['POST'])]
    public function generateQuizAjax(
        Request $request,
        QuizGeneratorService $quizGeneratorService
    ): JsonResponse {
        // Get the data from the request
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title']) || empty($data['title'])) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Le titre est obligatoire',
            ], Response::HTTP_BAD_REQUEST);
        }

        $title = $data['title'];
        $description = $data['description'] ?? null;
        $options = $data['options'] ?? [];

        try {
            // Generate the quiz
            $result = $quizGeneratorService->generateQuizFromTitle($title, $description, $options);

            if (!$result['success']) {
                return new JsonResponse([
                    'success' => false,
                    'error' => $result['error'],
                ], Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $result['data'],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    #[Route('/save-generated-quiz', name: 'app_quiz_save_generated_quiz', methods: ['POST'])]
    public function saveGeneratedQuiz(
        Request $request,
        QuizGeneratorService $quizGeneratorService
    ): JsonResponse {
        // Get the data from the request
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title']) || empty($data['title'])) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Le titre est obligatoire',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['quiz']) || empty($data['quiz'])) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Données de quiz manquantes',
            ], Response::HTTP_BAD_REQUEST);
        }

        $title = $data['title'];
        $quizData = $data['quiz'];

        try {
            // Create the quiz entity
            $quiz = $quizGeneratorService->createQuizEntityFromTitle($title, $quizData);

            return new JsonResponse([
                'success' => true,
                'quizId' => $quiz->getId(),
                'redirectUrl' => $this->generateUrl('app_quiz_show', ['id' => $quiz->getId()]),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/deleted', name: 'app_quiz_deleted', methods: ['GET'])]
    public function showDeleted(QuizRepository $quizRepository): Response
    {
        return $this->render('back/quiz/deleted.html.twig', [
            'quizzes' => $quizRepository->findAllDeleted(),
        ]);
    }
    
    #[Route('/{id<\d+>}', name: 'app_quiz_show', methods: ['GET'])]
    public function show(Quiz $quiz, QuizRepository $quizRepository): Response
    {
        // Use the repository to get the quiz with its questions and options
        $quiz = $quizRepository->findWithQuestionsAndOptions($quiz->getId());

        return $this->render('back/quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le quiz a été modifié avec succès');
            return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}/delete', name: 'app_quiz_delete', methods: ['POST'])]
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        // Debug log
        error_log('Delete method called for Quiz ID: ' . $quiz->getId());
        error_log('CSRF Token received: ' . ($request->getPayload()->has('_token') ? $request->getPayload()->getString('_token') : 'No token found'));
        error_log('Request method: ' . $request->getMethod());
        error_log('Request content: ' . $request->getContent());
        error_log('Request parameters: ' . json_encode($request->request->all()));

        try {
            // Check if _token is in the request
            if (!$request->getPayload()->has('_token')) {
                $this->addFlash('danger', 'Erreur: Token CSRF manquant');
                error_log('CSRF token missing');
                return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
            }

            $token = $request->getPayload()->getString('_token');
            $expectedToken = 'delete' . $quiz->getId();

            error_log('Expected token key: ' . $expectedToken);
            error_log('Validating token: ' . $token);

            if ($this->isCsrfTokenValid($expectedToken, $token)) {
                // Soft delete - set the deletedAt timestamp instead of removing
                $quiz->delete();
                $entityManager->flush();

                // Verify the entity was actually deleted
                $entityManager->refresh($quiz);
                error_log('Quiz deletedAt after flush: ' . ($quiz->getDeletedAt() ? $quiz->getDeletedAt()->format('Y-m-d H:i:s') : 'NULL'));

                $this->addFlash('success', 'Le quiz a été supprimé avec succès');
                error_log('Quiz soft deleted successfully');
            } else {
                $this->addFlash('danger', 'Erreur de sécurité lors de la suppression du quiz');
                error_log('CSRF token validation failed');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la suppression du quiz: ' . $e->getMessage());
            error_log('Exception during quiz deletion: ' . $e->getMessage());
            error_log('Exception trace: ' . $e->getTraceAsString());
        }

        return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id<\d+>}/restore', name: 'app_quiz_restore', methods: ['POST'])]
    public function restore(Request $request, Quiz $quiz, EntityManagerInterface $entityManager, QuizRepository $quizRepository): Response
    {
        // We need to fetch the quiz including deleted ones
        $quiz = $quizRepository->findWithQuestionsAndOptions($quiz->getId(), true);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        if ($this->isCsrfTokenValid('restore' . $quiz->getId(), $request->getPayload()->getString('_token'))) {
            $quiz->restore();
            $entityManager->flush();
            $this->addFlash('success', 'Le quiz a été restauré avec succès');
        }

        return $this->redirectToRoute('app_quiz_deleted', [], Response::HTTP_SEE_OTHER);
    }
}
