<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class OpenRouterApiService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $apiUrl;

    public function __construct(
        HttpClientInterface $httpClient = null,
        ParameterBagInterface $params
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create([
            'timeout' => 60, // Increase timeout to 60 seconds
            'max_duration' => 90, // Max duration of 90 seconds
        ]);
        $this->apiKey = $params->get('openrouter_api_key');
        $this->apiUrl = $params->get('openrouter_api_url');
    }

    /**
     * Generate content using the OpenRouter API
     *
     * @param string $prompt The prompt to send to the API
     * @param array $options Additional options for the API request
     * @return array The API response
     */
    public function generateContent(string $prompt, array $options = []): array
    {
        // Debug log
        error_log('OpenRouterApiService::generateContent called');
        error_log('API URL: ' . $this->apiUrl);
        error_log('API Key length: ' . (strlen($this->apiKey) > 0 ? strlen($this->apiKey) : 'empty'));

        $defaultOptions = [
            'model' => 'meta-llama/llama-4-maverick:free',
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ];

        $requestOptions = array_merge($defaultOptions, $options);
        $requestOptions['messages'] = [
            [
                'role' => 'system',
                'content' => 'You are an expert educational content creator specializing in creating high-quality quiz questions. Your task is to create clear, concise, and challenging multiple-choice questions with one correct answer and several plausible but incorrect options. IMPORTANT: Your response MUST be in valid JSON format following the exact structure requested. Do not include any explanations outside of the JSON structure.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        error_log('Request options: ' . json_encode(array_diff_key($requestOptions, ['messages' => null])));

        try {
            error_log('Sending request to OpenRouter API...');
            $response = $this->httpClient->request('POST', $this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                    'X-Title' => 'Quiz Generator',
                    'Accept' => 'application/json'
                ],
                'json' => $requestOptions,
            ]);

            error_log('Request sent successfully');

            $statusCode = $response->getStatusCode();
            error_log('Response status code: ' . $statusCode);

            if ($statusCode !== Response::HTTP_OK) {
                // Get error details from response if available
                try {
                    $errorData = $response->toArray(false);
                    $errorMessage = $errorData['error']['message'] ?? 'Unknown API error';
                    error_log('API error details: ' . json_encode($errorData));
                } catch (\Exception $e) {
                    $errorMessage = 'API returned status code ' . $statusCode;
                }

                error_log('API returned non-OK status code: ' . $statusCode . ' - ' . $errorMessage);
                return [
                    'success' => false,
                    'error' => $errorMessage,
                ];
            }

            try {
                $content = $response->toArray();
                error_log('Response content received successfully: ' . json_encode(array_keys($content)));

                // Check if the response has the expected structure
                if (!isset($content['choices']) || !is_array($content['choices']) || count($content['choices']) === 0) {
                    error_log('Invalid API response structure: missing choices array. Full response: ' . json_encode($content));
                    return [
                        'success' => false,
                        'error' => 'Invalid API response structure: missing choices array',
                    ];
                }

                return [
                    'success' => true,
                    'data' => $content,
                ];
            } catch (\Exception $e) {
                error_log('Error parsing API response: ' . $e->getMessage());
                error_log('Raw response: ' . $response->getContent(false));
                return [
                    'success' => false,
                    'error' => 'Error parsing API response: ' . $e->getMessage(),
                ];
            }
        } catch (\Exception $e) {
            error_log('Exception in OpenRouterApiService: ' . $e->getMessage());
            error_log('Exception trace: ' . $e->getTraceAsString());
            return [
                'success' => false,
                'error' => 'API request failed: ' . $e->getMessage(),
            ];
        }
    }
}
