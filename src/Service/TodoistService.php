<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TodoistService
{
    private const CLIENT_ID = '935b272bb3f04031a1e54a0731baa622';
    private const CLIENT_SECRET = 'a6dd8e9791b040d3b3b963e01a53a268';
    private const REDIRECT_URI = 'http://localhost:8000/callback';
    private const TOKEN_URL = 'https://api.todoist.com/oauth/access_token';
    private const TASKS_URL = 'https://api.todoist.com/rest/v2/tasks';

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getOAuthUrl(): string
    {
        $scope = 'task:add,data:read_write';
        return sprintf(
            'https://todoist.com/oauth/authorize?client_id=%s&scope=%s&redirect_uri=%s',
            self::CLIENT_ID,
            $scope,
            self::REDIRECT_URI
        );
    }

    public function exchangeAuthCodeForToken(string $authorizationCode): ?string
    {
        $response = $this->httpClient->request('POST', self::TOKEN_URL, [
            'body' => [
                'client_id' => self::CLIENT_ID,
                'client_secret' => self::CLIENT_SECRET,
                'code' => $authorizationCode,
                'redirect_uri' => self::REDIRECT_URI,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data = $response->toArray();
        return $data['access_token'] ?? null;
    }

    public function getTasks(string $accessToken): array
    {
        $response = $this->httpClient->request('GET', self::TASKS_URL, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        return $response->toArray();
    }

    public function createTask(string $accessToken, string $content, ?string $dueDate = null): bool
    {
        $body = ['content' => $content];
        if ($dueDate) {
            $body['due_date'] = $dueDate;
        }

        $response = $this->httpClient->request('POST', self::TASKS_URL, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $body,
        ]);

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    public function completeTask(string $accessToken, string $taskId): bool
    {
        $url = sprintf('%s/%s/close', self::TASKS_URL, $taskId);

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    public function deleteTask(string $accessToken, string $taskId): bool
    {
        $url = sprintf('%s/%s', self::TASKS_URL, $taskId);

        $response = $this->httpClient->request('DELETE', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }
}