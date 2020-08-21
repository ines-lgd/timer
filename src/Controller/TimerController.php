<?php

namespace App\Controller;

use App\Entity\Timer;
use App\Form\TimerType;
use App\Repository\ProjectRepository;
use App\Repository\TimerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TimerController extends AbstractController
{

    private $timerRepository;

    private $projectRepository;

    private $userRepository;

    private $entityManager;

    public function __construct
    (
        TimerRepository $timerRepository,
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->timerRepository = $timerRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/timers/{id}",name="list_timers")
     * @param int $id
     * @return Response
     */
    public function list(int $id)
    {
        // Get Timers Project
        $timers = $this->timerRepository->findBy(['project' => $id], ['createdAt' => 'DESC']);

        return $this->render('timer/index.html.twig', [
            'timers' => $timers,
            'project_id' => $id
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timers", name="list_user_timers")
     */
    public function list_user()
    {
        // Get User logged
        $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

        // Get Timer User logged
        $timers = $this->timerRepository->findBy(['user' => $user->getId()], ['createdAt' => 'DESC']);

        return $this->render('timer/index.html.twig', [
            'timers' => $timers
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timer/add/{id}", name="add_timer")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function create(Request $request, int $id)
    {
        // Create Timer and get Project and form data
        $timer = new Timer();
        $project = $this->projectRepository->find($id);

        $form = $this->createForm(TimerType::class, $timer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Get User logged
            $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

            // Set User and Project
            $project->setUpdatedAt();
            $timer->setUser($user);
            $timer->setProject($project);

            // Add Timer in database
            $this->entityManager->persist($timer);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'Le timer a bien été crée.');

            return $this->redirectToRoute('show_timer', [
                'id' => $timer->getId()
            ]);
        }

        return $this->render('timer/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timer/update/{id}", name="update_timer")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function update(Request $request, int $id) {

        // Get Timer
        $timer = $this->timerRepository->findOneBy(['id' => $id]);

        // Check if User logged is not User's Timer
        if ($timer->getUser()->getUsername() ==! $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas modifier ce timer.');

            return $this->redirectToRoute('list_timers');
        }

        // Get form data
        $form = $this->createForm(TimerType::class, $timer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Set update date Project
            $timer->getProject()->setUpdatedAt();

            // Update Timer in database
            $this->entityManager->persist($timer);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'Le timer a bien été modifié.');

            return $this->redirectToRoute('show_timer', [
                'id' => $id
            ]);
        }

        return $this->render('timer/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timer/remove/{id}", name="remove_timer")
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function remove(int $id) {

        // Get Timer
        $timer = $this->timerRepository->findOneBy(['id' => $id]);

        // Check if User logged is not User's Timer
        if ($timer->getUser()->getUsername() ==! $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer ce Timer.');

            return $this->redirectToRoute('list_timers');
        }

        // Set update date Project
        $project = $timer->getProject();
        $project->setUpdatedAt();

        // Remove Timer and update Project in database
        $this->entityManager->persist($project);
        $this->entityManager->remove($timer);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification', 'Le timer a bien été supprimé.');

        return $this->redirectToRoute('list_timers');
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timer/run/{id}", name="run_timer")
     * @param int $id
     * @return RedirectResponse
     */
    public function run(int $id) {

        // Get Timer in database
        $timer = $this->timerRepository->findOneBy(['id' => $id]);

        // Check if User logged is not User's Timer
        if ($timer->getUser()->getUsername() ==! $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas accéder a ce timer.');

            return $this->redirectToRoute('list_timers');
        }

        // Get Timer started
        $timer_started = $this->timerRepository->findOneBy(['status' => true, 'user' => $timer->getUser()->getId()]);

        if ($timer_started) {

            // Add message flash
            $this->addFlash('warning', 'Vous avez déjà un timer en cours. Arrêtez-le ou supprimez-le pour pouvoir en lancer un autre.');

            // Redirect to Timer started
            return $this->redirectToRoute('show_timer', [
               'id' => $timer_started->getId()
            ]);
        }

        // Run Timer and set update date Project
        $timer->run();

        // Update Timer in database
        $this->entityManager->persist($timer);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification','Timer lancé !');

        return $this->redirectToRoute('show_timer', [
            'id' => $timer->getId()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timer/stop/{id}", name="stop_timer")
     * @param int $id
     * @return RedirectResponse
     */
    public function stop(int $id) {

        // Get Timer in database
        $timer = $this->timerRepository->findOneBy(['id' => $id]);

        // Check if User logged is not User's Timer
        if ($timer->getUser()->getUsername() ==! $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas accéder a ce timer.');

            return $this->redirectToRoute('list_timers');
        }

        // Stop Timer and set update date Project
        $timer->stop();

        // Update Timer in database
        $this->entityManager->persist($timer);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification','Timer arrêté !');

        return $this->redirectToRoute('show_timer', [
            'id' => $timer->getId()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timer/reset/{id}", name="reset_timer")
     * @param int $id
     * @return RedirectResponse
     */
    public function reset(int $id) {

        // Get Timer in database
        $timer = $this->timerRepository->findOneBy(['id' => $id]);

        // Check if User logged is not User's Timer
        if ($timer->getUser()->getUsername() ==! $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas accéder a ce timer.');

            return $this->redirectToRoute('list_timers');
        }

        // Reset Timer and set update date Project
        $timer->reset();

        // Update Timer in database
        $this->entityManager->persist($timer);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification','Timer annulé !');

        return $this->redirectToRoute('show_timer', [
            'id' => $timer->getId()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timer/{id}", name="show_timer")
     * @param int $id
     * @return Response
     */
    public function show(int $id) {

        // Get Timer in database
        $timer = $this->timerRepository->findOneBy(['id' => $id]);

        if ($timer === null) {

            // Add message flash
            $this->addFlash('notification', 'Ce timer n’existe pas ou a été supprimé.');

            return $this->redirectToRoute('list_timers');
        }

        // Check if User logged is not User's Timer
        if ($timer->getUser()->getUsername() ==! $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas accéder a ce timer.');

            return $this->redirectToRoute('list_timers');
        }

        return $this->render('timer/show.html.twig', [
            'timer' => $timer
        ]);
    }
}
