<?php

namespace App\Controller;

use App\Entity\Option;
use App\Entity\Question;
use App\Form\OptionType;
use App\Repository\OptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(['/back/option', '/back/option/'])]
class OptionController extends AbstractController
{
    #[Route('/', name: 'app_option_index', methods: ['GET'])]
    public function index(OptionRepository $optionRepository): Response
    {
        return $this->render('back/option/index.html.twig', [
            'options' => $optionRepository->findAll(),
        ]);
    }

    #[Route('/question/{question_id}', name: 'app_option_by_question', methods: ['GET'])]
    public function optionsByQuestion(int $question_id, OptionRepository $optionRepository, EntityManagerInterface $entityManager): Response
    {
        $question = $entityManager->getRepository(Question::class)->find($question_id);

        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        return $this->render('back/option/index.html.twig', [
            'options' => $optionRepository->findByQuestion($question),
            'question' => $question
        ]);
    }

    #[Route('/new', name: 'app_option_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $option = new Option();
        $question = null;

        // Pre-select a question if question_id is provided in the query
        $questionId = $request->query->get('question_id');
        if ($questionId) {
            $question = $entityManager->getRepository(Question::class)->find($questionId);
            if ($question) {
                $option->setQuestion($question);
            }
        }

        // Create the form with the standalone_option option set to true
        // to include the question field
        $form = $this->createForm(OptionType::class, $option, [
            'standalone_option' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If we had a pre-selected question, make sure it's still set
            if ($question) {
                $option->setQuestion($question);
            }

            $entityManager->persist($option);
            $entityManager->flush();

            $this->addFlash('success', 'L\'option a été créée avec succès');

            // Redirect to the question's options if a question is set
            if ($option->getQuestion()) {
                return $this->redirectToRoute('app_option_by_question', ['question_id' => $option->getQuestion()->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_option_index', [], Response::HTTP_SEE_OTHER);
        }

        // Pass the question to the template if we have one
        $templateParams = [
            'option' => $option,
            'form' => $form,
        ];

        if ($question) {
            $templateParams['question'] = $question;
        }

        return $this->render('back/option/new.html.twig', $templateParams);
    }

    #[Route('/new/question/{question_id}', name: 'app_option_new_for_question', methods: ['GET', 'POST'])]
    public function newForQuestion(int $question_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $question = $entityManager->getRepository(Question::class)->find($question_id);

        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        $option = new Option();
        $option->setQuestion($question);

        // Create the form without the question field since we already have a question
        $form = $this->createForm(OptionType::class, $option, [
            'standalone_option' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Make sure the question is still set (it might have been modified in the form)
            $option->setQuestion($question);

            $entityManager->persist($option);
            $entityManager->flush();

            $this->addFlash('success', 'L\'option a été créée avec succès');
            return $this->redirectToRoute('app_option_by_question', ['question_id' => $question_id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/option/new.html.twig', [
            'option' => $option,
            'form' => $form,
            'question' => $question
        ]);
    }

    #[Route('/{id}', name: 'app_option_show', methods: ['GET'])]
    public function show(Option $option): Response
    {
        return $this->render('back/option/show.html.twig', [
            'option' => $option,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_option_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Option $option, EntityManagerInterface $entityManager): Response
    {
        // Store the original question for later use
        $originalQuestion = $option->getQuestion();

        // Create the form without the question field
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Make sure the question relationship is preserved
            if ($originalQuestion) {
                $option->setQuestion($originalQuestion);
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'option a été modifiée avec succès');

            // Redirect to the question's options list if available
            if ($option->getQuestion()) {
                return $this->redirectToRoute('app_option_by_question', ['question_id' => $option->getQuestion()->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_option_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/option/edit.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_option_delete', methods: ['POST'])]
    public function delete(Request $request, Option $option, EntityManagerInterface $entityManager): Response
    {
        $questionId = null;
        if ($option->getQuestion()) {
            $questionId = $option->getQuestion()->getId();
        }

        try {
            if ($this->isCsrfTokenValid('delete' . $option->getId(), $request->getPayload()->getString('_token'))) {
                // Soft delete - set the deletedAt timestamp instead of removing
                $option->delete();
                $entityManager->flush();
                $this->addFlash('success', 'L\'option a été supprimée avec succès');
            } else {
                $this->addFlash('danger', 'Erreur de sécurité lors de la suppression de l\'option');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la suppression de l\'option: ' . $e->getMessage());
        }

        // Redirect to the question's options list if available
        if ($questionId) {
            return $this->redirectToRoute('app_option_by_question', ['question_id' => $questionId], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_option_index', [], Response::HTTP_SEE_OTHER);
    }
}
