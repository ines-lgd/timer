<?php

namespace App\Repository;

use App\Entity\Timer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Timer[] getTimeHistory()
 */
class TimerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timer::class);
    }

    /**
     * 
     * @property int $id_project
     * @return array Return array objet Timer[] [id,user name,time use , type (1 = auto)]
     */

    public function getTimeHistory ($id_project){

      $time_history = $this->createQueryBuilder('Select t.id ,u.pseudo ,t.time_use ,t.type FROM User.u Join Timer.t where t.id_user=u.id')
          ->andWhere('t.id_project = :id_project')
          ->setParameter('id_project', $id_project)
          ->orderBy('t.id', 'ASC')
          ->getQuery()
          ->getResult()
      ;

      return $time_history;

    }
    
    public function addTime($id_project,$time,$type,$id_user): String
    {

        $reponse= 'true';
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $timer = new Timer();
        $timer->setIdProject($id_project);
        $timer->setTimeUse($time);
        $timer->setType($type);
        $timer->setIdUser($id_user);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($timer);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $reponse;
    }



}
