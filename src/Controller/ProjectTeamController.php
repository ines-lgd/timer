<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectTeamType;
use App\Repository\TeamRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectTeamController extends AbstractController
{

    private $teamRepository;

    private $userRepository;

    private $entityManager;
    private $projectRepository;

    public function __construct(TeamRepository $teamRepository, UserRepository $userRepository, ProjectRepository $projectRepository, EntityManagerInterface $entityManager)
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
        $team = $this->teamRepository->findOneBy(['id' => $id]);
        $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);
        $project = new Project();
        $project->setTeam($team);
        $project->setCreatedBy($user);
        $project->setLeader($user);

        // Get form data
        $form = $this->createForm(ProjectTeamType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/team/delete_project/{team_id}/{project_id}", name="delete_project_team")
     * @param int $team_id
     * @param int $project_id
     * @return RedirectResponse
     */
    public function removeProject(int $team_id, int $project_id)
    {
        // Get Team, User and User logged
        $team = $this->teamRepository->find($team_id);
        $project = $this->projectRepository->find($project_id);

        // Check if User is Creator Team
        if ($team->getCreatedBy()->getUsername() !== $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer ce projet.');

            return $this->redirectToRoute('show_team', [
                'id' => $team->getId()
            ]);
        }

        // Remove Project in Team and Update Team in database
        $team->removeProject($project);
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification', $project->getName() .' a été supprimé du groupe.');

        return $this->redirectToRoute('show_team', [
            'id' => $team_id
        ]);
    }

    /**
     * @param Team $team
     * @return Project[]
     */
    public function getProjects(Team $team) {

        // Get all Projects
        $projects = $this->projectRepository->findAll();

        // Filtre Projects list
        foreach ($projects as $key => $project) {

            // Remove Project already in Team
            if (in_array($project, $team->getProjects()->toArray())) {
                unset($projects[$key]);
            }
        }

        return $projects;
    }
}
