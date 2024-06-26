<?php 

namespace App\Controller;

use App\Entity\User;
use App\Repository\AssociationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class HomeController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Vos autres méthodes ici...

    private function getUserAssociationId(User $user): ?int
    {
        // Supposons que l'utilisateur a une relation 'association' qui est l'entité Association
        return $user->getAsso() ? $user->getAsso()->getId() : null;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, AssociationRepository $associationRepository): Response
    {        

        if ($this->getUser() !== null) {
            $associationId = $this->getUserAssociationId($this->getUser());
            if($associationId === null){
                return $this->redirectToRoute('login');
            }
            $associationName = $associationRepository->findOneBy(['id' => $associationId])->getNom();


            return $this->redirectToRoute('association_home', ['name' => $associationName]);
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}