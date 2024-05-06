<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/cars')]
class CarController extends AbstractController
{
    #[Route('/', name: 'cars')]
    public function cars(SerializerInterface $serializer, CarRepository $carRepository): JsonResponse
    {
        $cars = $carRepository->findAll();
        $serializedCars = $serializer->serialize($cars, 'json');

        return new JsonResponse($serializedCars, 200, [], true);
    }

    #[Route('/{id}', name: 'car')]
    public function car(Car $car = null): JsonResponse
    {
        if (!$car) {
            return $this->json(['error' => 'Car not found'], 404);
        }

        return $this->json($car);
    }
}