<?php

namespace App\Controller;

use App\Entity\Appartment;
use App\Form\AppartmentType;
use App\Repository\AppartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/appartment')]
final class AppartmentController extends AbstractController{


    #[Route('/new', name: 'app_appartment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Only ROLE_OWNER and ROLE_ADMIN can create apartments
        $this->denyAccessUnlessGranted('ROLE_OWNER');

        $appartment = new Appartment();
        $form = $this->createForm(AppartmentType::class, $appartment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the owner to the current user
            $appartment->setOwner($this->getUser());
            $entityManager->persist($appartment);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_listing_new', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('appartment/new.html.twig', [
            'appartment' => $appartment,
            'form' => $form,
        ]);
    }

    #[Route(name: 'app_appartment_index', methods: ['GET'])]
    public function index(AppartmentRepository $appartmentRepository): Response
    {
        // All users can see all apartments
        $appartments = $appartmentRepository->findAll();

        return $this->render('appartment/index.html.twig', [
            'appartments' => $appartments,
        ]);
    }

    #[Route('/{id}', name: 'app_appartment_show', methods: ['GET'])]
    public function show(Appartment $appartment): Response
    {
        return $this->render('appartment/show.html.twig', [
            'appartment' => $appartment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appartment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Appartment $appartment, EntityManagerInterface $entityManager): Response
    {
        // Check if user is admin or owner of the apartment
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') && $appartment->getOwner() !== $user) {
            throw $this->createAccessDeniedException('You cannot edit this apartment.');
        }

        $form = $this->createForm(AppartmentType::class, $appartment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_appartment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appartment/edit.html.twig', [
            'appartment' => $appartment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appartment_delete', methods: ['POST'])]
    public function delete(Request $request, Appartment $appartment, EntityManagerInterface $entityManager): Response
    {
        // Check if user is admin or owner of the apartment
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') && $appartment->getOwner() !== $user) {
            throw $this->createAccessDeniedException('You cannot delete this apartment.');
        }

        if ($this->isCsrfTokenValid('delete'.$appartment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($appartment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_appartment_index', [], Response::HTTP_SEE_OTHER);
    }
}
