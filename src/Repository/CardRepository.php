<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function myFindAllWithPaging1($currentPage, $nbPerPage, $session){

        $query = $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->leftJoin('a.theme', 'c')
            ->andWhere('c.id = '.$session)

            ->setFirstResult(($currentPage - 1) * $nbPerPage )
            ->setMaxResults($nbPerPage)
            ->getQuery();

        return new Paginator($query);
    }
    public function myFindAllWithPaging($currentPage, $nbPerPage, $session){

        $query = $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->leftJoin('a.theme', 'c')
            ->andWhere('c.id = '.$session)
            
            ->setFirstResult(($currentPage - 1) * $nbPerPage )
            ->setMaxResults($nbPerPage)
            ->getQuery();  
            
        return new Paginator($query);
    }
    
     /**
      * @return Card[] Returns an array of Card objects
      */
    
    public function findCard($id, $session)
    {



        $query =
            'SELECT * FROM card 
            WHERE card.id = '.$id.' AND card.theme_id = '.$session

        ;

        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }
   

    /*
    public function findOneBySomeField($value): ?Card
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
