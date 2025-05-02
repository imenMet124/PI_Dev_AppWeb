<?php

namespace App\Service;

use App\Entity\Option;
use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;

class MockQuizGeneratorService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Generate a quiz directly from a title and description
     *
     * @param string $title The title of the formation
     * @param string|null $description Optional description of the formation
     * @param array $options Options for quiz generation
     * @return array The result of the quiz generation
     */
    public function generateQuizFromTitle(string $title, ?string $description = null, array $options = []): array
    {
        // Debug log
        error_log('MockQuizGeneratorService::generateQuizFromTitle called');
        error_log('Title: ' . $title);
        error_log('Description: ' . ($description ?? 'null'));
        error_log('Options: ' . json_encode($options));

        // Default options
        $defaultOptions = [
            'numQuestions' => 5,
            'numOptions' => 4,
            'difficulty' => 'medium',
            'language' => 'french',
        ];

        $options = array_merge($defaultOptions, $options);
        
        // Generate mock quiz data
        $quizData = [
            'questions' => []
        ];
        
        $numQuestions = $options['numQuestions'];
        $numOptions = $options['numOptions'];
        
        for ($i = 0; $i < $numQuestions; $i++) {
            $question = [
                'text' => "Question " . ($i + 1) . " sur " . $title,
                'options' => []
            ];
            
            $correctOptionIndex = rand(0, $numOptions - 1);
            
            for ($j = 0; $j < $numOptions; $j++) {
                $option = [
                    'text' => "Option " . ($j + 1) . " pour la question " . ($i + 1),
                    'isCorrect' => ($j === $correctOptionIndex)
                ];
                
                $question['options'][] = $option;
            }
            
            $quizData['questions'][] = $question;
        }
        
        return [
            'success' => true,
            'data' => $quizData
        ];
    }

    /**
     * Create a quiz entity from the generated data without a formation
     *
     * @param string $title The title for the quiz
     * @param array $quizData The generated quiz data
     * @return Quiz The created quiz entity
     */
    public function createQuizEntityFromTitle(string $title, array $quizData): Quiz
    {
        // Create a new quiz
        $quiz = new Quiz();
        $quiz->setTitle('Quiz: ' . $title);

        // Add questions and options
        foreach ($quizData['questions'] as $questionData) {
            $question = new Question();
            $question->setText($questionData['text']);
            $question->setQuiz($quiz);

            // Add options
            foreach ($questionData['options'] as $optionData) {
                $option = new Option();
                $option->setText($optionData['text']);
                $option->setIsCorrect($optionData['isCorrect']);
                $option->setQuestion($question);
                $question->addOption($option);
            }

            $quiz->addQuestion($question);
        }

        // Persist the quiz
        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        return $quiz;
    }
}
