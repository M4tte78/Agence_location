<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Vehicle; 
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function countReservationsByVehicle(Vehicle $vehicle): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.vehicle = :vehicle')
            ->setParameter('vehicle', $vehicle)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function hasReservationForToday(Vehicle $vehicle, \DateTimeImmutable $currentDate): bool
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.vehicle = :vehicle')
            ->andWhere('r.startDate <= :currentDate')
            ->andWhere('r.endDate >= :currentDate')
            ->setParameter('vehicle', $vehicle)
            ->setParameter('currentDate', $currentDate)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }




//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
