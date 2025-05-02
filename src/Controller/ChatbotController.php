<?php

namespace App\Controller;

use App\Service\DeepSeekService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController
{
    private DeepSeekService $deepSeekService;

    public function __construct(DeepSeekService $deepSeekService)
    {
        $this->deepSeekService = $deepSeekService;
    }

    /**
     * @Route("/chatbot", name="chatbot", methods={"POST"})
     */
    public function chatbot(Request $request, SessionInterface $session): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['response' => 'You must be logged in to use the chatbot.'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';

        if (empty($message)) {
            return new JsonResponse(['response' => 'Message cannot be empty.'], 400);
        }

        // Call DeepSeek API
        $response = $this->deepSeekService->sendMessage($message);

        // Save chat history in session
        $chatHistory = $session->get('chat_history', []);
        $chatHistory[] = ['role' => 'user', 'content' => $message];
        $chatHistory[] = ['role' => 'bot', 'content' => $response];
        $session->set('chat_history', $chatHistory);

        return new JsonResponse(['response' => $response]);
    }

    /**
     * @Route("/chatbot/history", name="chatbot_history", methods={"GET"})
     */
    public function getChatHistory(SessionInterface $session): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['history' => []], 403);
        }

        $chatHistory = $session->get('chat_history', []);
        return new JsonResponse(['history' => $chatHistory]);
    }
}