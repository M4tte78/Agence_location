<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public function findBySearch(string $search): array
    {
        $qb = $this->createQueryBuilder('v');
        if (!empty($search)) {
            $qb->andWhere('v.brand LIKE :search OR v.model LIKE :search OR v.type LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        return $qb->getQuery()->getResult();
    }
}
