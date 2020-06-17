<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{

    private $userRepository;

    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/users", name="list_users")
     * @return Response
     */
    public function list()
    {
        // Get all users
        $users = $this->userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param UserAuthenticator $authenticator
     * @return RedirectResponse|Response
     */
    public function register
    (
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        UserAuthenticator $authenticator
    )
    {
        // Redirect if an User is logged
        if ($this->getUser()) {

            // Add message flash
            $this->addFlash('warning', 'Vous êtes déjà connecté en tant que '. $this->getUser()->getUsername() .'.');

            return $this->redirectToRoute('app_home');
        }

        // Create User and get form data
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Encode User password
            $passwordEncoder = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordEncoder);

            // Add User in database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'Inscription effectué.');

            // Connect User
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/set_user/{id}", name="set_user")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function update(Request $request, int $id)
    {
        // Get User
        $user = $this->userRepository->find($id);

        // Check if User is User logged
        if ($user->getUsername() !== $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas modifier cet utilisateur.');

            return $this->redirectToRoute('app_home');
        }

        // Get form data
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Update User in database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'La modification a bien été effectué.');

            return $this->redirectToRoute('list_users');
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/remove_user/{id}", name="remove_user")
     * @param int $id
     * @return RedirectResponse
     */
    public function remove(int $id)
    {
        // Get User
        $user = $this->userRepository->find($id);

        // Check if User is User logged
        if ($user->getUsername() !== $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer ce groupe.');

            return $this->redirectToRoute('app_home');
        }

        // Remove User in database
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification', 'L’utilisateur a bien été supprimé.');

        return $this->redirectToRoute('app_logout');
    }
}
