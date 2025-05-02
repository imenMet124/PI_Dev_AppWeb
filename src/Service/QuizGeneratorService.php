<?php

namespace App\Service;

use App\Entity\Formation;
use App\Entity\Option;
use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;

class QuizGeneratorService
{
    private OpenRouterApiService $openRouterApiService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        OpenRouterApiService $openRouterApiService,
        EntityManagerInterface $entityManager
    ) {
        $this->openRouterApiService = $openRouterApiService;
        $this->entityManager = $entityManager;
    }

    /**
     * Generate a quiz for a formation
     *
     * @param Formation $formation The formation to generate a quiz for
     * @param array $options Options for quiz generation
     * @return array The result of the quiz generation
     */
    public function generateQuiz(Formation $formation, array $options = []): array
    {
        // Default options
        $defaultOptions = [
            'numQuestions' => 5,
            'numOptions' => 4,
            'difficulty' => 'medium', // easy, medium, hard
            'language' => 'french',
        ];

        $options = array_merge($defaultOptions, $options);

        // Create the prompt for the AI
        $prompt = $this->createPrompt($formation, $options);

        // Call the OpenRouter API
        $response = $this->openRouterApiService->generateContent($prompt, [
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ]);

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['error'],
            ];
        }

        // Parse the response to extract questions and options
        $parsedQuiz = $this->parseQuizResponse($response['data']);

        if (!$parsedQuiz['success']) {
            return [
                'success' => false,
                'error' => $parsedQuiz['error'],
            ];
        }

        return [
            'success' => true,
            'data' => $parsedQuiz['data'],
        ];
    }

    /**
     * Create a quiz entity from the generated data
     *
     * @param Formation $formation The formation to associate the quiz with
     * @param array $quizData The generated quiz data
     * @return Quiz The created quiz entity
     */
    public function createQuizEntity(Formation $formation, array $quizData): Quiz
    {
        // Create a new quiz
        $quiz = new Quiz();
        $quiz->setTitle('Quiz: ' . $formation->getTitre());
        $quiz->setFormation($formation);

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
        error_log('generateQuizFromTitle called');
        error_log('Title: ' . $title);
        error_log('Description: ' . ($description ?? 'null'));
        error_log('Options: ' . json_encode($options));

        // Default options
        $defaultOptions = [
            'numQuestions' => 5,
            'numOptions' => 4,
            'difficulty' => 'medium', // easy, medium, hard
            'language' => 'french',
        ];

        $options = array_merge($defaultOptions, $options);
        error_log('Merged options: ' . json_encode($options));

        // Create the prompt for the AI
        $prompt = $this->createPromptFromTitle($title, $description, $options);
        error_log('Generated prompt: ' . substr($prompt, 0, 200) . '...');

        // Call the OpenRouter API
        error_log('Calling OpenRouter API...');
        $response = $this->openRouterApiService->generateContent($prompt, [
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ]);
        error_log('OpenRouter API response success: ' . ($response['success'] ? 'true' : 'false'));

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['error'],
            ];
        }

        // Parse the response to extract questions and options
        $parsedQuiz = $this->parseQuizResponse($response['data']);

        if (!$parsedQuiz['success']) {
            return [
                'success' => false,
                'error' => $parsedQuiz['error'],
            ];
        }

        return [
            'success' => true,
            'data' => $parsedQuiz['data'],
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

    /**
     * Create a prompt for the AI based on the formation and options
     *
     * @param Formation $formation The formation to generate a quiz for
     * @param array $options Options for quiz generation
     * @return string The prompt for the AI
     */
    private function createPrompt(Formation $formation, array $options): string
    {
        $prompt = "Génère un quiz de {$options['numQuestions']} questions à choix multiples sur le sujet suivant:\n\n";
        $prompt .= "Titre: {$formation->getTitre()}\n";

        if ($formation->getDescription()) {
            $prompt .= "Description: {$formation->getDescription()}\n\n";
        }

        $prompt .= "Instructions spécifiques:\n";
        $prompt .= "1. Crée exactement {$options['numQuestions']} questions à choix multiples.\n";
        $prompt .= "2. Chaque question doit avoir exactement {$options['numOptions']} options.\n";
        $prompt .= "3. Une seule option doit être correcte pour chaque question.\n";
        $prompt .= "4. Le niveau de difficulté doit être '{$options['difficulty']}'.\n";
        $prompt .= "5. Les questions doivent être en {$options['language']}.\n";
        $prompt .= "6. Retourne les résultats au format JSON avec la structure suivante:\n";
        $prompt .= "```json
{
  \"questions\": [
    {
      \"text\": \"Texte de la question\",
      \"options\": [
        {
          \"text\": \"Texte de l'option\",
          \"isCorrect\": true/false
        },
        ...
      ]
    },
    ...
  ]
}
```\n";
        $prompt .= "7. Assure-toi que le JSON est valide et respecte exactement cette structure.\n";

        return $prompt;
    }

    /**
     * Parse the response from the AI to extract questions and options
     *
     * @param array $response The response from the AI
     * @return array The parsed quiz data
     */
    private function parseQuizResponse(array $response): array
    {
        try {
            // Add detailed logging for debugging
            error_log('Response structure: ' . json_encode(array_keys($response)));

            // Extract the content from the response
            $content = $response['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                error_log('No content field found in response. Full response: ' . json_encode($response));
                return [
                    'success' => false,
                    'error' => 'No content found in the response',
                ];
            }

            error_log('Content extracted: ' . substr($content, 0, 100) . '...');

            // Extract JSON from the content
            preg_match('/```json(.*?)```/s', $content, $matches);

            if (empty($matches[1])) {
                // Try to find JSON without the markdown code block
                error_log('No code block found, trying to extract raw JSON');
                preg_match('/\{.*\}/s', $content, $matches);

                if (empty($matches[0])) {
                    error_log('No JSON object found in content. Content: ' . $content);

                    // As a fallback, try to manually format the content as quiz data
                    if (strpos($content, 'question') !== false) {
                        error_log('Attempting to manually parse content as quiz data');
                        return $this->attemptManualParsing($content);
                    }

                    return [
                        'success' => false,
                        'error' => 'No JSON found in the response',
                    ];
                }

                $jsonString = $matches[0];
            } else {
                $jsonString = $matches[1];
            }

            error_log('JSON string extracted: ' . substr($jsonString, 0, 100) . '...');

            // Parse the JSON
            $quizData = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('JSON decode error: ' . json_last_error_msg() . '. JSON string: ' . $jsonString);
                return [
                    'success' => false,
                    'error' => 'Invalid JSON: ' . json_last_error_msg(),
                ];
            }

            // Validate the structure
            if (!isset($quizData['questions']) || !is_array($quizData['questions'])) {
                error_log('Invalid quiz structure: questions array not found. Data: ' . json_encode($quizData));
                return [
                    'success' => false,
                    'error' => 'Invalid quiz structure: questions array not found',
                ];
            }

            foreach ($quizData['questions'] as $index => $question) {
                if (!isset($question['text']) || !isset($question['options']) || !is_array($question['options'])) {
                    error_log('Invalid question structure at index ' . $index . ': ' . json_encode($question));
                    return [
                        'success' => false,
                        'error' => "Invalid question structure at index $index",
                    ];
                }

                $hasCorrectOption = false;
                foreach ($question['options'] as $optionIndex => $option) {
                    if (!isset($option['text']) || !isset($option['isCorrect'])) {
                        error_log('Invalid option structure at question ' . $index . ', option ' . $optionIndex . ': ' . json_encode($option));
                        return [
                            'success' => false,
                            'error' => "Invalid option structure at question $index, option $optionIndex",
                        ];
                    }

                    if ($option['isCorrect']) {
                        $hasCorrectOption = true;
                    }
                }

                if (!$hasCorrectOption) {
                    error_log('Question at index ' . $index . ' has no correct option: ' . json_encode($question));
                    return [
                        'success' => false,
                        'error' => "Question at index $index has no correct option",
                    ];
                }
            }

            return [
                'success' => true,
                'data' => $quizData,
            ];
        } catch (\Exception $e) {
            error_log('Exception in parseQuizResponse: ' . $e->getMessage());
            error_log('Exception trace: ' . $e->getTraceAsString());
            return [
                'success' => false,
                'error' => 'Error parsing response: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Attempt to manually parse content that doesn't contain proper JSON
     */
    private function attemptManualParsing(string $content): array
    {
        try {
            error_log('Attempting manual parsing of content');
            $questions = [];

            // Split by numbered questions (1., 2., etc.)
            if (preg_match_all('/(?:\d+\.\s*)(.*?)(?=\d+\.|$)/s', $content, $matches)) {
                foreach ($matches[1] as $index => $questionBlock) {
                    error_log('Processing question block ' . ($index + 1));

                    // Extract the question text
                    if (preg_match('/^(.+?)(?:\n|$)/s', trim($questionBlock), $questionMatch)) {
                        $questionText = trim($questionMatch[1]);

                        // Extract options
                        $options = [];
                        if (preg_match_all('/(?:[a-z]\)|\-|\*)\s*(.+?)(?=(?:[a-z]\)|\-|\*|$))/s', $questionBlock, $optionMatches)) {
                            foreach ($optionMatches[1] as $optIndex => $optionText) {
                                $optionText = trim($optionText);

                                // Check if this option is marked as correct
                                $isCorrect = false;
                                if (
                                    stripos($optionText, 'correct') !== false ||
                                    stripos($questionBlock, 'correct answer: ' . chr(97 + $optIndex)) !== false ||
                                    stripos($questionBlock, 'correct: ' . chr(97 + $optIndex)) !== false
                                ) {
                                    $isCorrect = true;
                                }

                                $options[] = [
                                    'text' => $optionText,
                                    'isCorrect' => $isCorrect
                                ];
                            }
                        }

                        // Ensure at least one option is correct
                        $hasCorrect = false;
                        foreach ($options as $option) {
                            if ($option['isCorrect']) {
                                $hasCorrect = true;
                                break;
                            }
                        }

                        // If no correct option found, mark the first one as correct
                        if (!$hasCorrect && count($options) > 0) {
                            $options[0]['isCorrect'] = true;
                        }

                        if (count($options) > 0) {
                            $questions[] = [
                                'text' => $questionText,
                                'options' => $options
                            ];
                        }
                    }
                }
            }

            if (count($questions) > 0) {
                error_log('Successfully created ' . count($questions) . ' questions through manual parsing');
                return [
                    'success' => true,
                    'data' => ['questions' => $questions]
                ];
            } else {
                error_log('Manual parsing failed to extract questions');
                return [
                    'success' => false,
                    'error' => 'Could not extract questions from the content'
                ];
            }
        } catch (\Exception $e) {
            error_log('Exception in manual parsing: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error in manual parsing: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a prompt for the AI based on a title and description
     *
     * @param string $title The title of the formation
     * @param string|null $description Optional description of the formation
     * @param array $options Options for quiz generation
     * @return string The prompt for the AI
     */
    private function createPromptFromTitle(string $title, ?string $description, array $options): string
    {
        $prompt = "Génère un quiz de {$options['numQuestions']} questions à choix multiples sur le sujet suivant:\n\n";
        $prompt .= "Titre: {$title}\n";

        if ($description) {
            $prompt .= "Description: {$description}\n\n";
        }

        $prompt .= "Instructions spécifiques:\n";
        $prompt .= "1. Crée exactement {$options['numQuestions']} questions à choix multiples.\n";
        $prompt .= "2. Chaque question doit avoir exactement {$options['numOptions']} options.\n";
        $prompt .= "3. Une seule option doit être correcte pour chaque question.\n";
        $prompt .= "4. Le niveau de difficulté doit être '{$options['difficulty']}'.\n";
        $prompt .= "5. Les questions doivent être en {$options['language']}.\n";
        $prompt .= "6. Retourne les résultats au format JSON avec la structure suivante:\n";
        $prompt .= "```json
{
  \"questions\": [
    {
      \"text\": \"Texte de la question\",
      \"options\": [
        {
          \"text\": \"Texte de l'option\",
          \"isCorrect\": true/false
        },
        ...
      ]
    },
    ...
  ]
}
```\n";
        $prompt .= "7. Assure-toi que le JSON est valide et respecte exactement cette structure.\n";

        return $prompt;
    }
}
