<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Reservation\ReservationService;


#[Route('/api/reservations')]
class ReservationController extends AbstractController
{
    
    private $reservationService;
    private $entityManager;

    public function __construct(ReservationService $reservationService, EntityManagerInterface $entityManager)
    {
        $this->reservationService = $reservationService;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'new_reservation', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        // Fetch request parameters
        $dateStart = strtotime($request->request->get('datestart'));
        $dateEnd = strtotime($request->request->get('dateend'));
        $userId = $request->request->get('user');
        $carId = $request->request->get('car');

        // Check if dates are in proper order
        if ($dateStart >= $dateEnd) {
            return $this->json('Reservation failed: End date should be after start date.', 400);
        }

        // Fetch user and car entities
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $car = $this->entityManager->getRepository(Car::class)->find($carId);

        // Check if the car is available for the specified date range
        if (!$this->reservationService->isCarAvailable($car, $dateStart, $dateEnd)) {
            return $this->json('Car is not available for the specified date range.', 400);
        }

        $reservation = new Reservation();
        $reservation->setCar($car);
        $reservation->setUser($user);
        $reservation->setCreated(new \DateTime());
        $reservation->setDateStart(new \DateTime($request->request->get('datestart')));
        $reservation->setDateEnd(new \DateTime($request->request->get('dateend')));

        // Persist reservation entity
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
        return $this->json('Reservation successfully completed.', 200);

    }

    #[Route('/{id}', name: 'modify_reservation', methods: ['PUT'])]
    public function modifyReservation(Request $request, EntityManagerInterface $entityManager, Reservation $reservation): JsonResponse
    {
        // Get data from the request
        $dateStart = $request->query->get('datestart');
        $dateEnd = $request->query->get('dateend');
        $carId = $request->query->get('car');

        // Validate date range
        if ($dateStart >= $dateEnd) {
            return $this->json('Reservation failed: End date should be after start date.', 400);
        }

        // Update reservation data
        if ($dateStart) {
            $reservation->setDateStart(new \DateTime($dateStart));
        }
        if ($dateEnd) {
            $reservation->setDateEnd(new \DateTime($dateEnd));
        }

        // Update car
        if ($carId) {
            $car = $entityManager->getRepository(Car::class)->find($carId);
            if (!$car) {
                return $this->json('Car not found.', 404);
            }
            $reservation->setCar($car);
        }

        $entityManager->flush();

        return $this->json('Reservation modified successfully.');
    }

    #[Route('/{id}', name: 'cancel_reservation', methods: ['DELETE'])]
    public function cancelReservation(EntityManagerInterface $entityManager, Reservation $reservation): JsonResponse
    {
        $entityManager->remove($reservation);
        $entityManager->flush();

        return $this->json('Reservation canceled successfully.');
    }
}