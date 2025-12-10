<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Find all bookings for listings owned by a specific user
     */
    public function findBookingsByOwner(User $owner): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.listingId', 'l')
            ->where('l.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('b.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
