<?php

namespace App\Controller;

use App\Form\UserTeamType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
     * @Route("team/add_user/{id}", name="add_user_team")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function addUser(Request $request, int $id)
    {
        // Get Team
        $team = $this->teamRepository->find($id);
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

                    // Set message error if user is all ready in team
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
            'form' => $form->createView()
        ]);
    }
}
