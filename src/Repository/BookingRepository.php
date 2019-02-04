<?php

namespace App\Repository;

use App\Entity\Ad;
use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /*public function canBeBooked(Ad $ad, \DateTime $startDate, \DateTime $endDate)
    {
        $overlappingBookings = $this->createQueryBuilder('b')
            ->andWhere('b.ad = :ad')
            ->andWhere('b.startDate < :endDate')
            ->andWhere('b.endDate > :startDate')
            ->setParameter('ad', $ad)
            ->setParameter('endDate', $endDate)
            ->setParameter('startDate', $startDate)
            ->getQuery()
            ->getResult();

        return count($overlappingBookings) === 0;
    }*/

    // /**
    //  * @return Booking[] Returns an array of Booking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
