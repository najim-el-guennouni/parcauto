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
    private SerializerInterface $serializer;
    private CarRepository $carRepository;

    public function __construct(SerializerInterface $serializer, CarRepository $carRepository)
    {
        $this->serializer = $serializer;
        $this->carRepository = $carRepository;
    }

    #[Route('/', name: 'cars', methods: ['GET'])]
    public function cars(): JsonResponse
    {
        $cars = $this->carRepository->findAll();
        $serializedCars = $this->serializer->serialize($cars, 'json');

        return new JsonResponse($serializedCars, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'car', methods: ['GET'])]
    public function car(int $id): JsonResponse
    {
        $car = $this->carRepository->find($id);
        
        if (!$car) {
            throw $this->createNotFoundException('Voiture non trouvée');
        }

        return $this->json($car);
    }
}
