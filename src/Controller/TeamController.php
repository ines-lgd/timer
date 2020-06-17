<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Form\UpdateTeamType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
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
     * @Route("/team", name="list_teams")
     * @return Response
     */
    public function list()
    {
        // Get User logged
        $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

        return $this->render('team/index.html.twig', [
            'teams' => $user->getTeams(),
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/team/add", name="add_team")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Create Team and get form data
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Add Creator Member
            $creator = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);
            $team->setCreatedBy($creator);
            $team->addUser($creator);

            // Add Team in database
            $this->entityManager->persist($team);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'Le groupe a bien été créer.');

            // Get Team created
            $name = $form->get("name")->getData();
            $new_team = $this->teamRepository->findOneBy(['name' => $name]);

            return $this->redirectToRoute('add_user_team', [
                'id' => $new_team->getId()
            ]);
        }

        return $this->render('team/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/team/update/{id}", name="update_team")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        // Get Team and User logged
        $team = $this->teamRepository->find($id);
        $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

        // Check if User is Creator Team
        if ($team->getCreatedBy()->getId() !== $user->getId()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas modifier ce groupe.');

            return $this->redirectToRoute('show_team', ['id' => $team->getId()]);
        }

        // Get form data
        $form = $this->createForm(UpdateTeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Update Team in database
            $this->entityManager->persist($team);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'Le groupe a bien été modifié.');

            return $this->redirectToRoute('show_team',  [
                'id' => $id
            ]);
        }

        return $this->render('team/form.html.twig', [
            'form' => $form->createView(),
            'team' => $team
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/team/remove/{id}", name="remove_team")
     * @param int $id
     * @return RedirectResponse
     */
    public function remove(int $id)
    {
        // Get Team and Creator
        $team = $this->teamRepository->find($id);

        // Check if User is Creator Team
        if ($team->getCreatedBy()->getUsername() !== $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer ce groupe.');

            return $this->redirectToRoute('list_teams');
        }

        // Remove User in database
        $this->entityManager->remove($team);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification', 'Le groupe a bien été supprimé.');

        return $this->redirectToRoute('list_teams');
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/team/{id}", name="show_team")
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        // Get Team
        $team = $this->teamRepository->find($id);

        return $this->render('team/view.html.twig', [
            'team' => $team
        ]);
    }
}
