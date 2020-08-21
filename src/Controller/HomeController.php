<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\TeamRepository;
use App\Repository\TimerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $userRepository;

    private $teamRepository;

    private $projectRepository;

    private $timerRepository;

    public function __construct
    (
        UserRepository $userRepository,
        TeamRepository $teamRepository,
        ProjectRepository $projectRepository,
        TimerRepository $timerRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;
        $this->projectRepository = $projectRepository;
        $this->timerRepository = $timerRepository;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index()
    {
        $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

        $count_teams = $user->getTeams()->count();

        $last_team = $this->teamRepository->findOneBy(['createdBy' => $user->getId()], ['createdAt' => 'DESC']);

        $projects = [];

        foreach ($user->getTeams() as $team) {
            foreach ($team->getProjects() as $project) {
                array_push($projects, $project);
            }
        }

        $count_projects = count($projects);

        $last_timer = $this->timerRepository->findOneBy(['user' => $user->getId()], ['createdAt', 'DESC']);

        $last_project = $last_timer->getProject();

        $count_timers = null;

        foreach ($user->getTimers() as $timer) {
            $count_timers += $timer->getTime();
        }


        return $this->render('home/index.html.twig', [
            'count_teams' => $count_teams,
            'last_team' => $last_team,
        ]);
    }
}
