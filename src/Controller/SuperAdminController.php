<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\SuperAdminUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Association;
use App\Form\AssociationType;
use App\Repository\AssociationRepository;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route(path: '/superadmin', name: 'superadmin_')]

class SuperAdminController extends AbstractController
{
    #[Route(path: '/', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        dump($this->getUser());
        if ($this->getUser() !== null && $this->getUser()->getRoles() === ['ROLE_SUPERADMIN']) {
            return $this->redirectToRoute('superadmin_dashboard');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        dump($error);
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        dump($lastUsername);

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/dashboard', name: 'dashboard')]
    public function dashboard(AssociationRepository $associationRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $proprietaire = $userRepository->findByRole('ROLE_PROPRIETAIRE');
        
        $associations = $associationRepository->findAll();
        $associationsWithUserCount = [];
    
        foreach ($associations as $association) {
            // Supposant que l'entité Association a une méthode countUsers()
            $userCount = $association->countListUsers();
            // Ajouter le nombre d'utilisateurs à un tableau ou à l'objet association
            $associationsWithUserCount[] = [
                'association' => $association,
                'userCount' => $userCount,
            ];
        }
        
        return $this->render('security/dashboard.html.twig', [
            'assos' => $associationsWithUserCount,
            'user' => $user,
            'proprietaire' => $proprietaire,
        ]);
    }

    #[Route(path: '/new_asso', name: 'new_asso', methods: ['GET', 'POST'])]
    public function newAsso(Request $request, EntityManagerInterface $entityManager): Response
    {
       $asso = new Association();
        $form = $this->createForm(AssociationType::class, $asso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $asso->setCle($form->get('cle')->getData());
            $asso->setNom($form->get('nom')->getData());
            $asso->setSiret($form->get('siret')->getData());
            $asso->setSlogan($form->get('slogan')->getData());

            $asso->setEvenementCheck(false);
            $asso->setPaiementCheck(false);
            $asso->setGalerieCheck(false);
            $asso->setMessageCheck(false);
            $asso->setActivated(false);

            $entityManager->persist($asso);
            $entityManager->flush();

            return $this->redirectToRoute('superadmin_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security/new_asso.html.twig', [
            'asso' => $asso,
            'form' => $form,
        ]);
    }

    #[Route(path: '/new_user', name: 'new_user', methods: ['GET', 'POST'])]
    public function newUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasherInterface, AssociationRepository $associationRepository, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(SuperAdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setEmail($form->get('email')->getData());
            $user->setRoles($form->get('roles')->getData());
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setNom($form->get('nom')->getData());
            $user->setPrenom($form->get('prenom')->getData());
            $user->setAsso($form->get('asso')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            $userAsso = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            
            $asso = $associationRepository->findOneBy(['id' => $form->get('asso')->getData()]);
            $asso->addListeUsers($userAsso->getId());

            $entityManager->persist($asso);
            $entityManager->flush();

            return $this->redirectToRoute('superadmin_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security/new_user.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
