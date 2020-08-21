<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectTeamType;
use App\Form\ProjectType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private $projectRepository;

    private $teamRepository;

    private $userRepository;

    private $entityManager;

    public function __construct(ProjectRepository $projectRepository, TeamRepository $teamRepository, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->projectRepository = $projectRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/projects", name="list_projects")
     * @return Response
     */
    public function list()
    {
        // Get all projects
        $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);
        $projects = [];

        foreach ($user->getTeams() as $team) {
            foreach ($team->getProjects() as $project) {
                array_push($projects, $project);
            }
        }

        return $this->render('project/index.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/project/add", name="add_project")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Create Project and get form data
        $project = new Project();

        // Add Creator and Team
        $creator = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);
        $project->setCreatedBy($creator);

        // Check User have Team
        if (empty($creator->getTeams()->toArray())) {

            // Add message flash
            $this->addFlash('notification', 'Vous n’avez aucun groupe.');

            return $this->redirectToRoute('add_team');
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Set Creator as Leader
            $project->setLeader($creator);

            // Add Project in database
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'Le projet a bien été créé.');

            return $this->redirectToRoute('show_project', [
                'id' => $project->getId()
            ]);
        }

        return $this->render('project/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/project/update/{id}", name="update_project")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {

        // Get Project and Team
        $project = $this->projectRepository->find($id);

        // Check if User is Leader
        if ($project->getLeader()->getUsername() !== $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas modifier ce projet.');

            return $this->redirectToRoute('show_project', ['id' => $project->getId()]);
        }

        // Get form data
        $form = $this->createForm(ProjectTeamType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Set time updatedAt
            $project->setUpdatedAt();

            // Update Project in database
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'Le projet a bien été modifié.');

            return $this->redirectToRoute('show_project', [
                'id' => $id
            ]);
        }

        return $this->render('project/form.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/project/remove/{id}", name="remove_project")
     * @param int $id
     * @return RedirectResponse
     */
    public function remove(int $id)
    {
        // Get Project and Leader
        $project = $this->projectRepository->find($id);

        // Check if User is Leader Team
        if ($project->getLeader()->getPseudo() !== $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer ce projet.');

            return $this->redirectToRoute('list_projects');
        }

        // Remove Project in database
        $this->entityManager->remove($project);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification', 'Le projet a bien été supprimé.');

        return $this->redirectToRoute('list_projects');
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/project/{id}", name="show_project")
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        // Get Project
        $project = $this->projectRepository->findOneBy(['id' => $id]);

        if ($project === null) {

            // Add message flash
            $this->addFlash('warning', 'Ce projet n’existe pas ou a été supprimé.');

            return $this->redirectToRoute('list_projects');
        }

        return $this->render('project/view.html.twig', [
            'project' => $project
        ]);
    }
}