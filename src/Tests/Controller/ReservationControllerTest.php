<?php

namespace App\Tests\Controller;

use App\Controller\ReservationController;
use App\Service\Reservation\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ReservationControllerTest extends TestCase
{
    public function testNewReservationSuccess()
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $reservationService = new ReservationService($entityManagerMock); // Create real service

        // Mock entity creation and persistence
        $entityManagerMock->expects($this->once())
            ->method('persist');
        $entityManagerMock->expects($this->once())
            ->method('flush');

        $reservationController = new ReservationController($reservationService, $entityManagerMock);

        $request = new Request([], [
            'datestart' => (new \DateTime())->format('Y-m-d'),
            'dateend' => (new \DateTime('+1 day'))->format('Y-m-d'),
            'user' => 1,
            'car' => 2,
        ]);

        $response = $reservationController->new($request);

        $this->assertJsonStringEqualsJsonString(
            json_encode('Réservation réussie.'),
            $response->getContent()
        );
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    
    
}
