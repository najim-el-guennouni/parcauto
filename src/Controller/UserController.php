<?php

namespace App\Controller;

use LDAP\Result;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users')]
class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{id}/reservations', name: 'user_reservations', methods: ['GET'])]
    public function getUserReservations(int $id): JsonResponse
    {
        $reservations = $this->entityManager->getRepository(Reservation::class)->findBy(['user' => $id]);

        return $this->json($reservations);
    }
}
