<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectTeamType;
use App\Repository\TeamRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectTeamController extends AbstractController
{

    private $teamRepository;

    private $userRepository;

    private $entityManager;

    private $projectRepository;

    public function __construct
    (
        TeamRepository $teamRepository,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("team/add_project/{id}", name="add_project_team")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function addProject(Request $request, int $id)
    {
        // Get Team
        $team = $this->teamRepository->find($id);

        // Get User logged
        $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

        // Create Project with Team
        $project = new Project();
        $project->setTeam($team);

        // Check if User is not in Team
        if (!in_array($user, $team->getUsers()->toArray())) {

            // Add message Flash
            $this->addFlash('warning', 'Vous ne pouvez ajouter de projet à ce groupe.');

            return $this->redirectToRoute('show_team', [
                'id' => $id
            ]);
        }

        // Get form data
        $form = $this->createForm(ProjectTeamType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Set Creator and Leader
            $project->setCreatedBy($user);
            $project->setLeader($user);

            // Add Project in database
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'Le projet a bien été créé.');

            return $this->redirectToRoute('show_team', [
                'id' => $team->getId()
            ]);
        }

        return $this->render('project/form.html.twig', [
            'team' => $team,
            'form' => $form->createView()
        ]);
    }
}
