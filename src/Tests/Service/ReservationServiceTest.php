<?php

namespace App\Tests\Service\Reservation;

use App\Entity\Car;
use App\Entity\Reservation;
use App\Service\Reservation\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use PHPUnit\Framework\TestCase;

class ReservationServiceTest extends TestCase
{
    public function testIsCarAvailableNoExistingReservations()
    {
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->createMock(Reservation::class)); // Mock repository

        $reservationService = new ReservationService($mockEntityManager);

        $car = new Car();
        $dateStart = strtotime('2024-05-10');
        $dateEnd = strtotime('2024-05-15');

        $this->assertTrue($reservationService->isCarAvailable($car, $dateStart, $dateEnd));
    }
}
