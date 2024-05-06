<?php
namespace App\Service\Reservation;

use App\Entity\Car;
use App\Entity\User;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isCarAvailable(Car $car, int $dateStart, int $dateEnd): bool
    {
        // Get reservations for the car within the specified date range
        $reservations = $this->entityManager->getRepository(Reservation::class)
            ->createQueryBuilder('r')
            ->where('r.car = :car')
            ->andWhere('r.dateEnd > :dateStart')
            ->andWhere('r.dateStart < :dateEnd')
            ->setParameter('car', $car)
            ->setParameter('dateStart', date('Y-m-d H:i:s', $dateStart))
            ->setParameter('dateEnd', date('Y-m-d H:i:s', $dateEnd))
            ->getQuery()
            ->getResult();

        // If there are no reservations, the car is available
        return empty($reservations);
    }



}
