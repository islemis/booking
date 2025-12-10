<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

    #[Route('/review')]
final class ReviewController extends AbstractController{
    #[Route(name: 'app_review_index', methods: ['GET'])]
    public function index(ReviewRepository $reviewRepository, Security $security): Response
    {
        // Get the logged-in user
        $user = $security->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to view your reviews.');
        }
    
        // Fetch reviews related to the logged-in user
        $reviews = $reviewRepository->findBy(['userId' => $user]);
    
        return $this->render('review/index.html.twig', [
            'reviews' => $reviews,
        ]);
    }
    


    #[Route('/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $review = new Review();
    
        // Get the logged-in user
        $user = $security->getUser();
    
        // Automatically set the user for the review
        if ($user) {
            $review->setUserId($user);
        }
    
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();
    
            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_review_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_delete', methods: ['POST'])]
    public function delete(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
    }
}
