<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/users", name="list_users")
     */
    public function all()
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
     * @return RedirectResponse|Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Redirect if an User is log
        if ($this->getUser()) {
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

            return $this->redirectToRoute('list_users');
        }

        // View form User
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
        // Get User and get form data
        $user = $this->userRepository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Verify User
        if ($this->verifyUser($user)) {
            return $this->redirectToRoute('app_home');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            // Update User in database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'La modification a bien été effectué.');

            return $this->redirectToRoute('list_users');
        }

        // View form User
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

        // Verify User
        if ($this->verifyUser($user)) {
            return $this->redirectToRoute('app_home');
        }

        // Remove User in database
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification', 'L’utilisateur a bien été supprimé.');

        return $this->redirectToRoute('list_users');
    }

    /**
     * Verify User
     * @param User $user
     * @return bool
     */
    public function verifyUser(User $user)
    {
        $result = false;

        if (
            $this->getUser()->getUsername() === $user->getUsername() ||
            in_array("ROLE_ADMIN", $this->getUser()->getRoles())
        ) {
            $result = true;
        }

        return $result;
    }
}
