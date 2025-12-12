<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Form\NewBookingType;

use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/booking')]
final class BookingController extends AbstractController{
    #[Route(name: 'app_booking_index', methods: ['GET'])]
    public function index(BookingRepository $bookingRepository): Response
    {
        // Only logged-in users can see their bookings
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        // Get only the current user's bookings
        $user = $this->getUser();
        $bookings = $bookingRepository->findBy(['userId' => $user]);
        
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    #[Route('/owner/bookings', name: 'app_owner_bookings', methods: ['GET'])]
    public function ownerBookings(BookingRepository $bookingRepository): Response
    {
        // Only owners and admins can view owner bookings
        $this->denyAccessUnlessGranted('ROLE_OWNER');
        
        $user = $this->getUser();
        $bookings = $bookingRepository->findBookingsByOwner($user);
        
        return $this->render('booking/owner-bookings.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    #[Route('/new', name: 'app_booking_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
    
        $booking = new Booking();
    
        // Set the current user
        $booking->setUserId($this->getUser());
    
        // Get the listing ID from the URL parameter and set it
        $listingId = $request->query->get('id');
        if ($listingId) {
            $listing = $entityManager->getRepository(\App\Entity\Listing::class)->find($listingId);
            if ($listing) {
                $booking->setListingId($listing);
            } else {
                throw $this->createNotFoundException('Listing not found.');
            }
        } else {
            throw $this->createNotFoundException('Listing ID is missing.');
        }
    
        // Create the form
        $form = $this->createForm(NewBookingType::class, $booking);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Set default status to 'pending' only if not already set
            if (!$booking->getStatus()) {
                $booking->setStatus('pending');
            }
    
            $entityManager->persist($booking);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_booking_show', [
                'id' => $booking->getId()
            ], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_booking_show', methods: ['GET'])]
    public function show(Booking $booking): Response
    {
        // Only the booking owner or admins can view the booking
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $booking->getUserId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez voir que vos propres réservations');
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_booking_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Only the booking owner or admins can edit the booking
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $booking->getUserId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez éditer que vos propres réservations');
        }
        $form = $this->createForm(BookingType::class, $booking, ['is_new' => true]);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_booking_delete', methods: ['POST'])]
    public function delete(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Only the booking owner or admins can delete the booking
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $booking->getUserId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres réservations');
        }

        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($booking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/owner/{id}/edit', name: 'app_owner_booking_edit', methods: ['GET', 'POST'])]
    public function ownerEdit(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Only the owner of the listing can edit the booking
        $listing = $booking->getListingId();
        if (!$this->isGranted('ROLE_ADMIN') && $listing->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez éditer que les réservations de vos annonces');
        }
    
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Réservation mise à jour avec succès');
            return $this->redirectToRoute('app_owner_bookings');
        }
    
        return $this->render('booking/owner-edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/owner/{id}', name: 'app_owner_booking_delete', methods: ['POST'])]
    public function ownerDelete(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Only the owner of the listing can delete the booking
        $listing = $booking->getListingId();
        if (!$this->isGranted('ROLE_ADMIN') && $listing->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que les réservations de vos annonces');
        }

        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Réservation supprimée avec succès');
        }

        return $this->redirectToRoute('app_owner_bookings', [], Response::HTTP_SEE_OTHER);
    }
}
