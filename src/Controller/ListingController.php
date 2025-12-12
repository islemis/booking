<?php

namespace App\Controller;

use App\Entity\Listing;
use App\Form\ListingType;
use App\Repository\ListingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/listings')]
final class ListingController extends AbstractController{
    #[Route(name: 'app_listing_index', methods: ['GET'])]
    public function index(ListingRepository $listingRepository): Response
    {
        return $this->render('listing/index.html.twig', [
            'listings' => $listingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_listing_new', methods: ['GET', 'POST'])]
    public function newListing(Request $request, EntityManagerInterface $entityManager): Response
    {
        $listing = new Listing();
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listing->setDateOfPublish(new \DateTime());
            
            // Set the owner to the current user
            $listing->setOwner($this->getUser());

            // Ensure the appartment is set
            if (!$listing->getAppartment()) {
                $this->addFlash('error', 'Please select an appartment.');
                return $this->render('listing/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $entityManager->persist($listing);
            $entityManager->flush();

            return $this->redirectToRoute('app_listing_index');
        }

        return $this->render('listing/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    
    #[Route('/{id}', name: 'app_listing_show', methods: ['GET'])]
    public function show(Listing $listing): Response
    {
        return $this->render('listing/show.html.twig', [
            'listing' => $listing,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_listing_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Listing $listing, EntityManagerInterface $entityManager): Response
    {
        // Check if user is admin or owner of the listing
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') && $listing->getOwner() !== $user) {
            throw $this->createAccessDeniedException('You cannot edit this listing.');
        }

        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_listing_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('listing/edit.html.twig', [
            'listing' => $listing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_listing_delete', methods: ['POST'])]
    public function delete(Request $request, Listing $listing, EntityManagerInterface $entityManager): Response
    {
        // Check if user is admin or owner of the listing
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') && $listing->getOwner() !== $user) {
            throw $this->createAccessDeniedException('You cannot delete this listing.');
        }

        if ($this->isCsrfTokenValid('delete'.$listing->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($listing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_listing_index', [], Response::HTTP_SEE_OTHER);
    }
}
