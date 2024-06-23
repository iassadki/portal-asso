<?php

namespace App\Controller;

use Amp\Http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ConfigurationFormType;
use App\Repository\AssociationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route(path: '/configuration', name: 'configuration_')]
class ConfigurationController extends AbstractController
{
    #[Route(path: '/', name: 'login')]
    public function index(
        UserRepository $userRepository, 
        AssociationRepository $associationRepository,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        EntityManagerInterface $entityManager
        ): Response
    {
        $form = $this->createForm(ConfigurationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user && $userPasswordHasherInterface->isPasswordValid($user, $form->get('oldPassword')->getData())) {
                // La vérification de la clé d'association reste inchangée
                $cle = $associationRepository->findOneBy(['cle' => $form->get('cle')->getData()]);
                if ($cle) {
                    $newHashedPassword = $userPasswordHasherInterface->hashPassword($user, $form->get('newPassword')->getData());
                    $user->setPassword($newHashedPassword);
                    $entityManager->persist($user);
                    $entityManager->flush();
                }
            }
        }
        return $this->render('configuration/index.html.twig', [
            'asso' => $associationRepository->findBy(['cle' => $form->get('cle')->getData()]),  
        ]);
    }
}