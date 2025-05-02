<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => false, // This is a creation form
            'validation_groups' => ['Default', 'registration']
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Manually validate before checking isValid()
            $errors = $validator->validate($user, null, ['Default', 'registration']);
            
            if ($form->isValid() && count($errors) === 0) {
                // Hash the password
                if ($plainPassword = $form->get('password')->getData()) {
                    $user->setPassword(
                        $passwordHasher->hashPassword($user, $plainPassword)
                    );
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'User created successfully!');
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }

            // Add validation errors to flash messages
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/guests', name: 'app_user_guests', methods: ['GET'])]
    public function guests(UserRepository $userRepository): Response
    {
        return $this->render('user/guests.html.twig', [
            'users' => $userRepository->findGuests(),
        ]);
    }

    #[Route('/non-guests', name: 'app_user_nonguests', methods: ['GET'])]
    public function nonGuests(UserRepository $userRepository): Response
    {
        return $this->render('user/nonguests.html.twig', [
            'users' => $userRepository->findNonGuests(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(User $user): Response
    {
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): Response {
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true, // This is an edit form
            'validation_groups' => ['Default'] // No password validation
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $validator->validate($user);
            
            if ($form->isValid() && count($errors) === 0) {
                // Handle password change if provided
                if ($plainPassword = $form->get('password')->getData()) {
                    $user->setPassword(
                        $passwordHasher->hashPassword($user, $plainPassword)
                    );
                }

                $entityManager->flush();
                $this->addFlash('success', 'User updated successfully!');
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User deleted successfully!');
        } else {
            $this->addFlash('error', 'Invalid CSRF token');
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}