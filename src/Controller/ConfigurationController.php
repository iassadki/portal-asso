<?php
namespace App\Controller;

use App\Form\ColorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ConfigurationFormType;
use App\Form\ConfigurationWebsiteType;
use App\Repository\AssociationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\LogoType;
use Doctrine\Common\Lexer\Token;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


#[Route(path: '/configuration', name: 'configuration_')]
class ConfigurationController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route(path: '/', name: 'login')]
    public function index(
        UserRepository $userRepository, 
        AssociationRepository $associationRepository,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        EntityManagerInterface $entityManager, 
    ): Response
    {
        $form = $this->createForm(ConfigurationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->get('user')->getData();
            $user = $userRepository->findOneBy(['email' => $userData->getEmail()]);
            if ($user && $userPasswordHasherInterface->isPasswordValid($user, $form->get('user')->get('oldPassword')->getData())) {
                $cle = $associationRepository->findOneBy(['cle' => $form->get('cle')->getData()]); 
                if ($cle) {
                    $newHashedPassword = $userPasswordHasherInterface->hashPassword($user, $form->get('user')->get('newPassword')->getData());
                    if ($form->get('user')->get('newPassword')->getData() === $form->get('user')->get('confirmPassword')->getData()) {
                        $user->setPassword($newHashedPassword);
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
                        $this->tokenStorage->setToken($token);
                        
                        if($cle->isActivated() == true){
                            return $this->redirectToRoute('association_home', ['name' => $cle->getNom()]);
                        }
                        
                        $assoName = $cle->getNom();
                        return $this->redirectToRoute('configuration_personalisation_1', ['name' => $assoName]);
                    } else {
                        $this->addFlash('danger', 'Les mots de passe ne correspondent pas');
                    }
                }
                } else {
                    $this->addFlash('danger', 'Clé invalide');
                }
            }
            else {
                $this->addFlash('danger', 'Utilisateur introuvable');
            }
        

        return $this->render('configuration/index.html.twig', [
            'form' => $form,
            'asso' => $associationRepository->findBy(['cle' => $form->get('cle')->getData()]),
        ]);
    }

    #[Route(path: '/{name}/1', name: 'personalisation_1')]
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

            return $this->redirectToRoute('configuration_personalisation_2', ['name' => $name]);
        }

        return $this->render('configuration/personalisation1.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route(path: '/{name}/2', name: 'personalisation_2')]
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

            return $this->redirectToRoute('configuration_personalisation_3', ['name' => $name]);
        }    

        return $this->render('configuration/personalisation2.html.twig',[
            'form' => $form,
            'name' => $assoName,
        ]);
    }

    #[Route(path: '/{name}/3', name: 'personalisation_3')]
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

            return $this->redirectToRoute('configuration_personalisation_resume', ['name' => $name]);
        }

        $asso->setCouleurPrimaire('#02232C');
        $asso->setCouleurSecondaire('#22A5DD');
        $asso->setCouleurTertiaire('#E55924');

        $entityManager->persist($asso);
        $entityManager->flush();

    
        return $this->render('configuration/personalisation3.html.twig',[
            'colorForm' => $form,
            'name' => $assoName,
        ]);
    }

    #[Route(path: '/{name}/resume', name: 'personalisation_resume')]
    public function resume(string $name, AssociationRepository $associationRepository, EntityManagerInterface $entityManager): Response
    {
        $asso = $associationRepository->findOneBy(['nom' => $name]);
        $assoName = $asso->getNom();

        $assoPaiement = $asso->isPaiementCheck();
        $assoMessage = $asso->isMessageCheck();
        $assoEvenement = $asso->isEvenementCheck();
        $assoGalerie = $asso->isGalerieCheck();
        $assoCouleurPrimaire = $asso->getCouleurPrimaire();
        $assoCouleurSecondaire = $asso->getCouleurSecondaire();
        $assoCouleurTertiaire = $asso->getCouleurTertiaire();

        $assoActivate = $asso->setActivated(true);
        $entityManager->persist($assoActivate);
        $entityManager->flush();

        return $this->render('configuration/resume.html.twig',[
            'name' => $assoName,
            'paiement' => $assoPaiement,
            'message' => $assoMessage,
            'evenement' => $assoEvenement,
            'galerie' => $assoGalerie,
            'couleurPrimaire' => $assoCouleurPrimaire,
            'couleurSecondaire' => $assoCouleurSecondaire,
            'couleurTertiaire' => $assoCouleurTertiaire,
        ]);
    }


}
