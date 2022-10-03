<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PowerRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PowerController extends AbstractController
{
    public function __construct(
        protected PowerRepository        $powerRepository,
        protected SerializerInterface    $serializer,
        protected EntityManagerInterface $entityManager,
        protected UrlGeneratorInterface  $urlGenerator,
        protected ValidatorInterface     $validator,)
    {
    }

    //Show all powers, to be used in the select
    #[Route('/api/powers', name: 'powers', methods: ['GET'])]
    public function getAllPowers(Request $request): JsonResponse
    {
        $powersList = $this->powerRepository->findAll();

        $jsonPowersList = $this->serializer->serialize($powersList, 'json');

        return new JsonResponse($jsonPowersList, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
