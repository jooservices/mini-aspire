<?php

namespace App\Repository;

use App\Entity\Wagers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Wagers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wagers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wagers[]    findAll()
 * @method Wagers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WagersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wagers::class);
    }

    /**
     * @param int $id
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function getItem(int $id)
    {
        return $this->createQueryBuilder('wagers')
            ->where('wagers.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SIMPLEOBJECT);
    }

    // /**
    //  * @return Wagers[] Returns an array of Wagers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Wagers
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
