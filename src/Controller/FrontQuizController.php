<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quiz')]
class FrontQuizController extends AbstractController
{
    #[Route('/{id}/take', name: 'app_front_quiz_take', methods: ['GET'])]
    public function take(Quiz $quiz, QuizRepository $quizRepository): Response
    {
        // Get the quiz with questions and options
        $quiz = $quizRepository->findWithQuestionsAndOptions($quiz->getId());

        // If quiz doesn't exist or is deleted, redirect to formations
        if (!$quiz) {
            $this->addFlash('error', 'Le quiz demandé n\'existe pas.');
            return $this->redirectToRoute('app_front_formation_index');
        }

        return $this->render('front/quiz/take.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/submit', name: 'app_front_quiz_submit', methods: ['POST'])]
    public function submit(Request $request, Quiz $quiz, QuizRepository $quizRepository, SessionInterface $session): Response
    {
        // Get the quiz with questions and options
        $quiz = $quizRepository->findWithQuestionsAndOptions($quiz->getId());
        
        // If quiz doesn't exist or is deleted, redirect to formations
        if (!$quiz) {
            $this->addFlash('error', 'Le quiz demandé n\'existe pas.');
            return $this->redirectToRoute('app_front_formation_index');
        }

        // Get submitted answers
        $submittedAnswers = $request->request->all();
        
        // Calculate score
        $score = 0;
        $totalQuestions = count($quiz->getQuestions());
        $userAnswers = [];
        
        foreach ($quiz->getQuestions() as $question) {
            $questionId = $question->getId();
            $selectedOptionId = $submittedAnswers['question_' . $questionId] ?? null;
            
            // Store user's answer for display in results
            $userAnswers[$questionId] = $selectedOptionId;
            
            // Check if answer is correct
            foreach ($question->getOptions() as $option) {
                if ($option->getId() == $selectedOptionId && $option->isIsCorrect()) {
                    $score++;
                    break;
                }
            }
        }
        
        // Calculate percentage
        $percentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;
        
        // Store results in session
        $results = [
            'quiz_id' => $quiz->getId(),
            'quiz_title' => $quiz->getTitle(),
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => $percentage,
            'user_answers' => $userAnswers,
        ];
        
        $session->set('quiz_results', $results);
        
        return $this->redirectToRoute('app_front_quiz_result', ['id' => $quiz->getId()]);
    }

    #[Route('/{id}/result', name: 'app_front_quiz_result', methods: ['GET'])]
    public function result(Quiz $quiz, QuizRepository $quizRepository, SessionInterface $session): Response
    {
        // Get results from session
        $results = $session->get('quiz_results');
        
        // If no results or different quiz, redirect to take the quiz
        if (!$results || $results['quiz_id'] != $quiz->getId()) {
            return $this->redirectToRoute('app_front_quiz_take', ['id' => $quiz->getId()]);
        }
        
        // Get the quiz with questions and options for detailed feedback
        $quiz = $quizRepository->findWithQuestionsAndOptions($quiz->getId());
        
        return $this->render('front/quiz/result.html.twig', [
            'quiz' => $quiz,
            'results' => $results,
        ]);
    }
}
