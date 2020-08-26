<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Team;
use App\Entity\Timer;
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
        if ($this->getUser()) {

            $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

            $count_teams = $user->getTeams()->count();

            $last_team = $this->teamRepository->findOneBy(['createdBy' => $user->getId()], ['createdAt' => 'DESC']);

            if(!$last_team) {

                $last_team = new Team();
            }

            $projects = [];

            foreach ($user->getTeams() as $team) {

                foreach ($team->getProjects() as $project) {

                    array_push($projects, $project);
                }
            }

            $count_projects = count($projects);

            $last_timer = $this->timerRepository->findOneBy(['user' => $user->getId()], ['start' => 'DESC']);

            $count_timers = 0;

            if ($last_timer) {

                $last_project = $last_timer->getProject();

                foreach ($user->getTimers() as $timer) {

                    if ($timer->getTime() !== "") {

                        $count_timers += strtotime($timer->getTime());
                    }
                }

            } else {

                $last_project = new Project();
                $last_timer = new Timer();
                $count_timers = strtotime(0);
            }

            return $this->render('home/index.html.twig', [
                'count_teams' => $count_teams,
                'last_team' => $last_team,
                'count_projects' => $count_projects,
                'last_project' => $last_project,
                'count_timers' => $count_timers,
                'last_timer' => $last_timer
            ]);
        }

        return $this->render('home/index.html.twig');
    }
}
