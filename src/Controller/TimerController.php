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

    public function addTime($id_project,$time , $type): Response
    {
        $pseudo = $this->getUser()->getUserName();
        $id_user = $this->userRepository->gettUserIdByPseudo($pseudo);
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $timer = new Timer();
        $timer->setIdProject($id_project);
        $timer->setTimeUse($time);
        $timer->setType($type);
        $timer->setIdUser($id_user);


        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$product->getId());
    }


}
