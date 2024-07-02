<?php

namespace App\Controller;

use App\Entity\Association;
use App\Form\AdminUserType;
use App\Repository\AssociationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\ConfigurationFormType;
use App\Form\ConfigurationWebsiteType;
use App\Form\LogoType;
use App\Form\ColorType;
use App\Repository\EvenementRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Evenement;
use App\Form\EvenementType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

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
            'galerie' => $galerie,
            'name' => $name
        ]);
    }

    public function header(string $name, AssociationRepository $associationRepository): Response
    {
        $association = $associationRepository->findOneBy(['nom' => $name]);
        
        $paiement = $association->isPaiementCheck();
        $message = $association->isMessageCheck();
        $evenement = $association->isEvenementCheck();
        $galerie = $association->isGalerieCheck();

        return $this->render('association/header.html.twig', [
            'associations' => $association,
            'paiement' => $paiement,
            'message' => $message,
            'evenement' => $evenement,
            'galerie' => $galerie,
        ]);
    }

    #[Route('/{name}/admin', name: 'admin', methods: ['GET'])]
    public function admin(string $name, AssociationRepository $associationRepository, UserRepository $userRepository): Response
    {

        $asso = $associationRepository->findOneBy(['nom' => $name]);
        $users = $userRepository->findBy(['asso' => $asso->getId()]);

        //afficher les utilisateurs de l'association

        return $this->render('association/admin.html.twig', [
            'name' => $name,
            'users' => $users
        ]);
    }

    #[Route('/{name}/admin/newUser', name: 'admin_newUser', methods: ['GET'])]
    public function newUser (string $name, 
        UserRepository $userRepository, 
        Request $request, 
        EntityManagerInterface $entityManager,
        AssociationRepository $associationRepository,
        UserPasswordHasherInterface $userPasswordHasherInterface        
    ): Response
    {
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
        $asso = $associationRepository->findOneBy(['nom' => $name]);

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
            $user->setAsso($asso->getId());

            $entityManager->persist($user);
            $entityManager->flush();

            $userAsso = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            
            $asso->addListeUsers($userAsso->getId());

            $entityManager->persist($asso);
            $entityManager->flush();

            return $this->redirectToRoute('association_admin', ['name' => $name]);
        }

        return $this->render('association/new.html.twig', [
            'name' => $name,
            'form' => $form,
        ]);
    }

    #[Route('/{name}/admin/editUser/{id}', name: 'admin_editUser', methods: ['GET'])]
    public function editUser (string $name, 
        UserRepository $userRepository, 
        Request $request, 
        EntityManagerInterface $entityManager,
        AssociationRepository $associationRepository,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        int $id
    ): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
        $asso = $associationRepository->findOneBy(['nom' => $name]);

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
            $user->setAsso($asso->getId());

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('association_admin', ['name' => $name]);
        }

        return $this->render('association/edit.html.twig', [
            'name' => $name,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{name}/admin/deleteUser/{id}', name: 'admin_deleteUser', methods: ['GET'])]
    public function deleteUser (string $name, 
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        AssociationRepository $associationRepository,
        int $id
    ): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        $asso = $associationRepository->findOneBy(['nom' => $name]);

        $asso->removeListeUsers($user->getId());

        //supprimer l'id de l'utilisateur de la liste des utilisateurs de l'association


        $entityManager->persist($asso);
        $entityManager->flush();

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('association_admin', ['name' => $name]);
    }

    #[Route(path: '/{name}/personalisation/1', name: 'personalisation_1')]
    public function personalisation1(string $name, AssociationRepository $associationRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $asso = $associationRepository->findOneBy(['nom' => $name]);

        $form = $this->createForm(ConfigurationWebsiteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $asso->setPaiementCheck($form->get('paiement')->getData());
            $asso->setMessageCheck($form->get('message')->getData());
            $asso->setEvenementCheck($form->get('evenement')->getData());
            $asso->setGalerieCheck($form->get('gallery')->getData());

            $entityManager->persist($asso);
            $entityManager->flush();

            return $this->redirectToRoute('association_home', ['name' => $name]);
        }

        return $this->render('association/personalisation1.html.twig',[
            'form' => $form,
            'name' => $name,
        ]);
    }

    #[Route(path: '/{name}/personalisation/2', name: 'personalisation_2')]
    public function personalisation2(string $name, AssociationRepository $associationRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $asso = $associationRepository->findOneBy(['nom' => $name]);
        $assoName = $asso->getNom();
        $form = $this->createForm(LogoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form->get('logo')->getData();

            if($logo){
                $targetDirectory = $this->getParameter('logo_directory');
                $newFilename = 'logo.' . $logo->guessExtension();
            
                // Chemin complet du fichier cible
                $fullPath = $targetDirectory . '/' . $newFilename;
            
                // Vérifie si un fichier avec le même nom existe déjà et le supprime si c'est le cas
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            
                try {
                    $logo->move(
                        $targetDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }

            return $this->redirectToRoute('association_home', ['name' => $name]);
        }    

        return $this->render('association/personalisation2.html.twig',[
            'form' => $form,
            'name' => $assoName,
        ]);
    }

    #[Route(path: '/{name}/personalisation/3', name: 'personalisation_3')]
    public function personalisation3(string $name, AssociationRepository $associationRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $asso = $associationRepository->findOneBy(['nom' => $name]);
        $assoName = $asso->getNom();
        
        $form = $this->createForm(ColorType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($form->get('couleurPrimaire')->getData() == null){
                $asso->setCouleurPrimaire('#02232C');
            }
            else{
                $asso->setCouleurPrimaire($form->get('couleurPrimaire')->getData());
            }

            if($form->get('couleurSecondaire')->getData() == null){
                $asso->setCouleurSecondaire('#22A5DD');
            }
            else{
                $asso->setCouleurSecondaire($form->get('couleurSecondaire')->getData());
            }

            if($form->get('couleurTertiaire')->getData() == null){
                $asso->setCouleurTertiaire('#E55924');
            }
            else{
                $asso->setCouleurTertiaire($form->get('couleurTertiaire')->getData());
            }

            $entityManager->persist($asso);
            $entityManager->flush();

            return $this->redirectToRoute('association_home', ['name' => $name]);
        }

        $asso->setCouleurPrimaire('#02232C');
        $asso->setCouleurSecondaire('#22A5DD');
        $asso->setCouleurTertiaire('#E55924');

        $entityManager->persist($asso);
        $entityManager->flush();

    
        return $this->render('association/personalisation3.html.twig',[
            'colorForm' => $form,
            'name' => $assoName,
        ]);
    }

    #[Route(path: '/{name}/evenements/new', name: 'evenements_new')]
    public function newEvenement (string $name, 
        Request $request, 
        EntityManagerInterface $entityManager,
    ): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form->get('image')->getData();
            if($img){
                $targetDirectory = $this->getParameter('evenements_directory');
                $newFilename =  $form->get('nom')->getData().'.'.$img->guessExtension();
            
                // Chemin complet du fichier cible
                $fullPath = $targetDirectory . '/' . $newFilename;
            
                // Vérifie si un fichier avec le même nom existe déjà et le supprime si c'est le cas
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            
                try {
                    $img->move(
                        $targetDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }

            $evenement->setNom($form->get('nom')->getData());
            $evenement->setDescription($form->get('description')->getData());
            $evenement->setLieu($form->get('lieu')->getData());
            $evenement->setDateDebut($form->get('dateDebut')->getData());
            $evenement->setDateFin($form->get('dateFin')->getData());
            $evenement->setProprietaire($this->getUser());

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('association_evenements', ['name' => $name]);
        }

        return $this->render('association/newEvenement.html.twig', [
            'form' => $form,
            'name' => $name
        ]);
    }

    #[Route(path: '/{name}/evenements/', name: 'evenements')]
    public function evenements (string $name, 
        EvenementRepository $evenementRepository,
        AssociationRepository $associationRepository
    ): Response
    {
        $evenement = $evenementRepository->findAll();
        $association = $associationRepository->findOneBy(['nom' => $name]);

        $paiement = $association->isPaiementCheck();
        $message = $association->isMessageCheck();
        $events = $association->isEvenementCheck();
        $galerie = $association->isGalerieCheck();

        foreach($evenement as $event){
            if($event->getProprietaire()->getAsso()->getId() == $this->getUser()->getAsso()->getId()){
                $eventData = [
                    'event' => $event,
                    'participate' => in_array($this->getUser()->getId(), $event->getListeUsers())
                ];                
                $evenements[] = $eventData;
            }
        }

        return $this->render('association/evenements.html.twig', [
            'evenements' => $evenements,
            'name' => $name,
            'paiement' => $paiement,
            'message' => $message,
            'evenement' => $events,
            'galerie' => $galerie
        ]);

    }

    #[Route(path: '/{name}/evenements/edit/{id}', name: 'evenements_edit')]
    public function editEvenement (string $name, 
        Request $request, 
        EntityManagerInterface $entityManager,
        EvenementRepository $evenementRepository,
        int $id
    ): Response
    {
        $evenement = $evenementRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form->get('image')->getData();
            if($img){
                $targetDirectory = $this->getParameter('evenements_directory');
                $newFilename =  $form->get('nom')->getData().'.'.$img->guessExtension();
            
                // Chemin complet du fichier cible
                $fullPath = $targetDirectory . '/' . $newFilename;
            
                // Vérifie si un fichier avec le même nom existe déjà et le supprime si c'est le cas
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            
                try {
                    $img->move(
                        $targetDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }

            $evenement->setNom($form->get('nom')->getData());
            $evenement->setDescription($form->get('description')->getData());
            $evenement->setLieu($form->get('lieu')->getData());
            $evenement->setDateDebut($form->get('dateDebut')->getData());
            $evenement->setDateFin($form->get('dateFin')->getData());
            $evenement->setProprietaire($this->getUser());

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('association_home', ['name' => $name]);
        }

        return $this->render('association/editEvenement.html.twig', [
            'form' => $form,
            'name' => $name,
            'evenement' => $evenement
        ]);
    }

    #[Route(path: '/{name}/evenements/delete/{id}', name: 'evenements_delete')]
    public function deleteEvenement (string $name, 
        EntityManagerInterface $entityManager,
        EvenementRepository $evenementRepository,
        int $id
    ): Response
    {
        $evenement = $evenementRepository->findOneBy(['id' => $id]);

        $entityManager->remove($evenement);
        $entityManager->flush();

        return $this->redirectToRoute('association_home', ['name' => $name]);
    }

    #[Route(path: '/{name}/evenements/add/{id}', name: 'evenements_add')]
    public function addEvenement (string $name, 
        EntityManagerInterface $entityManager,
        EvenementRepository $evenementRepository,
        int $id
    ): Response
    {
        //si l'utilisateur est déjà inscrit à l'événement, le retirer de la liste des participants
        $evenement = $evenementRepository->findOneBy(['id' => $id]);

        $participate = in_array($this->getUser()->getId(), $evenement->getListeUsers());

        dump($this->getUser());

        if ($participate){
            $evenement->removeUser($this->getUser()->getId());
            $this->getUser()->removeEvenement($evenement->getId());

            $entityManager->persist($evenement);
            $entityManager->persist($this->getUser());
            $entityManager->flush();

        }
        else{
            $evenement->addUser($this->getUser()->getId());
            $this->getUser()->addEvenement($evenement->getId());

            $entityManager->persist($evenement);
            $entityManager->persist($this->getUser());
            $entityManager->flush();
        }

        return $this->redirectToRoute('association_evenements', [
            'name' => $name,
            'participate' => $participate
        ]);
    }

    #[Route(path: '/{name}/profil', name: 'profil')]
    public function profil (string $name, EvenementRepository $evenementRepository): Response
    {
        
        $user = $this->getUser();
        $evenement = $evenementRepository->findAll();

        foreach($evenement as $event){
           if(in_array($user->getId(), $event->getListeUsers())){
               $evenements[] = $event;
           }
        }

        return $this->render('association/profil.html.twig', [
            'name' => $name,
            'user' => $user,
            'evenements' => $evenements
        ]);
    }

    #[Route(path: '/{name}/calendar', name: 'calendar')]
    public function calendar(string $name): Response
    {
        return $this->render('association/calendar.html.twig', [
            'name' => $name
        ]);
    }

}
