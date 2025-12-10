<?php

namespace App\Controller;
use App\Form\LoginType;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\UserauthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, UserauthAuthenticator $authenticator): Response
    {
        $user = new User();
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $user->getUsername();
            if (empty($username)) {
                throw new \Exception('Username is empty!');
            }
            
            $password = $user->getPassword();
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);
            
            // Ensure roles is always an array
            $roles = $user->getRoles();
            if (!is_array($roles)) {
                $user->setRoles([$roles]);
            } elseif (empty($roles)) {
                $user->setRoles(['ROLE_USER']);
            }
        
            $entityManager->persist($user);
            $entityManager->flush();
        
            // Automatically log in the user after registration
            return $userAuthenticator->authenticateUser($user, $authenticator, $request);
        }
        
        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    } 

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // Only admins can view all users
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // Only admins or the user themselves can view user details
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez voir que votre propre profil');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Only admins can edit other users
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez éditer que votre propre profil');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Only admins can delete users
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
