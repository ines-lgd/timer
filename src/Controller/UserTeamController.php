<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Form\UserTeamType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserTeamController extends AbstractController
{

    private $teamRepository;

    private $userRepository;

    private $entityManager;

    public function __construct(TeamRepository $teamRepository, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("team/add_user/{id}", name="add_user_team")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function add(Request $request, int $id)
    {
        // Get Team and Users
        $team = $this->teamRepository->find($id);
        $users = $this->getUsers($team);

        // Get form data
        $form = $this->createForm(UserTeamType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Get User form
            $pseudo = $form->get("user")->getData();
            $user = $this->userRepository->findOneBy(['pseudo' => $pseudo]);

            if (empty($user)) {

                // Set message error if user not found
                $form->get('user')->addError(new FormError('Ce pseudo d’utilisateur n’existe pas.'));
            }
            else {

                if (in_array($user, $team->getUsers()->toArray())) {

                    // Set message error if user is already in team
                    $form->get('user')->addError(new FormError('Cet utilisateur fait déjà partie de ce groupe.'));
                }
                else {

                    // Add User in Team and update Team in database
                    $team->addUser($user);
                    $this->entityManager->persist($team);
                    $this->entityManager->flush();

                    return $this->redirectToRoute('add_user_team', [
                        'id' => $team->getId()
                    ]);
                }
            }
        }

        return $this->render('team/user.html.twig', [
            'team' => $team,
            'users' => $users,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/team/delete_user/{team_id}/{user_id}", name="delete_user_team")
     * @param int $team_id
     * @param int $user_id
     * @return RedirectResponse
     */
    public function remove(int $team_id, int $user_id)
    {
        // Get Team, User and User logged
        $team = $this->teamRepository->find($team_id);
        $user = $this->userRepository->find($user_id);
        $user_logged = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

        // Check if User logged is Creator and if User removed is Creator
        if (
            $team->getCreatedBy()->getId() !== $user_logged->getId() ||
            $team->getCreatedBy()->getId() !== $user->getId()
        ) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer d’utilisateur pour ce groupe.');

            return $this->redirectToRoute('show_team', [
                'id' => $team->getId()
            ]);
        }

        // Remove User in Team and Update Team in database
        $team->removeUser($user);
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification', $user->getName() .' a été supprimer du groupe.');

        return $this->redirectToRoute('show_team', [
            'id' => $team_id
        ]);
    }

    /**
     * @param Team $team
     * @return User[]
     */
    public function getUsers(Team $team) {

        // Get all Users
        $users = $this->userRepository->findAll();

        // Filtre User list
        foreach ($users as $key => $user) {

            // Remove User already in Team
            if (in_array($user, $team->getUsers()->toArray())) {
                unset($users[$key]);
            }
        }

        return $users;
    }
}
