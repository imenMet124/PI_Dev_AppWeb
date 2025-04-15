<?php

namespace App\Controller;
use App\Entity\Participation;
use App\Entity\Utilisateur; 

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/evenement/show/{id}', name: 'app_evenement_show')]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_back_evenement', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/back/evenement/new', name: 'app_back_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
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
                        $this->getParameter('images_directory'), // Défini dans services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
    
                $evenement->setImagePath('uploads/images/' . $newFilename);
            }
    
            $entityManager->persist($evenement);
            $entityManager->flush();
    
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
    public function getEvenements(EvenementRepository $evenementRepository): Response
    {
        return $this->render('back/evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/evenement/{id}/participer', name: 'app_evenement_participer')]
    public function participer(Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        // Simuler un utilisateur connecté (à remplacer plus tard par getUser())
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find(1); // ID 1 en dur pour test
    
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
    
        // Incrémenter le nombre de participants
        $evenement->setNombreParticipants($evenement->getNombreParticipants() + 1);
    
        // Créer une nouvelle participation
        $participation = new Participation();
        $participation->setEvenement($evenement);
        $participation->setUtilisateur($utilisateur);
        $participation->setDateParticipation(new \DateTime());
        $participation->setStatut('En attente');
    
        // Enregistrer dans la base de données
        $entityManager->persist($participation);
        $entityManager->flush();
    
        $this->addFlash('success', 'Participation enregistrée avec succès !');
    
        return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
    }
    

}
