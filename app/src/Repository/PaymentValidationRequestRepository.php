<?php

namespace App\Repository;

use App\Entity\PaymentValidationRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentValidationRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentValidationRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentValidationRequest[]    findAll()
 * @method PaymentValidationRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentValidationRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentValidationRequest::class);
    }

    // /**
    //  * @return PaymentValidationRequest[] Returns an array of PaymentValidationRequest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaymentValidationRequest
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
