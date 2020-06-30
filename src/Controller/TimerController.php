<?php

namespace App\Controller;

use App\Entity\Timer;
use App\Repository\TimerRepository;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TimerController extends AbstractController
{

    
    private $userRepository;
    private $timerRepository;

    private $entityManager;

    public function __construct(UserRepository $userRepository,TimerRepository $timerRepository ,EntityManagerInterface $entityManager)
    {
        $this->userRepository  = $userRepository;
        $this->timerRepository = $timerRepository;
        $this->entityManager   = $entityManager;
    }

    /**
     * @Route("/timer", name="timer")
     */
    public function index()
    {
        return $this->render('timer/index.html.twig', [
            'controller_name' => 'TimerController',
        ]);
    }
    //
    public function addTime($id_project, $time, $type): Response
    {
        //get id_user by pseudo
        $pseudo = $this->getUser()->getUserName();
        $id_user = $this->userRepository->gettUserIdByPseudo($pseudo);
        
        //add time track 
        $timer = new Timer();
        $timer->setIdProject($id_project);
        $timer->setTimeUse($time);
        $timer->setType($type);
        $timer->setIdUser($id_user);
        
        $entityManager->persist($timer);
        $entityManager->flush();

        return new Response('Saved new product with id '.$product->getId());
    }


}
