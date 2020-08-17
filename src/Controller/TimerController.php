<?php

namespace App\Controller;

use App\Entity\Timer;
use App\Form\TimerType;
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

    private $userRepository;

    private $entityManager;

    public function __construct(TimerRepository $timerRepository, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->timerRepository = $timerRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timers", name="list_timer")
     */
    public function list()
    {
        // Get User logged
        $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

        return $this->render('timer/index.html.twig', [
            'timers' => $user->getTimers()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/timer/add", name="add_timer")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Create Timer and get form data
        $timer = new Timer();
        $form = $this->createForm(TimerType::class, $timer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Get User logged
            $user = $this->userRepository->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

            // Start Timer
            $timer->setUser($user);

            // Add Timer in database
            $this->entityManager->persist($timer);
            $this->entityManager->flush();

            // Add message flash
            $this->addFlash('notification', 'le Timer a bien été créer.');

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
            $this->addFlash('warning', 'Vous ne pouvez pas modifier ce Timer.');

            return $this->redirectToRoute('list_timer');
        }

        // Get form data
        $form = $this->createForm(TimerType::class, $timer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

            return $this->redirectToRoute('list_timer');
        }

        // Remove Timer in database
        $this->entityManager->remove($timer);
        $this->entityManager->flush();

        // Add message flash
        $this->addFlash('notification', 'Le timer a bien été supprimer.');

        return $this->redirectToRoute('list_timer');
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
            $this->addFlash('warning', 'Vous ne pouvez pas accéder a ce Timer.');

            return $this->redirectToRoute('list_timer');
        }

        // Run Timer
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
            $this->addFlash('warning', 'Vous ne pouvez pas accéder a ce Timer.');

            return $this->redirectToRoute('list_timer');
        }

        // Stop Timer
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
     * @Route("/timer/{id}", name="show_timer")
     * @param int $id
     * @return Response
     */
    public function show(int $id) {

        // Get Timer in database
        $timer = $this->timerRepository->findOneBy(['id' => $id]);

        // Check if User logged is not User's Timer
        if ($timer->getUser()->getUsername() ==! $this->getUser()->getUsername()) {

            // Add message flash
            $this->addFlash('warning', 'Vous ne pouvez pas accéder a ce Timer.');

            return $this->redirectToRoute('list_timer');
        }

        return $this->render('timer/show.html.twig', [
            'timer' => $timer
        ]);
    }

}
