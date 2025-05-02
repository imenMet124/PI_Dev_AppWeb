<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TacheRepository;
use Dompdf\Dompdf;
use Dompdf\Options;

final class HomeController extends AbstractController{
    #[Route('/', name: 'app_home')]
    public function index(TacheRepository $tacheRepository): Response
    {
        $totalTasks = $tacheRepository->getTotalTasks();
        $totalCompleted = $tacheRepository->getTotalCompletedTasks();
        $completionRates = $tacheRepository->getCompletionRatePerUser();
        $topCompleter = $tacheRepository->getTopCompleter();
        $leastCompleter = $tacheRepository->getLeastCompleter();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'totalTasks' => $totalTasks,
            'totalCompleted' => $totalCompleted,
            'completionRates' => $completionRates,
            'topCompleter' => $topCompleter,
            'leastCompleter' => $leastCompleter,
        ]);
    }  

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [

        ]);
    }

    #[Route('/download-statistics-pdf', name: 'download_statistics_pdf')]
    public function downloadStatisticsPdf(TacheRepository $tacheRepository): Response
    {
        $totalTasks = $tacheRepository->getTotalTasks();
        $totalCompleted = $tacheRepository->getTotalCompletedTasks();
        $completionRates = $tacheRepository->getCompletionRatePerUser();
        $topCompleter = $tacheRepository->getTopCompleter();
        $leastCompleter = $tacheRepository->getLeastCompleter();

        $html = $this->renderView('home/statistics_pdf.html.twig', [
            'totalTasks' => $totalTasks,
            'totalCompleted' => $totalCompleted,
            'completionRates' => $completionRates,
            'topCompleter' => $topCompleter,
            'leastCompleter' => $leastCompleter,
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->stream('task_statistics.pdf', ['Attachment' => true]),
            200,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }
}
