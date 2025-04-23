<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailSendTestController extends AbstractController
{
    #[Route('/send-test-mail', name: 'send_test_mail')]
    public function index(TransportInterface $transport): Response
    {
        $email = new Email();
        $email->from('expediteur@test.com');
        $email->to('destinataire@test.com');
        $email->subject('Test email direct via transport');
        $email->text('Ceci est un test direct utilisant le transport. Timestamp: ' . time());
        
        try {
            $transport->send($email);
            return new Response('✅ SUCCÈS: Email envoyé avec succès à ' . date('H:i:s'));
        } catch (TransportExceptionInterface $e) {
            return new Response('❌ ERREUR: ' . $e->getMessage() . "\n\nTrace:\n" . $e->getTraceAsString(), 500);
        }
    }
}
