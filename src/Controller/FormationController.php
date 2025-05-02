<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Quiz;
use App\Form\FormationType;
use App\Form\QuizGeneratorType;
use App\Repository\FormationRepository;
use App\Service\QuizGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/back/formation')]
class FormationController extends AbstractController
{
    #[Route('/', name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        return $this->render('back/formation/index.html.twig', [
            'formations' => $formationRepository->findAllOrderedByDate(),
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle PDF file upload
            $pdfFile = $form->get('pdfFile')->getData();
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    // Move the file to the directory where PDFs are stored
                    $pdfFile->move(
                        $this->getParameter('formations_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du fichier PDF');
                }

                // Update the 'file_path' property to store the PDF file name
                $formation->setFilePath('uploads/formations/' . $newFilename);
            }

            $entityManager->persist($formation);
            $entityManager->flush();

            $this->addFlash('success', 'La formation a été créée avec succès');
            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation, FormationRepository $formationRepository): Response
    {
        // Use the repository to get the formation with its quiz
        $formation = $formationRepository->findWithQuiz($formation->getId());

        return $this->render('back/formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle PDF file upload
            $pdfFile = $form->get('pdfFile')->getData();
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    // Move the file to the directory where PDFs are stored
                    $pdfFile->move(
                        $this->getParameter('formations_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du fichier PDF');
                }

                // Update the 'file_path' property to store the PDF file name
                $formation->setFilePath('uploads/formations/' . $newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'La formation a été modifiée avec succès');
            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $formation->getId(), $request->getPayload()->getString('_token'))) {
                // Soft delete - set the deletedAt timestamp instead of removing
                $formation->delete();
                $entityManager->flush();
                $this->addFlash('success', 'La formation a été supprimée avec succès');
            } else {
                $this->addFlash('danger', 'Erreur de sécurité lors de la suppression de la formation');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la suppression de la formation: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/deleted', name: 'app_formation_deleted', methods: ['GET'])]
    public function showDeleted(FormationRepository $formationRepository): Response
    {
        return $this->render('back/formation/deleted.html.twig', [
            'formations' => $formationRepository->findAllDeleted(),
        ]);
    }

    #[Route('/{id}/restore', name: 'app_formation_restore', methods: ['POST'])]
    public function restore(Request $request, Formation $formation, EntityManagerInterface $entityManager, FormationRepository $formationRepository): Response
    {
        // We need to fetch the formation including deleted ones
        $formation = $formationRepository->findWithQuiz($formation->getId(), true);

        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée');
        }

        if ($this->isCsrfTokenValid('restore' . $formation->getId(), $request->getPayload()->getString('_token'))) {
            $formation->restore();
            $entityManager->flush();
            $this->addFlash('success', 'La formation a été restaurée avec succès');
        }

        return $this->redirectToRoute('app_formation_deleted', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/generate-quiz', name: 'app_formation_generate_quiz', methods: ['GET', 'POST'])]
    public function generateQuiz(
        Request $request,
        Formation $formation,
        QuizGeneratorService $quizGeneratorService,
        EntityManagerInterface $entityManager,
        FormationRepository $formationRepository
    ): Response {
        // Use the repository to get the formation with its quiz
        $formation = $formationRepository->findWithQuiz($formation->getId());

        // Create the form for quiz generation options
        $form = $this->createForm(QuizGeneratorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $options = $form->getData();

            try {
                // Generate the quiz
                $result = $quizGeneratorService->generateQuiz($formation, $options);

                if (!$result['success']) {
                    $this->addFlash('danger', 'Erreur lors de la génération du quiz: ' . $result['error']);
                    return $this->redirectToRoute('app_formation_generate_quiz', ['id' => $formation->getId()]);
                }

                // Create the quiz entity
                $quiz = $quizGeneratorService->createQuizEntity($formation, $result['data']);

                $this->addFlash('success', 'Le quiz a été généré avec succès');
                return $this->redirectToRoute('app_quiz_show', ['id' => $quiz->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la génération du quiz: ' . $e->getMessage());
            }
        }

        return $this->render('back/formation/generate_quiz.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/generate-quiz-ajax', name: 'app_formation_generate_quiz_ajax', methods: ['POST'])]
    public function generateQuizAjax(
        Request $request,
        Formation $formation,
        QuizGeneratorService $quizGeneratorService
    ): JsonResponse {
        // Get the options from the request
        $data = json_decode($request->getContent(), true);
        $options = $data['options'] ?? [];

        try {
            // Generate the quiz
            $result = $quizGeneratorService->generateQuiz($formation, $options);

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

    #[Route('/{id}/save-generated-quiz', name: 'app_formation_save_generated_quiz', methods: ['POST'])]
    public function saveGeneratedQuiz(
        Request $request,
        Formation $formation,
        QuizGeneratorService $quizGeneratorService,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Get the quiz data from the request
        $data = json_decode($request->getContent(), true);
        $quizData = $data['quiz'] ?? null;

        if (!$quizData) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Données de quiz manquantes',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Create the quiz entity
            $quiz = $quizGeneratorService->createQuizEntity($formation, $quizData);

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
}
