<?php

namespace App\Repository;

use App\Entity\UsersLoans;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UsersLoans|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersLoans|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersLoans[]    findAll()
 * @method UsersLoans[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersLoansRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersLoans::class);
    }

    // /**
    //  * @return UsersLoans[] Returns an array of UsersLoans objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsersLoans
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
