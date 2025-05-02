<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Repository\OptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackController extends AbstractController
{
    #[Route(['/back', '/back/'], name: 'app_back')]
    public function index(
        FormationRepository $formationRepository,
        QuizRepository $quizRepository,
        QuestionRepository $questionRepository,
        OptionRepository $optionRepository
    ): Response {
        // Get counts for dashboard stats
        $formationsCount = count($formationRepository->findAll());
        $quizzesCount = count($quizRepository->findAll());
        $questionsCount = count($questionRepository->findAll());
        $optionsCount = count($optionRepository->findAll());

        // Get recent formations (limit to 5)
        $recentFormations = $formationRepository->findBy([], ['dateCreation' => 'DESC'], 5);

        // Get recent quizzes (limit to 5)
        $recentQuizzes = $quizRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('back/index.html.twig', [
            'formations_count' => $formationsCount,
            'quizzes_count' => $quizzesCount,
            'questions_count' => $questionsCount,
            'options_count' => $optionsCount,
            'recent_formations' => $recentFormations,
            'recent_quizzes' => $recentQuizzes,
        ]);
    }
}
