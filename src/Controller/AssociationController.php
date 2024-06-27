<?php

namespace App\Controller;

use App\Entity\Association;
use App\Form\AssociationType;
use App\Repository\AssociationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/portalAsso', name: 'association_')]
class AssociationController extends AbstractController
{
    #[Route('/{name}', name: 'home', methods: ['GET'])]
    public function index(string $name, AssociationRepository $associationRepository): Response
    {
        $association = $associationRepository->findOneBy(['nom' => $name]);
        
        $paiement = $association->isPaiementCheck();
        $message = $association->isMessageCheck();
        $evenement = $association->isEvenementCheck();
        $galerie = $association->isGalerieCheck();

        return $this->render('association/index.html.twig', [
            'associations' => $association,
            'paiement' => $paiement,
            'message' => $message,
            'evenement' => $evenement,
            'galerie' => $galerie
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $association = new Association();
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($association);
            $entityManager->flush();

            return $this->redirectToRoute('association_home', ['name' => $association->getNom()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('association/new.html.twig', [
            'association' => $association,
            'form' => $form,
        ]);
    }

    #[Route('/{name}/{id}', name: 'show', methods: ['GET'])]
    public function show(string $name, Association $association): Response
    {
        return $this->render('association/show.html.twig', [
            'association' => $association,
        ]);
    }

    #[Route('/{name}/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $name, Association $association, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('association_home', ['name' => $association->getNom()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('association/edit.html.twig', [
            'association' => $association,
            'form' => $form,
        ]);
    }

    #[Route('/{name}/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, string $name, Association $association, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$association->getId(), $request->request->get('_token'))) {
            $entityManager->remove($association);
            $entityManager->flush();
        }

        return $this->redirectToRoute('association_home', ['name' => $association->getNom()], Response::HTTP_SEE_OTHER);
    }
}
