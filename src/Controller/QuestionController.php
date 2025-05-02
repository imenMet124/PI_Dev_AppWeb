<?php

namespace App\Controller;

use App\Entity\Option;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/back/question')]
class QuestionController extends AbstractController
{
    #[Route('/', name: 'app_question_index', methods: ['GET'])]
    public function index(QuestionRepository $questionRepository): Response
    {
        return $this->render('back/question/index.html.twig', [
            'questions' => $questionRepository->findAll(),
        ]);
    }

    #[Route('/quiz/{quiz_id}', name: 'app_question_by_quiz', methods: ['GET'])]
    public function questionsByQuiz(int $quiz_id, QuestionRepository $questionRepository, EntityManagerInterface $entityManager): Response
    {
        $quiz = $entityManager->getRepository(Quiz::class)->find($quiz_id);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        return $this->render('back/question/index.html.twig', [
            'questions' => $questionRepository->findByQuiz($quiz),
            'quiz' => $quiz
        ]);
    }

    #[Route('/new', name: 'app_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $question = new Question();

        // Add at least two empty options
        $option1 = new Option();
        $option2 = new Option();
        $question->addOption($option1);
        $question->addOption($option2);

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if at least one option is marked as correct
            if (!$question->hasCorrectOption()) {
                $this->addFlash('error', 'Au moins une option doit être marquée comme correcte');
                return $this->render('back/question/new.html.twig', [
                    'question' => $question,
                    'form' => $form,
                ]);
            }

            // Ensure the Quiz is managed by fetching it from the database
            $quizId = $question->getQuiz()->getId();
            $quiz = $entityManager->getRepository(Quiz::class)->find($quizId);

            if (!$quiz) {
                $this->addFlash('error', 'Vous devez sélectionner un quiz valide');
                return $this->render('back/question/new.html.twig', [
                    'question' => $question,
                    'form' => $form,
                ]);
            }

            // Set the managed quiz entity
            $question->setQuiz($quiz);

            // First persist the question
            $entityManager->persist($question);

            // Then persist each option and ensure it's linked to the question
            foreach ($question->getOptions() as $option) {
                $option->setQuestion($question);
                $entityManager->persist($option);
            }

            $entityManager->flush();

            $this->addFlash('success', 'La question a été créée avec succès');

            return $this->redirectToRoute('app_question_by_quiz', ['quiz_id' => $quiz->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/question/new.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }
    #[Route('/new/quiz/{quiz_id}', name: 'app_question_new_for_quiz', methods: ['GET', 'POST'])]
    public function newForQuiz(int $quiz_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Fetch the quiz entity from the database
        $quiz = $entityManager->getRepository(Quiz::class)->find($quiz_id);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        $question = new Question();

        // Add at least two empty options
        $option1 = new Option();
        $option2 = new Option();
        $question->addOption($option1);
        $question->addOption($option2);

        // Set the managed quiz
        $question->setQuiz($quiz);

        $form = $this->createForm(QuestionType::class, $question, [
            'quiz' => $quiz // Pass the quiz to the form to disable the quiz field
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if at least one option is marked as correct
            if (!$question->hasCorrectOption()) {
                $this->addFlash('error', 'Au moins une option doit être marquée comme correcte');
                return $this->render('back/question/new.html.twig', [
                    'question' => $question,
                    'form' => $form,
                    'quiz' => $quiz,
                ]);
            }

            // Make sure we're still using the managed quiz entity
            $question->setQuiz($quiz);

            // First persist the question
            $entityManager->persist($question);

            // Then persist each option and ensure it's linked to the question
            foreach ($question->getOptions() as $option) {
                $option->setQuestion($question);
                $entityManager->persist($option);
            }

            $entityManager->flush();

            $this->addFlash('success', 'La question a été créée avec succès');
            return $this->redirectToRoute('app_question_by_quiz', ['quiz_id' => $quiz_id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/question/new.html.twig', [
            'question' => $question,
            'form' => $form,
            'quiz' => $quiz,
        ]);
    }
    #[Route('/{id}', name: 'app_question_show', methods: ['GET'])]
    public function show(Question $question, QuestionRepository $questionRepository): Response
    {
        // Use the repository to get the question with its options
        $question = $questionRepository->findWithOptions($question->getId());

        return $this->render('back/question/show.html.twig', [
            'question' => $question,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Question $question, EntityManagerInterface $entityManager, QuestionRepository $questionRepository): Response
    {
        // Get the question with its options
        $question = $questionRepository->findWithOptions($question->getId());

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if at least one option is marked as correct
            if (!$question->hasCorrectOption()) {
                $this->addFlash('error', 'Au moins une option doit être marquée comme correcte');
                return $this->render('back/question/edit.html.twig', [
                    'question' => $question,
                    'form' => $form,
                ]);
            }

            // Ensure the Quiz is managed by fetching it from the database
            $quizId = $question->getQuiz()->getId();
            $quiz = $entityManager->getRepository(Quiz::class)->find($quizId);

            if (!$quiz) {
                $this->addFlash('error', 'Le quiz associé est invalide');
                return $this->render('back/question/edit.html.twig', [
                    'question' => $question,
                    'form' => $form,
                ]);
            }

            // Set the managed quiz entity
            $question->setQuiz($quiz);

            // Make sure all options have the question set correctly
            // and handle option deletion
            foreach ($question->getOptions() as $option) {
                $option->setQuestion($question);
                $entityManager->persist($option);
            }

            // The options marked for deletion will be automatically removed
            // because we set 'allow_delete' => true in the form type
            // and we're using by_reference => false

            $entityManager->flush();

            $this->addFlash('success', 'La question a été modifiée avec succès');

            return $this->redirectToRoute('app_question_by_quiz', ['quiz_id' => $quiz->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/question/edit.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_question_delete', methods: ['POST'])]
    public function delete(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $question->getId(), $request->getPayload()->getString('_token'))) {
                // Soft delete - set the deletedAt timestamp instead of removing
                $question->delete();
                $entityManager->flush();
                $this->addFlash('success', 'La question a été supprimée avec succès');
            } else {
                $this->addFlash('danger', 'Erreur de sécurité lors de la suppression de la question');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la suppression de la question: ' . $e->getMessage());
        }

        // If the question belongs to a quiz, redirect to the quiz's questions list
        if ($question->getQuiz()) {
            return $this->redirectToRoute('app_question_by_quiz', ['quiz_id' => $question->getQuiz()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
    }
}
