<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\Utilisateur;
use App\Entity\Evenement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Component\Pager\PaginatorInterface;

final class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(EvenementRepository $evenementRepository): Response
    {
        $date = new \DateTime(); // Date actuelle
        $evenements = $evenementRepository->getEvenementsAvailable($date); // Assurez-vous que cette méthode existe dans votre repository
        $events = [];

        foreach ($evenements as $evenement) {
            $events[] = [
                'title' => $evenement->getNomEvenement(),
                'date' => $evenement->getDate()->format('Y-m-d'),
                'heure' => $evenement->getHeure()->format('H:i:s'),
                'capacite' => $evenement->getCapacite(),
                'nombre_participants' => $evenement->getNombreParticipants(),
                'url'   => $this->generateUrl('app_evenement_show', ['id' => $evenement->getId()]),
            ];
        }
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'calendarEvents' => json_encode($events),
        ]);
    }

    #[Route('/evenement/show/{id}', name: 'app_evenement_show')]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }



    // Ajoute SluggerInterface dans la méthode :


    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement, [
            'is_edit' => true, // Indique qu'il s'agit d'une modification
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('Image_Path')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), // chemin défini dans services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }

                $evenement->setImagePath('uploads/images/' . $newFilename);
            }

            // Si aucune nouvelle image n'est téléchargée, conserver l'image existante
            if (!$imageFile) {
                $evenement->setImagePath($evenement->getImagePath());
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_back_evenement', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/back/evenement/new', name: 'app_back_evenement_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        TransportInterface $transport,
        UtilisateurRepository $utilisateurRepository // <-- Ici aussi
    ): Response {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('Image_Path')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }

                $evenement->setImagePath('uploads/images/' . $newFilename);
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            // >>> Envoi de mail à tous les utilisateurs
            $utilisateurs = $utilisateurRepository->findAll(); // <-- Ici aussi
            foreach ($utilisateurs as $utilisateur) {
                if ($utilisateur->getEmail()) { // Vérifie qu'il a un email
                    $email = (new Email())
                        ->from('AdminHR@EasyHR.com')
                        ->to($utilisateur->getEmail())
                        ->subject('Nouveau événement planifié')
                        ->text('Un nouvel événement "' . $evenement->getNomEvenement() . '" a été planifié. Venez le découvrir ! Timestamp: ' . time());

                    try {
                        $transport->send($email);
                    } catch (TransportExceptionInterface $e) {
                        $this->addFlash('error', 'Erreur lors de l\'envoi du mail à ' . $utilisateur->getEmail() . ': ' . $e->getMessage());
                    }
                }
            }
            // <<< Fin d'envoi d'e-mails

            return $this->redirectToRoute('app_back_evenement', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/back/evenement/delete/{id}', name: 'app_back_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->getPayload()->getString('_token'))) {
            // Supprimer les participations associées manuellement si nécessaire
            foreach ($evenement->getParticipations() as $participation) {
                $entityManager->remove($participation);
            }

            // Ensuite supprimer l'événement
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_back_evenement', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/back/evenement', name: 'app_back_evenement')]
    public function getEvenements(EvenementRepository $evenementRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupérer tous les événements avec un QueryBuilder
        $queryBuilder = $evenementRepository->createQueryBuilder('e');

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder, // Query ou QueryBuilder
            $request->query->getInt('page', 1), // Numéro de la page (par défaut 1)
            10 // Nombre d'éléments par page
        );

        // Rendre la vue avec la pagination
        return $this->render('back/evenement/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/evenement/{id}/participer', name: 'app_evenement_participer', methods: ['POST'])]
    public function participer(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): JsonResponse
    {
        // Simuler un utilisateur connecté (temporaire)
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find(1);
    
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé.'], 404);
        }
    
        if ($evenement->getNombreParticipants() >= $evenement->getCapacite()) {
            return new JsonResponse(['error' => 'Événement complet.'], 400);
        }
    
        // Incrémenter le nombre de participants
        $evenement->setNombreParticipants($evenement->getNombreParticipants() + 1);
    
        $participation = new Participation();
        $participation->setEvenement($evenement);
        $participation->setUtilisateur($utilisateur);
        $participation->setDateParticipation(new \DateTime());
        $participation->setStatut('En attente');
    
        $entityManager->persist($participation);
        $entityManager->flush();
    
        return new JsonResponse(['success' => 'Participation enregistrée !']);
    }
    

    // filepath: src/Controller/EvenementController.php
    #[Route('/api/events', name: 'api_events', methods: ['GET'])]
    public function getEvents(EvenementRepository $evenementRepository): JsonResponse
    {
        $evenements = $evenementRepository->findAll();

        $events = [];
        foreach ($evenements as $evenement) {
            $events[] = [
                'title' => $evenement->getNomEvenement(),
                'start' => $evenement->getDate()->format('Y-m-d'),
                'end' => $evenement->getDate()->format('Y-m-d'),
                'url' => $this->generateUrl('app_evenement_show', ['id' => $evenement->getId()]),
            ];
        }

        return new JsonResponse($events);
    }
    #[Route('/evenement/calendar', name: 'app_evenement_calendar')]
    public function calendar(): Response
    {
        return $this->render('evenement/calendar.html.twig');
    }
}
