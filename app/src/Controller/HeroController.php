<?php

namespace App\Controller;

use App\Entity\Hero;
use App\Repository\HeroRepository;
use App\Repository\PowerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HeroController extends AbstractController
{
    public function __construct(
        protected HeroRepository         $heroRepository,
        protected PowerRepository       $powerRepository,
        protected SerializerInterface    $serializer,
        protected EntityManagerInterface $entityManager,
        protected UrlGeneratorInterface  $urlGenerator,
        protected ValidatorInterface     $validator,
    )
    {}

    //Show all heroes in Json, with pagination
    #[Route('/api/heroes', name: 'heroes', methods: ['GET'])]
    //Gets parameters from url, ex: "/api/books?page=3&limit=2" will show page 3 of pagination and display only two results
    public function getAllHeroes(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit= $request->get('limit', 3);
        $heroesList = $this->heroRepository->findAllPagination($page, $limit);

        $jsonHeroesList = $this->serializer->serialize($heroesList, 'json');

        return new JsonResponse($jsonHeroesList, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //Show one hero in Json
    #[Route('/api/heroes/{id}', name: 'heroDetail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getHeroDetail(Hero $hero, SerializerInterface $serializer): JsonResponse
    {
        $jsonHero = $serializer->serialize($hero, 'json');
        return new JsonResponse($jsonHero, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    

    #[Route('/hero', name: 'app_hero')]
    public function index(): Response
    {
        return $this->render('hero/index.html.twig', [
            'controller_name' => 'HeroController',
        ]);
    }
}
