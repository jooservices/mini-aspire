<?php

namespace App\Repository;

use App\Entity\Transactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Transactions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transactions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transactions[]    findAll()
 * @method Transactions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transactions::class);
    }

    /**
     * Searching transactions within today
     * @param int $loanId
     * @return int|mixed|string
     */
    public function getTransactionsByDay(int $loanId)
    {
        $now  = new \DateTime();
        $date = $now->format('Y-m-d');
        return $this->getEntityManager()->createQueryBuilder()
            ->select('transactions')
            ->from(Transactions::class, 'transactions')
            ->where('transactions.created >= :created')
            ->setParameter('created', $date . ' 00:00:00')
            ->andWhere('transactions.created <= :end')
            ->setParameter('end', $date . ' 23:59:59')
            ->andWhere('transactions.loan = :id')
            ->setParameter('id', $loanId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Searching transactions within this month
     * @param int $loanId
     * @return int|mixed|string
     */
    public function getTransactionsByMonth(int $loanId)
    {
        $now   = new \DateTime();
        $year  = $now->format('Y');
        $month = $now->format('m');

        return $this->getEntityManager()->createQueryBuilder()
            ->select('transactions')
            ->from(Transactions::class, 'transactions')
            ->where('transactions.created >= :created')
            ->setParameter('created', $year . '-' . $month . '-01' . ' 00:00:00')
            ->andWhere('transactions.created <= :end')
            ->setParameter('end',
                $year . '-' . $month . '-31' . ' 23:59:59') // Use 31 days for all month. Need improvement
            ->andWhere('transactions.loan = :id')
            ->setParameter('id', $loanId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Searching transaction within this year
     * @param int $loanId
     * @return int|mixed|string
     */
    public function getTransactionsByYear(int $loanId)
    {
        $now  = new \DateTime();
        $year = $now->format('Y');

        return $this->getEntityManager()->createQueryBuilder()
            ->select('transactions')
            ->from(Transactions::class, 'transactions')
            ->where('transactions.created >= :created')
            ->setParameter('created', $year . '-01-01' . ' 00:00:00')
            ->andWhere('transactions.created <= :end')
            ->setParameter('end',
                $year . '-12-31' . ' 23:59:59') // Use 31 days for all month. Need improvement
            ->andWhere('transactions.loan = :id')
            ->setParameter('id', $loanId)
            ->getQuery()
            ->getResult();
    }
}
