<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeepSeekService
{
    private const API_URL = 'https://api.deepseek.com/v1/chat/completions';
    private const API_KEY = 'sk-8e90da0e83c247f48ab1ca253ed638a2'; // Replace with your actual key

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function sendMessage(string $message): string
    {
        try {
            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . self::API_KEY,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        ['role' => 'user', 'content' => $message]
                    ],
                    'max_tokens' => 10,
                ],
            ]);

            $content = $response->toArray();
            return $content['choices'][0]['message']['content'] ?? 'No response from DeepSeek API.';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}